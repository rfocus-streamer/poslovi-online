<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckOnlineStatus extends Command
{
    protected $signature = 'check:online-status';
    protected $description = 'Update user online status based on last activity';

    public function handle()
    {
        User::where('is_online', true)
            ->where('last_seen_at', '<', now()->subMinutes(5))
            ->update(['is_online' => false]);

        $this->info('Online status updated successfully.');
    }
}
