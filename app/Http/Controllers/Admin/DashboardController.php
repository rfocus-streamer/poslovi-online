<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\ForcedService;
use App\Models\ServiceImage;
use App\Models\Project;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\Complaint;
use App\Models\ProjectFile;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\ContactMail;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Provera admin privilegija
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $activeTab = request()->input('tab', 'users');

        // KORISNICI =============================================================
        $usersSortColumn = $request->input('users_sort_column', 'id');
        $usersSortDirection = $request->input('users_sort_direction', 'asc');

        // Provera validnosti parametara za sortiranje
        $allowedUserColumns = ['id', 'firstname', 'lastname', 'email', 'created_at', 'last_seen_at'];
        if (!in_array($usersSortColumn, $allowedUserColumns)) {
            $usersSortColumn = 'id';
            $usersSortDirection = 'asc';
        }

        $usersQuery = User::orderBy($usersSortColumn, $usersSortDirection);

        if ($request->has('users_search') && !empty($request->users_search)) {
            $searchTerm = $request->users_search;
            $usersQuery->where(function($query) use ($searchTerm) {
                $query->where('firstname', 'like', "%{$searchTerm}%")
                      ->orWhere('lastname', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%")
                      ->orWhere('id', 'like', "%{$searchTerm}%");
            });
        }

        $users = $usersQuery->paginate(10, ['*'], 'page', $request->input('users_page', 1))
            ->setPageName('users_page')
            ->appends([
                'tab' => 'users',
                'users_search' => $request->users_search,
                'users_sort_column' => $usersSortColumn,
                'users_sort_direction' => $usersSortDirection
            ]);

            // PONUDE ================================================================
            $servicesQuery = Service::with('user');

            // Sortiranje
            $servicesSortColumn = $request->input('services_sort_column', 'id');
            $servicesSortDirection = $request->input('services_sort_direction', 'asc');

            $allowedServiceColumns = ['id', 'title', 'created_at', 'visible_expires_at', 'visible'];
            if (!in_array($servicesSortColumn, $allowedServiceColumns)) {
                $servicesSortColumn = 'id';
                $servicesSortDirection = 'asc';
            }

            $servicesQuery->orderBy($servicesSortColumn, $servicesSortDirection);

            // Filter po statusu
            if ($request->has('services_status')) {
                switch ($request->services_status) {
                    case 'active':
                        $servicesQuery->where('visible', true)
                                     ->where('visible_expires_at', '>', now());
                        break;
                    case 'inactive':
                        $servicesQuery->where(function($query) {
                            $query->where('visible', null)
                                  ->orWhereNull('visible')
                                  ->orWhere(function($q) {
                                      $q->where('visible', true)
                                        ->where(function($q2) {
                                            $q2->where('visible_expires_at', '<', now())
                                               ->orWhereNull('visible_expires_at');
                                        });
                                  });
                        });
                        break;
                    // 'all' ne treba nikakav dodatni where
                }
            }

            // Tekstualna pretraga
            if ($request->has('services_search') && !empty($request->services_search)) {
                $searchTerm = $request->services_search;
                $servicesQuery->where(function($query) use ($searchTerm) {
                    $query->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('id', 'like', "%{$searchTerm}%")
                          ->orWhereHas('user', function($q) use ($searchTerm) {
                              $q->where('firstname', 'like', "%{$searchTerm}%")
                                ->orWhere('lastname', 'like', "%{$searchTerm}%");
                          });
                });
            }

            $services = $servicesQuery->paginate(10, ['*'], 'page', $request->input('services_page', 1))
                ->setPageName('services_page')
                ->appends([
                    'tab' => 'services',
                    'services_search' => $request->services_search,
                    'services_status' => $request->services_status,
                    'services_sort_column' => $servicesSortColumn,
                    'services_sort_direction' => $servicesSortDirection
                ]);

        // PRETPLATE =============================================================
        $subscriptionsQuery = Subscription::with(['user', 'package']);

        // Sortiranje
        $subscriptionsSortColumn = $request->input('subscriptions_sort_column', 'id');
        $subscriptionsSortDirection = $request->input('subscriptions_sort_direction', 'asc');

        $allowedSubscriptionColumns = ['id', 'plan_id', 'amount', 'status', 'gateway', 'ends_at', 'created_at'];
        if (!in_array($subscriptionsSortColumn, $allowedSubscriptionColumns)) {
            $subscriptionsSortColumn = 'id';
            $subscriptionsSortDirection = 'asc';
        }

        $subscriptionsQuery->orderBy($subscriptionsSortColumn, $subscriptionsSortDirection);

        // Filter po statusu
        if ($request->has('subscriptions_status') && $request->subscriptions_status) {
            $subscriptionsQuery->where('status', $request->subscriptions_status);
        }

        // Tekstualna pretraga
        if ($request->has('subscriptions_search') && !empty($request->subscriptions_search)) {
            $searchTerm = $request->subscriptions_search;
            $subscriptionsQuery->where(function($query) use ($searchTerm) {
                $query->where('plan_id', 'like', "%{$searchTerm}%")
                      ->orWhere('gateway', 'like', "%{$searchTerm}%")
                      ->orWhere('subscription_id', 'like', "%{$searchTerm}%")
                      ->orWhereHas('user', function($q) use ($searchTerm) {
                          $q->where('firstname', 'like', "%{$searchTerm}%")
                            ->orWhere('lastname', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
                      });
            });
        }

        $subscriptions = $subscriptionsQuery->paginate(10, ['*'], 'page', $request->input('subscriptions_page', 1))
            ->setPageName('subscriptions_page')
            ->appends([
                'tab' => 'subscriptions',
                'subscriptions_search' => $request->subscriptions_search,
                'subscriptions_status' => $request->subscriptions_status,
                'subscriptions_sort_column' => $subscriptionsSortColumn,
                'subscriptions_sort_direction' => $subscriptionsSortDirection
            ]);

        // TRANSAKCIJE =========================================================
        $transactionsQuery = Transaction::with('user');

        // Sortiranje
        $transactionsSortColumn = $request->input('transactions_sort_column', 'id');
        $transactionsSortDirection = $request->input('transactions_sort_direction', 'desc');

        $allowedTransactionColumns = ['id', 'amount', 'user_id', 'payment_method', 'status', 'created_at'];
        if (!in_array($transactionsSortColumn, $allowedTransactionColumns)) {
            $transactionsSortColumn = 'id';
            $transactionsSortDirection = 'desc';
        }

        $transactionsQuery->orderBy($transactionsSortColumn, $transactionsSortDirection);

        // Filter po statusu
        if ($request->has('transactions_status') && $request->transactions_status) {
            $transactionsQuery->where('status', $request->transactions_status);
        }

        // Tekstualna pretraga
        if ($request->has('transactions_search') && !empty($request->transactions_search)) {
            $searchTerm = $request->transactions_search;
            $transactionsQuery->where(function($query) use ($searchTerm) {
                $query->where('transaction_id', 'like', "%{$searchTerm}%")
                      ->orWhere('payment_method', 'like', "%{$searchTerm}%")
                      ->orWhere('amount', 'like', "%{$searchTerm}%")
                      ->orWhereHas('user', function($q) use ($searchTerm) {
                          $q->where('firstname', 'like', "%{$searchTerm}%")
                            ->orWhere('lastname', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
                      });
            });
        }

        $transactions = $transactionsQuery->paginate(10, ['*'], 'page', $request->input('transactions_page', 1))
            ->setPageName('transactions_page')
            ->appends([
                'tab' => 'transactions',
                'transactions_search' => $request->transactions_search,
                'transactions_status' => $request->transactions_status,
                'transactions_sort_column' => $transactionsSortColumn,
                'transactions_sort_direction' => $transactionsSortDirection
            ]);


        // ISTAKNUTE PONUDE ======================================================
        $currentForcedServices = ForcedService::orderBy('priority')->pluck('service_id')->toArray();
        $allServices = Service::where('visible', true)
                         ->with('user')
                         ->orderBy('title')
                         ->get();


        // Email notification tab
        // Dohvati korisnike sa nepročitanim porukama
        $usersWithUnreadMessages = User::whereHas('unreadMessages')
            ->withCount(['unreadMessages as unread_messages_count'])
            ->get();

        // Dohvati korisnike sa nepročitanim odgovorima na tikete
        $usersWithUnreadTicketResponses = User::whereHas('tickets', function($query) {
                $query->whereHas('responses', function($q) {
                    $q->whereNull('read_at')
                      ->whereColumn('ticket_responses.user_id', '!=', 'tickets.user_id');
                });
            })->withCount(['tickets as unread_responses_count' => function($query) {
                $query->whereHas('responses', function($q) {
                    $q->whereNull('read_at')
                      ->whereColumn('ticket_responses.user_id', '!=', 'tickets.user_id');
                });
            }])->get();

        // Dohvati dostupne šablone
        $messageTemplates = $this->getEmailTemplates('messages.*');
        $ticketTemplates = $this->getEmailTemplates('tickets.*');
        $subscriptionTemplates = $this->getEmailTemplates('subscriptions.*');
        $inactiveTemplates = $this->getEmailTemplates('inactive.*');

         // Get users with active subscriptions but no services
        $usersWithSubscriptionsWithoutServices = User::whereHas('subscriptions', function($query) {
            $query->where('status', 'active');
        })->whereDoesntHave('services')->get();

        // Get inactive users (last seen > 30 days ago)
        $inactiveUsers = User::where('last_seen_at', '<', now()->subDays(30))->get();

        // PROJEKTI ==============================================================
        $projects = Project::orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $request->input('projects_page', 1))
            ->setPageName('projects_page')
            ->appends(['tab' => 'projects']);

        // PAKETI ================================================================
        $packages = Package::orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $request->input('packages_page', 1))
            ->setPageName('packages_page')
            ->appends(['tab' => 'packages']);

        // NEPOTREBNI FAJLOVI ===================================================
        $folderMap = [
            'attachments' => [Ticket::class, 'attachment'],
            'complaints' => [Complaint::class, 'attachment'],
            'project_files' => [ProjectFile::class, 'file_path'],
            'services' => [ServiceImage::class, 'image_path'],
            'user' => [User::class, 'avatar'],
            'response-attachments' => [TicketResponse::class, 'attachment'],
        ];

        $allFiles = [];
        $unusedFiles = [];

        foreach ($folderMap as $folder => [$model, $column]) {
            $files = Storage::disk('public')->files($folder);
            $dbValues = $model::pluck($column)->filter()->toArray();
            $usedFilenames = array_map('basename', $dbValues);

            foreach ($files as $filePath) {
                $allFiles[] = $filePath;
                $filename = basename($filePath);

                if (!in_array($filename, $usedFilenames)) {
                    $unusedFiles[] = $filePath;
                }
            }
        }

        return view('admin.dashboard', compact(
            'users',
            'services',
            'currentForcedServices',
            'allServices',
            'projects',
            'packages',
            'activeTab',
            'unusedFiles',
            'subscriptions',
            'transactions',
            'usersWithUnreadMessages',
            'usersWithUnreadTicketResponses',
            'usersWithSubscriptionsWithoutServices',
            'inactiveUsers',
            'messageTemplates',
            'ticketTemplates',
            'subscriptionTemplates',
            'inactiveTemplates'
        ));
    }

    // metoda za ažuriranje
    public function updateForcedServices(Request $request)
    {
        $request->validate([
            'forced_services' => 'array|max:3',
            'forced_services.*' => 'nullable|exists:services,id'
        ]);

        // Obriši postojeće
        ForcedService::truncate();

        // Dodaj nove sa prioritetom
        if ($request->filled('forced_services')) {
            foreach ($request->forced_services as $index => $serviceId) {
                if ($serviceId) {
                    ForcedService::create([
                        'service_id' => $serviceId,
                        'priority' => $index + 1
                    ]);
                }
            }
        }

        return back()->with('success', 'Istaknute ponude su uspešno ažurirane.');
    }

    public function transactionDetails(Transaction $transaction)
    {
        return response()->json([
            'user_name' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'payment_method' => $transaction->payment_method,
            'status' => $transaction->status,
            'status_color' => [
                'completed' => 'success',
                'pending' => 'warning',
                'failed' => 'danger'
            ][$transaction->status] ?? 'primary',
            'created_at' => $transaction->created_at->format('d.m.Y. H:i'),
            'transaction_id' => $transaction->transaction_id,
            'payload' => $transaction->payload
        ]);
    }

    public function sendMessageReminders(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'template' => 'required|string',
            'subject' => 'required|string',
            'additional_message' => 'nullable|string'
        ]);

        $users = User::whereIn('id', $request->users)->get();

        foreach ($users as $user) {

            $templatePath = 'admin.emails.templates.messages.' . $request->template;
            if (!view()->exists($templatePath)) {
                return back()->withErrors([
                    'template' => "Šablon '$templatePath' ne postoji!"
                ]);
            }

            $details = [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'message' =>  $request->additional_message,
                'template' => $templatePath,
                'subject' => $request->subject,
                //'from_email' => 'sektormediaofficial@gmail.com',
                'from_email' => config('mail.from.address'),
                'from' => 'Poslovi Online',
                'unreadMessages' => true
            ];

            try {
                Mail::to($user->email)->send(new ContactMail($details));
                //return 'Email poslan!';
            } catch (\Exception $e) {
                return 'Greška: ' . $e->getMessage();
            }
        }

        return back()->with('success', 'Podsjetnici su uspješno poslani ' . count($users) . ' korisnicima!');
    }

    public function sendTicketReminders(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'template' => 'required|string',
            'subject' => 'required|string',
            'additional_message' => 'nullable|string'
        ]);

        $users = User::whereIn('id', $request->users)->get();

        foreach ($users as $user) {
            $details = [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'message' => $request->additional_message,
                'template' => 'admin.emails.templates.tickets.' . $request->template,
                'subject' => $request->subject,
                'from_email' => config('mail.from.address'),
                'from' => 'Poslovi Online'
            ];

            Mail::to($user->email)->send(new ContactMail($details));
        }

        return back()->with('success', 'Podsjetnici su uspješno poslani ' . count($users) . ' korisnicima!');
    }

    private function getEmailTemplates($pattern)
    {
        $files = glob(resource_path('views/admin/emails/templates/' . str_replace('.', '/', $pattern) . '.blade.php'));
        $templates = [];

        foreach ($files as $file) {
            $templates[] = basename($file, '.blade.php');
        }

        return $templates;
    }

    public function sendSubscriptionReminders(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'template' => 'required|string',
            'subject' => 'required|string',
            'additional_message' => 'nullable|string'
        ]);

        $users = User::whereIn('id', $request->users)->get();

        foreach ($users as $user) {
            $templatePath = 'admin.emails.templates.subscriptions.' . $request->template;
            if (!view()->exists($templatePath)) {
                return back()->withErrors(['template' => "Šablon '$templatePath' ne postoji!"]);
            }

            $details = [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'message' => $request->additional_message,
                'template' => $templatePath,
                'subject' => $request->subject,
                'from_email' => config('mail.from.address'),
                'from' => config('mail.from.name'),
            ];

            Mail::to($user->email)->send(new ContactMail($details));
        }

        return back()->with('success', 'Podsetnici za pretplate poslati ' . count($users) . ' korisnicima!');
    }

    public function sendInactiveReminders(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'template' => 'required|string',
            'subject' => 'required|string',
            'additional_message' => 'nullable|string'
        ]);

        $users = User::whereIn('id', $request->users)->get();

        foreach ($users as $user) {
            $templatePath = 'admin.emails.templates.inactive.' . $request->template;
            if (!view()->exists($templatePath)) {
                return back()->withErrors(['template' => "Šablon '$templatePath' ne postoji!"]);
            }

            $details = [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'message' => $request->additional_message,
                'template' => $templatePath,
                'subject' => $request->subject,
                'from_email' => config('mail.from.address'),
                'from' => config('mail.from.name'),
            ];

            Mail::to($user->email)->send(new ContactMail($details));
        }

        return back()->with('success', 'Podsetnici za neaktivne korisnike poslati ' . count($users) . ' korisnicima!');
    }

    public function sendCustomEmail(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'subject' => 'required|string',
            'content' => 'required|string',
            'emails' => 'nullable|string'
        ]);

        $recipients = $request->recipients;
        $customEmails = $request->emails ? array_map('trim', explode(',', $request->emails)) : [];
        $users = collect();

        // Process recipient groups
        if (in_array('all', $recipients)) {
            $users = User::all();
        } else {
            if (in_array('active', $recipients)) {
                $users = $users->merge(User::where('last_seen_at', '>', now()->subDays(30))->get());
            }
            if (in_array('inactive', $recipients)) {
                $users = $users->merge(User::where('last_seen_at', '<', now()->subDays(30))->get());
            }
            if (in_array('premium', $recipients)) {
                $users = $users->merge(User::whereHas('subscriptions', function($q) {
                    $q->where('status', 'active');
                })->get());
            }
            if (in_array('free', $recipients)) {
                $users = $users->merge(User::whereDoesntHave('subscriptions')->get());
            }
        }

        // Add custom emails
        foreach ($customEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $users->push(new User(['email' => $email, 'firstname' => '', 'lastname' => '']));
            }
        }

        // Remove duplicates
        $users = $users->unique('email');

        foreach ($users as $user) {
            $details = [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'message' => $request->content,
                'template' => 'admin.emails.templates.custom',
                'subject' => $request->subject,
                'from_email' => config('mail.from.address'),
                'from' => config('mail.from.name'),
                'is_custom' => true
            ];

            Mail::to($user->email)->send(new ContactMail($details));
        }

        return back()->with('success', 'Prilagođeni email poslat ' . $users->count() . ' primaoca!');
    }

    public function profile(User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }
        return view('admin.users.profile', compact('user'));
    }

    public function deposit(User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        return view('admin.users.deposit', compact('user'));
    }

    public function depositAmount(Request $request, User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $depositAmount = $request->amount;
        $user->deposits += $depositAmount;
        $user->save();
        return redirect()->back()->with('success', 'Depozit za '.$user->firstname.' '.$user->lastname.' je uspešno dodat!')
                    ->withInput(); // Ovaj .withInput() omogućava da sačuvaš podatke forme nakon što se stranica učita ponovo
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(Request $request, User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        // Ažuriranje ostalih podataka
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->street = $request->street;
        $user->city = $request->city;
        $user->country = $request->country;

        // Čuvanje promena u bazi
        $user->save();

        // Vraćamo korisnika na isti tab (pretpostavljam da je tab u URL-u sa parametrom 'tab')
        return redirect()->back()->with('success', 'Profil uspešno ažuriran!')
                    ->withInput(); // Ovaj .withInput() omogućava da sačuvaš podatke forme nakon što se stranica učita ponovo
    }

    public function deleteFile(Request $request)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $file = $request->input('file_path');

        if (Storage::disk('public')->exists($file)) {
            Storage::disk('public')->delete($file);
            return back()->with('success', 'Fajl je uspešno obrisan.');
        }

        return back()->with('error', 'Fajl nije pronađen.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
