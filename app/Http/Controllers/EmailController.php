<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\EmailNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\ContactMail;
use Carbon\Carbon;

class EmailController extends Controller
{
    /**
     * Send daily emails from CSV list
     */
    public function sendDailyEmails()
    {
        //Log::info('Daily email process started at ' . now());

        $csvPath = storage_path('app/csv_lists.csv'); // Putanja do CSV fajla
        $dailyLimit = 20;
        $sentCount = 0;
        $errorCount = 0;
        $processedEmails = [];

        // Proveri da li CSV fajl postoji
        if (!file_exists($csvPath)) {
            Log::error("CSV file not found at path: {$csvPath}");
            return [
                'success' => false,
                'message' => 'CSV file not found',
                'sent' => 0,
                'errors' => 0,
                'timestamp' => now()
            ];
        }

        // Pročitaj CSV fajl
        $csvData = $this->readCSV($csvPath);
        if (empty($csvData)) {
            Log::warning('CSV file is empty');
            return [
                'success' => false,
                'message' => 'CSV file is empty',
                'sent' => 0,
                'errors' => 0,
                'timestamp' => now()
            ];
        }

        // Podrazumevani šablon i subject
        $template = 'csv_users'; // Promenite po potrebi
        $subject = 'Dnevni izveštaj - ' . config('app.name');
        $additionalMessage = 'Ovo je automatski generisani dnevni email.';

        $templatePath = 'admin.emails.templates.csv.' . $template;
        if (!view()->exists($templatePath)) {
            Log::error("Template '{$templatePath}' does not exist");
            return [
                'success' => false,
                'message' => "Šablon '{$templatePath}' ne postoji!",
                'sent' => 0,
                'errors' => 0,
                'timestamp' => now()
            ];
        }

        // Procesiraj CSV red po red
        foreach ($csvData as $index => $row) {
            if ($sentCount >= $dailyLimit) {
                break;
            }

            $email = $this->getEmailFromRow($row);

            // Preskoči ako nema emaila
            if (empty($email)) {
                continue;
            }

            // Proveri da li email već postoji u bazi korisnika
            if (User::where('email', $email)->exists()) {
                Log::info("Email {$email} already exists in users table, skipping...");
                $processedEmails[] = $email;
                continue;
            }

            // Proveri da li je email već obrađen u ovoj sesiji
            if (in_array($email, $processedEmails)) {
                continue;
            }

            try {
                $firstName = $this->getFirstNameFromRow($row);
                $lastName = $this->getLastNameFromRow($row);

                $details = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'message' => '',
                    'template' => $templatePath,
                    'subject' => 'Nova prilika za online zaradu',
                    'from_email' => config('mail.from.address'),
                    'from' => 'Poslovi Online',
                    'unreadMessages' => true
                ];

                // Pošalji email
                Mail::to($email)->send(new ContactMail($details));
                $sentCount++;
                $processedEmails[] = $email;

                //Log::info("Daily email sent to: {$email}");

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send email to {$email}: " . $e->getMessage());
            }
        }

        // Obriši poslate emailove iz CSV fajla
        if (!empty($processedEmails)) {
            $this->removeEmailsFromCSV($csvPath, $processedEmails);
            //Log::info("Removed " . count($processedEmails) . " emails from CSV file");
        }

        $result = [
            'success' => true,
            'total_processed' => count($processedEmails),
            'sent' => $sentCount,
            'errors' => $errorCount,
            'remaining_in_csv' => count($csvData) - count($processedEmails),
            'timestamp' => now()
        ];

        //Log::info('Daily email process completed', $result);

