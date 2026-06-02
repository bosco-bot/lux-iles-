<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPrivilegeClubTierChangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $oldTier,
        public ?string $newTier
    ) {}

    public function handle(EmailService $emailService): void
    {
        try {
            $emailService->sendPrivilegeClubTierChange($this->user, $this->oldTier, $this->newTier);
            Log::info('Email Privilege Club envoyé', ['user_id' => $this->user->id, 'new_tier' => $this->newTier]);
        } catch (\Exception $e) {
            Log::error('Erreur email Privilege Club: '.$e->getMessage());
            throw $e;
        }

        try {
            $emailService->sendPrivilegeClubAdminAlert($this->user, $this->oldTier, $this->newTier);
            Log::info('Alerte admins Privilege Club envoyée', ['user_id' => $this->user->id, 'new_tier' => $this->newTier]);
        } catch (\Exception $e) {
            // Ne pas échouer le job si l'alerte interne admin échoue.
            Log::error('Erreur alerte admins Privilege Club: '.$e->getMessage());
        }
    }
}
