<?php

namespace Tests\Feature;

use App\Models\PrivilegeClubNotification;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PrivilegeClubWhatsappChecklistTest extends TestCase
{
    protected User $admin;

    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('session.driver', 'array');

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->withoutMiddleware(ValidateCsrfToken::class);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->string('privilege_tier', 20)->nullable();
            $table->boolean('privilege_tier_manual_override')->default(false);
            $table->timestamps();
        });

        Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('island_id')->constrained('islands');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('villa_id')->constrained('villas');
            $table->foreignId('user_id')->constrained('users');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('status')->default('confirmed');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('recipient_id')->constrained('users');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('villa_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });

        Schema::create('privilege_club_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type', 40);
            $table->string('old_tier', 20)->nullable();
            $table->string('new_tier', 20)->nullable();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamps();
        });

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Lux',
            'email' => 'admin@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $this->client = User::create([
            'first_name' => 'Client',
            'last_name' => 'VIP',
            'email' => 'client@test.luxiles.fr',
            'phone' => '+33601020304',
            'password' => Hash::make('password'),
            'privilege_tier' => 'signature',
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('privilege_club_notifications');
        Schema::dropIfExists('villa_reviews');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('client_documents');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function admin_sees_pending_whatsapp_on_client_profile(): void
    {
        PrivilegeClubNotification::create([
            'user_id' => $this->client->id,
            'type' => PrivilegeClubNotification::TYPE_TIER_UP,
            'old_tier' => 'insider',
            'new_tier' => 'signature',
            'message' => 'Félicitations ! Vous accédez au niveau SIGNATURE.',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.clients.show', $this->client))
            ->assertOk()
            ->assertSee('WhatsApp en attente')
            ->assertSee('Marquer WhatsApp envoyé')
            ->assertSee('+33601020304');
    }

    #[Test]
    public function admin_can_mark_whatsapp_as_sent(): void
    {
        $notification = PrivilegeClubNotification::create([
            'user_id' => $this->client->id,
            'type' => PrivilegeClubNotification::TYPE_TIER_UP,
            'old_tier' => 'insider',
            'new_tier' => 'signature',
            'message' => 'Félicitations !',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.clients.privilege-club.whatsapp-sent', [$this->client, $notification]))
            ->assertRedirect();

        $notification->refresh();
        $this->assertNotNull($notification->whatsapp_sent_at);
    }

    #[Test]
    public function cannot_mark_whatsapp_for_another_clients_notification(): void
    {
        $other = User::create([
            'first_name' => 'Autre',
            'last_name' => 'Client',
            'email' => 'autre@test.luxiles.fr',
            'password' => Hash::make('password'),
        ]);

        $notification = PrivilegeClubNotification::create([
            'user_id' => $other->id,
            'type' => PrivilegeClubNotification::TYPE_TIER_UP,
            'old_tier' => null,
            'new_tier' => 'insider',
            'message' => 'Test',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.clients.privilege-club.whatsapp-sent', [$this->client, $notification]))
            ->assertNotFound();
    }
}