        //pozivanje funkcije za neaktivne korisnike (slanje emaila)
        $this->sendInactivityReminder30d();
        //pozivanje funkcije za postavljenje gigova clanova koji imaju pretplatu
        $this->sendGigReminder7d();
        //pozivanje funkcije za uplatu pretplate
        $this->sendRegistrationReminder24h();
        //pozivanje funkcije za obnovu pretplate
        $this->sendSubscriptionExpiredReminder();
        //pozivanje promo funkcije za pretplatu
        $this->sendPromoPackageToNewUsers();
        return $result;
    }

    /**
     * Read CSV file and return data as array
     */
    private function readCSV($filePath)
    {
        $data = [];
        $header = null;

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // Preskoči BOM ako postoji
            $bom = fread($handle, 3);
            if ($bom != "\xEF\xBB\xBF") {
                rewind($handle);
            }

            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$header) {
                    $header = $row;
                    continue;
                }

                if (count($row) === count($header)) {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Extract email from CSV row
     */
    private function getEmailFromRow($row)
    {
        // Proveri različite moguće kolone za email
        $emailColumns = ['Email 1', 'email', 'Email', 'Email Address'];

        foreach ($emailColumns as $column) {
            if (isset($row[$column]) && !empty(trim($row[$column]))) {
                return trim($row[$column]);
            }
        }

        return null;
    }

    /**
     * Extract first name from CSV row
     */
    private function getFirstNameFromRow($row)
    {
        $firstNameColumns = ['First Name', 'first_name', 'FirstName', 'Ime'];

        foreach ($firstNameColumns as $column) {
            if (isset($row[$column]) && !empty(trim($row[$column]))) {
                return trim($row[$column]);
            }
        }

        return '';
    }

    /**
     * Extract last name from CSV row
     */
    private function getLastNameFromRow($row)
    {
        $lastNameColumns = ['Last Name', 'last_name', 'LastName', 'Prezime'];

        foreach ($lastNameColumns as $column) {
            if (isset($row[$column]) && !empty(trim($row[$column]))) {
                return trim($row[$column]);
            }
        }

        return '';
    }

    /**
     * Remove processed emails from CSV file
     */
    private function removeEmailsFromCSV($filePath, $emailsToRemove)
    {
        $data = $this->readCSV($filePath);
        $emailsToRemove = array_map('strtolower', $emailsToRemove);

        // Filtriraj redove - ukloni one sa emailovima koji su poslati
        $filteredData = array_filter($data, function($row) use ($emailsToRemove) {
            $email = $this->getEmailFromRow($row);
            return !in_array(strtolower($email), $emailsToRemove);
        });

        // Zapiši nazad u CSV
        if (($handle = fopen($filePath, 'w')) !== FALSE) {
            // Zapiši header
            if (!empty($filteredData)) {
                fputcsv($handle, array_keys(reset($filteredData)));

                // Zapiši redove
                foreach ($filteredData as $row) {
                    fputcsv($handle, $row);
                }
            }
            fclose($handle);
        }
    }

    /**
     * Get CSV statistics
     */
    public function getCSVStats()
    {
        $csvPath = storage_path('app/csv_lists.csv');

        if (!file_exists($csvPath)) {
            return [
                'total_emails' => 0,
                'file_exists' => false
            ];
        }

        $data = $this->readCSV($csvPath);
        $emails = [];

        foreach ($data as $row) {
            $email = $this->getEmailFromRow($row);
            if ($email && !in_array($email, $emails)) {
                $emails[] = $email;
            }
        }

        return [
            'total_emails' => count($emails),
            'file_exists' => true,
            'emails' => $emails
        ];
    }

     /**
     * Mejlovi za neaktivne korisnike (30 dana)
     */
    private function sendInactivityReminder30d()
    {
        $type = 'inactivity_30d';
        $dailyLimit = 50;
        $template = 'inactive_users';
        $days = 30;
        $sentCount = 0;
        $errorCount = 0;

        $templatePath = 'admin.emails.templates.inactive.' . $template;
        if (!view()->exists($templatePath)) {
            Log::error("Template '{$templatePath}' does not exist");
            return ['success' => false, 'sent' => 0, 'errors' => 0];
        }

        $inactiveThreshold = Carbon::now()->subDays($days);

        $users = User::where(function($query) use ($inactiveThreshold) {
                $query->whereNull('last_seen_at')
                      ->orWhere('last_seen_at', '<', $inactiveThreshold);
            })
            ->get();

        foreach ($users as $user) {
            if ($this->shouldSkipNotification($user->id, $type, $days * 24)) {
                continue;
            }

            try {
                $details = [
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                    'email' => $user->email,
                    'message' => '',
                    'template' => $templatePath,
                    'subject' => 'Vratite se - vaš nalog vas čeka',
                    'from_email' => config('mail.from.address'),
                    'from' => config('app.name'),
                    'inactive_days' => $days,
                    'unreadMessages' => true,
                ];

                if($sentCount <= $dailyLimit){
                    Mail::to($user->email)->send(new ContactMail($details));
                }

                $sentCount++;
                $this->recordNotification($user->id, $type);

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send {$type} to {$user->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'sent' => $sentCount,
            'errors' => $errorCount,
            'type' => $type,
            'days_threshold' => $days
        ];
    }

    /**
     * Mejlovi za one koji nisu postavili gig nakon 7 dana
     */
    private function sendGigReminder7d()
    {
        $type = 'gig_reminder_7d';
        $dailyLimit = 50;
        $template = 'unused_subscriptions';
        $days = 7;
        $sentCount = 0;
        $errorCount = 0;

        $templatePath = 'admin.emails.templates.subscriptions.' . $template;
        if (!view()->exists($templatePath)) {
            Log::error("Template '{$templatePath}' does not exist");
            return ['success' => false, 'sent' => 0, 'errors' => 0];
        }

        $gigThreshold = Carbon::now()->subDays($days);

        // Get users with active subscriptions but no services
        $users = User::whereHas('subscriptions', function($query) {
            $query->where('status', 'active');
        })->whereDoesntHave('services')->get();

        foreach ($users as $user) {
            // Proveri da li je već poslat email
            if ($this->shouldSkipNotification($user->id, $type, $days * 24)) {
                continue;
            }

            // Proveri da li korisnik već ima postavljen gig koristeći Service model
            $hasService = Service::where('user_id', $user->id)->exists();

            // Ako korisnik već ima servis, preskoči slanje
            if ($hasService) {
                continue;
            }

            try {
                $details = [
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                    'message' => '',
                    'email' => $user->email,
                    'template' => $templatePath,
                    'subject' => 'Još uvek niste postavili ponudu',
                    'from_email' => config('mail.from.address'),
                    'from' => config('app.name'),
                    'days' => $days,
                    'unreadMessages' => true,
                ];

                if($sentCount <= $dailyLimit){
                    Mail::to($user->email)->send(new ContactMail($details));
                }

                $sentCount++;
                $this->recordNotification($user->id, $type);

                //Log::info("Gig reminder sent to: {$user->email}");

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send {$type} to {$user->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'sent' => $sentCount,
            'errors' => $errorCount,
            'type' => $type,
            'days_threshold' => $days,
            'users_checked' => $users->count()
        ];
    }

    /**
     * Mejlovi za one koji su se registrovali, ali nisu platili (24h)
     */
    private function sendRegistrationReminder24h()
    {
        $type = 'registration_24h';
        $dailyLimit = 50;
        $template = 'registration_reminder_24h';
        $hours = 24;
        $sentCount = 0;
        $errorCount = 0;

        $templatePath = 'admin.emails.templates.reminders.' . $template;
        if (!view()->exists($templatePath)) {
            Log::error("Template '{$templatePath}' does not exist");
            return ['success' => false, 'sent' => 0, 'errors' => 0];
        }

        $registrationThreshold = Carbon::now()->subHours($hours);

        $users = User::whereDoesntHave('subscriptions')  // Korisnici koji nemaju nijednu pretplatu
             ->whereDoesntHave('services')     // Korisnici koji nemaju usluge
             ->get();

        foreach ($users as $user) {
            $lastNotification = EmailNotification::where('user_id', $user->id)
                ->where('type', $type)
                ->first();
            if ($lastNotification) {
                continue;
            }

            try {
                $details = [
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                    'message' => '',
                    'email' => $user->email,
                    'template' => $templatePath,
                    'subject' => 'Postavite svoju ponudu već danas!',
                    'from_email' => config('mail.from.address'),
                    'from' => config('app.name'),
                    'hours' => $hours,
                    'unreadMessages' => true,
                ];

                if($sentCount <= $dailyLimit){
                    Mail::to($user->email)->send(new ContactMail($details));
                }

                $sentCount++;
                $this->recordNotification($user->id, $type);

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send {$type} to {$user->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'sent' => $sentCount,
            'errors' => $errorCount,
            'type' => $type,
            'hours_threshold' => $hours
        ];
    }

    /**
     * Mejlovi za članove kojima je istekla pretplata
     */
    private function sendSubscriptionExpiredReminder()
    {
        $type = 'subscription_expired';
        $dailyLimit = 50;
        $template = 'subscription_expired';
        $sentCount = 0;
        $errorCount = 0;

        $templatePath = 'admin.emails.templates.reminders.' . $template;
        if (!view()->exists($templatePath)) {
            Log::error("Template '{$templatePath}' does not exist");
            return ['success' => false, 'sent' => 0, 'errors' => 0];
        }

        // Pronađi korisnike kojima je istekla pretplata
        $users = User::whereNotNull('package_id') // Imali su pretplatu
            ->whereNotNull('package_expires_at') // Imao je datum isteka
            ->where('package_expires_at', '<', now()->subDays(2)) // Pretplata je istekla pre dva dana
            ->get();

        foreach ($users as $user) {
            // Proveri da li je već poslat email u poslednjih 30 dana
            if ($this->shouldSkipNotification($user->id, $type, 24 * 30)) { // 30 dana
                continue;
            }

            try {
                $details = [
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                    'message' => '',
                    'email' => $user->email,
                    'template' => $templatePath,
                    'subject' => 'Vaša pretplata je istekla',
                    'from_email' => config('mail.from.address'),
                    'from' => config('app.name'),
                    'expired_date' => $user->package_expires_at,
                    'days_since_expired' => $user->package_expires_at,
                    'unreadMessages' => true,
                ];

                if($sentCount <= $dailyLimit){
                    Mail::to($user->email)->send(new ContactMail($details));
                }
                $sentCount++;
                $this->recordNotification($user->id, $type);

                //Log::info("Subscription expired reminder sent to: {$user->email}");

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send {$type} to {$user->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'sent' => $sentCount,
            'errors' => $errorCount,
            'type' => $type,
            'users_checked' => $users->count()
        ];
    }


    /**
     * Mejlovi za korisnike koji nisu imali paket i registrovani su pre više od 4 dana, koji dobijaju promo paket
     */
    private function sendPromoPackageToNewUsers()
    {
        $type = 'new_user_promo_package';
        $dailyLimit = 50;
        $template = 'new_user_promo_package'; // Novi template za promo paket
        $sentCount = 0;
        $errorCount = 0;

        $templatePath = 'admin.emails.templates.reminders.' . $template;
        if (!view()->exists($templatePath)) {
            Log::error("Template '{$templatePath}' does not exist");
            return ['success' => false, 'sent' => 0, 'errors' => 0];
        }

        // Pronađi korisnike koji nisu imali paket i registrovani su pre više od 4 dana
        $users = User::whereNull('package_id') // Nisu imali paket
            ->where('created_at', '<', now()->subDays(4)) // Registrovani su pre više od 4 dana
            ->get();

        foreach ($users as $user) {
            // Proveri da li je već poslat email u poslednjih 30 dana
            if ($this->shouldSkipNotification($user->id, $type, 24 * 30)) { // 30 dana
                continue;
            }

            try {
                // Ažuriraj package_id na 8 (promo), koji predstavlja promo paket
                $user->package_id = 8;
                $user->save();

                // Priprema podataka za slanje emaila
                $details = [
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                    'message' => 'Čestitamo! Kao naš novi korisnik, dobili ste promo paket.',
                    'email' => $user->email,
                    'template' => $templatePath,
                    'subject' => 'Dobili ste promo paket!',
                    'from_email' => config('mail.from.address'),
                    'from' => config('app.name'),
                    'expired_date' => now(), // Ovdje možemo postaviti trenutno vreme jer nemaju još nikakvu pretplatu
                    'days_since_expired' => 0, // Jer nisu imali paket pre
                    'unreadMessages' => true,
                ];

                // Ako nije prešao dnevni limit, šaljemo email
                if($sentCount <= $dailyLimit){
                    Mail::to($user->email)->send(new ContactMail($details));
                }

                $sentCount++;
                $this->recordNotification($user->id, $type); // Zabeleži notifikaciju

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send {$type} to {$user->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'sent' => $sentCount,
            'errors' => $errorCount,
            'type' => $type,
            'users_checked' => $users->count()
        ];
    }

     /**
     * Provera da li treba preskočiti slanje notifikacije
     */
    private function shouldSkipNotification($userId, $type, $hoursThreshold)
    {
        $lastNotification = EmailNotification::where('user_id', $userId)
            ->where('type', $type)
            ->where('last_sent_at', '>=', Carbon::now()->subHours($hoursThreshold))
            ->first();

        return $lastNotification !== null;
    }

    /**
     * Beleženje slanja notifikacije
     */
    private function recordNotification($userId, $type)
    {
        EmailNotification::updateOrCreate(
            [
                'user_id' => $userId,
                'type' => $type
            ],
            [
                'last_sent_at' => now(),
                'sent_count' => \DB::raw('sent_count + 1')
            ]
        );
    }


    /**
     * Dodatna metoda za specifične tipove emailova
     */
    public function sendSubscriptionReminders()
    {
        // Ostavljamo prazno za sada
    }
}
