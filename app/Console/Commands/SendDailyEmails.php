<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Log;

class SendDailyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily emails at 10:30 AM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily email sending process...');

        try {
            // Pozovite EmailController metodu
            $emailController = app(EmailController::class);
            $result = $emailController->sendDailyEmails();

            $this->info('Daily emails sent successfully!');
            //Log::info('Daily emails sent successfully at ' . now());

        } catch (\Exception $e) {
            $this->error('Error sending daily emails: ' . $e->getMessage());
            Log::error('Error sending daily emails: ' . $e->getMessage());
        }
    }
}
