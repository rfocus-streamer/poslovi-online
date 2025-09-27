<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

                Log::info("Daily email sent to: {$email}");

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to send email to {$email}: " . $e->getMessage());
            }
        }

        // Obriši poslate emailove iz CSV fajla
        if (!empty($processedEmails)) {
            $this->removeEmailsFromCSV($csvPath, $processedEmails);
            Log::info("Removed " . count($processedEmails) . " emails from CSV file");
        }

        $result = [
            'success' => true,
            'total_processed' => count($processedEmails),
            'sent' => $sentCount,
            'errors' => $errorCount,
            'remaining_in_csv' => count($csvData) - count($processedEmails),
            'timestamp' => now()
        ];

        Log::info('Daily email process completed', $result);

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
     * Dodatna metoda za specifične tipove emailova
     */
    public function sendSubscriptionReminders()
    {
        // Ostavljamo prazno za sada
    }
}
