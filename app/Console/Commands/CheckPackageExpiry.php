<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPackageExpiry extends Command
{
    protected $signature = 'packages:check-expiry';
    protected $description = 'Automatski deaktivira istekle pakete korisnika';

    public function handle()
    {
        $expiredCount = User::whereNotNull('package_id')
                          ->where('package_expires_at', '<', now())
                          ->update([
                              'package_id' => null,
                              'package_expires_at' => null
                          ]);

        if ($expiredCount > 0) {
            $this->info("Deaktivirano {$expiredCount} isteklih paketa.");
            Log::info("Deaktivirano {$expiredCount} isteklih paketa.");
        } else {
            $this->info("Nema isteklih paketa za deaktivaciju.");
        }

        return 0;
    }
}
