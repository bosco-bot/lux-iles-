<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PrivilegeClubPageTest extends TestCase
{
    protected User $user;

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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->string('privilege_tier', 20)->nullable();
            $table->boolean('privilege_tier_manual_override')->default(false);
            $table->timestamp('privilege_tier_updated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->unsignedBigInteger('villa_id')->default(1);
            $table->foreignId('user_id')->constrained('users');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('status');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('recipient_id')->constrained('users');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('reservation_id')->nullable();
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

        $this->user = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Client',
            'email' => 'marie@test.luxiles.fr',
            'password' => Hash::make('password'),
            'privilege_tier' => 'insider',
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('privilege_club_notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function guest_cannot_access_privilege_club_page(): void
    {
        $this->get(route('espace-client.privilege-club'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_view_privilege_club_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('espace-client.privilege-club'))
            ->assertOk()
            ->assertSee('LUXÎLES PRIVILEGE CLUB')
            ->assertSee('INSIDER');
    }
}
