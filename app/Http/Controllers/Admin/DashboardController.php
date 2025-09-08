<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\FiatPayout;
use App\Models\ForcedService;
use App\Models\ServiceImage;
use App\Models\Project;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\Complaint;
use App\Models\ProjectFile;
use App\Models\Message;
use App\Models\Invoice;
use App\Models\PrivilegedCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Mail\ContactMail;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Balance;
use Stripe\Exception\ApiErrorException;

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

        // PRIVILEGOVANI PROCENTI ================================================
        $privilegedCommissionsSortColumn = $request->input('privileged_commissions_sort_column', 'id');
        $privilegedCommissionsSortDirection = $request->input('privileged_commissions_sort_direction', 'asc');

        // Provera validnosti parametara za sortiranje
        $allowedPrivilegedCommissionColumns = ['id', 'buyer_commission', 'seller_commission', 'created_at'];
        if (!in_array($privilegedCommissionsSortColumn, $allowedPrivilegedCommissionColumns)) {
            $privilegedCommissionsSortColumn = 'id';
            $privilegedCommissionsSortDirection = 'asc';
        }

        $privilegedCommissionsQuery = PrivilegedCommission::with('user')
            ->orderBy($privilegedCommissionsSortColumn, $privilegedCommissionsSortDirection);

        $privilegedCommissions = $privilegedCommissionsQuery->paginate(10, ['*'], 'page', $request->input('privileged_commissions_page', 1))
            ->setPageName('privileged_commissions_page')
            ->appends([
                'tab' => 'privileged_commissions',
                'privileged_commissions_sort_column' => $privilegedCommissionsSortColumn,
                'privileged_commissions_sort_direction' => $privilegedCommissionsSortDirection
            ]);

        // Search za privilegovane procente
        $searchedUsers = collect();
        if ($activeTab === 'privileged_commissions' && $request->has('user_search') && !empty($request->user_search)) {
            $searchTerm = $request->user_search;

            $searchedUsers = User::where(function($query) use ($searchTerm) {
                $query->where('firstname', 'like', "%{$searchTerm}%")
                    ->orWhere('lastname', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('id', 'like', "%{$searchTerm}%");
            })->whereNotIn('id', PrivilegedCommission::pluck('user_id')->toArray())
            ->limit(10)
            ->get();
        }

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

        // STRIPE TRANSACTIONS ======================================================
        $stripeFilters = [
            'status' => $request->input('stripe_status', ''),
            'transaction_id' => $request->input('stripe_transaction_id', ''),
            'customer_email' => $request->input('stripe_customer_email', ''),
            'subscription_id' => $request->input('stripe_subscription_id', ''),
            'from_date' => $request->input('stripe_from_date', ''),
            'to_date' => $request->input('stripe_to_date', ''),
        ];

        $stripePage = $request->input('stripe_page', 1);
        $stripePerPage = 10;
        $startingAfter = $request->input('starting_after', null);
        $endingBefore = $request->input('ending_before', null);

        // Određivanje tekućeg meseca i godine
        $currentMonth = $request->input('report_month', date('n'));
        $currentYear = $request->input('report_year', date('Y'));

        $stripeTransactions = null;
        $stripePagination = null;
        $stripeBalance = 0;

        $monthlyStripeReport = Array(
            'total_amount' => 0,
            'successful_charges' => 0,
            'failed_charges' => 0,
            'currency' => '',
            'transactions' => Array()
        );

        // Only fetch Stripe transactions if this is the active tab to avoid unnecessary API calls
        if ($activeTab === 'stripe_transactions') {
            $stripeTransactions = $this->getStripeTransactions($stripeFilters, $stripePerPage, $startingAfter, $endingBefore);
            $stripePagination = $this->formatStripePagination($stripeTransactions, $stripePage, $stripePerPage);
        }

        if ($activeTab === 'finances') {
            $stripeBalance = $this->getStripeBalance();
            $monthlyStripeReport = $this->getMonthlyTransactions($currentYear, $currentMonth);
        }



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


        // FIAT PAYOUTS ===========================================================
        $payoutsQuery = FiatPayout::with('user');

        // Sortiranje
        $payoutsSortColumn = $request->input('payouts_sort_column', 'id');
        $payoutsSortDirection = $request->input('payouts_sort_direction', 'desc');

        // Provera validnosti parametara za sortiranje
        $allowedPayoutColumns = ['id', 'user_id', 'amount', 'request_date', 'payed_date', 'status', 'payment_method'];
        if (!in_array($payoutsSortColumn, $allowedPayoutColumns)) {
            $payoutsSortColumn = 'id';
            $payoutsSortDirection = 'desc';
        }

        $payoutsQuery->orderBy($payoutsSortColumn, $payoutsSortDirection);

        // Filter po statusu
        if ($request->has('payouts_status') && $request->payouts_status) {
            $payoutsQuery->where('status', $request->payouts_status);
        }

        // Tekstualna pretraga
        if ($request->has('payouts_search') && !empty($request->payouts_search)) {
            $searchTerm = $request->payouts_search;
            $payoutsQuery->where(function($query) use ($searchTerm) {
                $query->where('id', 'like', "%{$searchTerm}%")
                      ->orWhere('payment_method', 'like', "%{$searchTerm}%")
                      ->orWhere('amount', 'like', "%{$searchTerm}%")
                      ->orWhereHas('user', function($q) use ($searchTerm) {
                          $q->where('firstname', 'like', "%{$searchTerm}%")
                             ->orWhere('lastname', 'like', "%{$searchTerm}%")
                             ->orWhere('email', 'like', "%{$searchTerm}%");
                      });
            });
        }

        $fiatPayouts = $payoutsQuery->paginate(10, ['*'], 'page', $request->input('payouts_page', 1))
            ->setPageName('payouts_page')
            ->appends([
                'tab' => 'fiatpayouts',
                'payouts_search' => $request->payouts_search,
                'payouts_status' => $request->payouts_status,
                'payouts_sort_column' => $payoutsSortColumn,
                'payouts_sort_direction' => $payoutsSortDirection
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
            'privilegedCommissions',
            'searchedUsers',
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
            'inactiveTemplates',
            'fiatPayouts',
            'stripeTransactions',
            'stripeFilters',
            'stripePagination',
            'stripeBalance',
            'monthlyStripeReport',
            'currentMonth',
            'currentYear'
        ));
    }

    // CRUD metode za Privileged Commission
    public function storePrivilegedCommission(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:privileged_commissions,user_id',
            'buyer_commission' => 'required|numeric|min:0|max:100',
            'seller_commission' => 'required|numeric|min:0|max:100'
        ]);

        PrivilegedCommission::create($request->only([
            'user_id', 'buyer_commission', 'seller_commission'
        ]));

        return redirect()->route('admin.dashboard', ['tab' => 'privileged_commissions'])
            ->with('success', 'Privilegovani procenti su uspešno dodati za korisnika.');
    }

    public function updatePrivilegedCommission(Request $request, PrivilegedCommission $privilegedCommission)
    {
        $request->validate([
            'buyer_commission' => 'required|numeric|min:0|max:100',
            'seller_commission' => 'required|numeric|min:0|max:100'
        ]);

        $privilegedCommission->update($request->only([
            'buyer_commission', 'seller_commission'
        ]));

        return redirect()->route('admin.dashboard', ['tab' => 'privileged_commissions'])
            ->with('success', 'Privilegovani procenti su uspešno ažurirani.');
    }

    public function destroyPrivilegedCommission(PrivilegedCommission $privilegedCommission)
    {
        $privilegedCommission->delete();

        return redirect()->route('admin.dashboard', ['tab' => 'privileged_commissions'])
            ->with('success', 'Privilegovani procenti su uspešno obrisani.');
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

    public function userInvoices(User $user)
    {
        $invoices = Invoice::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc') // Sortiraj od najnovijih ka najstarijima
                                ->paginate(50); // Paginacija sa 50 stavke po stranici

        return view('admin.users.user_invoices', compact('user', 'invoices'));
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

    private function getStripeBalance()
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $balance = Balance::retrieve();
            return $balance;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Balance API Error: '.$e->getMessage());
            return null;
        }
    }

    private function getMonthlyTransactions($year, $month)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Određivanje početka i kraja meseca
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->timestamp;
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->timestamp;

            // Dobijanje balance transakcija za period
            $balanceTransactions = \Stripe\BalanceTransaction::all([
                'limit' => 100,
                'created' => [
                    'gte' => $startDate,
                    'lte' => $endDate
                ],
                'type' => 'charge', // Samo transakcije vezane za naplate
                'expand' => ['data.source', 'data.source.customer']
            ]);

            $totalAmount = 0;
            $totalNetAmount = 0;
            $totalFees = 0;
            $successfulCharges = 0;

            foreach ($balanceTransactions->data as $transaction) {
                if ($transaction->type === 'charge' && $transaction->source->status === 'succeeded') {
                    $totalAmount += $transaction->amount;
                    $totalNetAmount += $transaction->net;
                    $totalFees += $transaction->fee;
                    $successfulCharges++;
                }
            }

            return [
                'total_amount' => $totalAmount / 100,
                'total_net_amount' => $totalNetAmount / 100,
                'total_fees' => $totalFees / 100,
                'successful_charges' => $successfulCharges,
                'failed_charges' => 0, // Balance transakcije ne uključuju neuspešne
                'currency' => 'eur'
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Monthly Transactions Error: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Dobija Stripe transakcije preko API-ja
     */
    private function getStripeTransactions($filters, $limit = 100, $startingAfter = null, $endingBefore = null)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // If subscription_id is specified, use a different approach
            if (!empty($filters['subscription_id'])) {
                return $this->getTransactionsBySubscription($filters['subscription_id'], $filters, $limit);
            }

            $params = [
                'limit' => $limit,
                'expand' => ['data.customer', 'data.payment_intent', 'data.invoice.subscription', 'data.invoice.lines']
            ];

            // Date filters are still applied via API
            if (!empty($filters['from_date'])) {
                $params['created']['gte'] = strtotime($filters['from_date']);
            }

            if (!empty($filters['to_date'])) {
                $params['created']['lte'] = strtotime($filters['to_date']);
            }

            // If customer email is specified, find the customer ID first
            if (!empty($filters['customer_email'])) {
                // Search for customers with this email
                try {
                    $customers = \Stripe\Customer::all([
                        'email' => $filters['customer_email'],
                        'limit' => 10
                    ]);

                    if (count($customers->data) > 0) {
                        // Get all customer IDs
                        $customerIds = array_map(function($customer) {
                            return $customer->id;
                        }, $customers->data);

                        // Filter by customer IDs instead of email
                        if (count($customerIds) === 1) {
                            $params['customer'] = $customerIds[0];
                        } else {
                            // For multiple customers, fetch all charges for each customer
                            $allCharges = [];
                            foreach ($customerIds as $customerId) {
                                $customerParams = $params;
                                $customerParams['customer'] = $customerId;

                                $customerCharges = Charge::all($customerParams);
                                $allCharges = array_merge($allCharges, $customerCharges->data);
                            }

                            // Now apply client-side status filter
                            if (!empty($filters['status'])) {
                                $allCharges = array_filter($allCharges, function($charge) use ($filters) {
                                    return $charge->status === $filters['status'];
                                });
                            }

                            // Create a filtered result
                            $filteredCharges = new \stdClass();
                            $filteredCharges->data = $allCharges;
                            $filteredCharges->has_more = false;
                            $filteredCharges->url = '';
                            $filteredCharges->object = 'list';

                            return $filteredCharges;
                        }
                    } else {
                        // No customers found with this email
                        $emptyResult = new \stdClass();
                        $emptyResult->data = [];
                        $emptyResult->has_more = false;
                        return $emptyResult;
                    }
                } catch (\Exception $e) {
                    Log::error('Error searching for customers: ' . $e->getMessage());
                    // Fall back to manual filtering
                }
            }

            // Pagination
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            if ($endingBefore) {
                $params['ending_before'] = $endingBefore;
            }

            // Get charges
            $charges = Charge::all($params);

            // Get the data array from the charges object
            $dataArray = $charges->data;

            // Client-side filtering by status
            if (!empty($filters['status'])) {
                $dataArray = array_filter($dataArray, function($charge) use ($filters) {
                    return $charge->status === $filters['status'];
                });
            }

            // Additional filtering by email if specified (if not already handled via customer ID)
            if (!empty($filters['customer_email']) && empty($params['customer'])) {
                $dataArray = array_filter($dataArray, function($charge) use ($filters) {
                    return isset($charge->customer->email) &&
                           stripos($charge->customer->email, $filters['customer_email']) !== false;
                });
            }

            // Additional filtering by transaction_id if specified
            if (!empty($filters['transaction_id'])) {
                $dataArray = array_filter($dataArray, function($charge) use ($filters) {
                    return stripos($charge->id, $filters['transaction_id']) !== false;
                });
            }

            // Reset array keys
            $dataArray = array_values($dataArray);

            // Create a new Stripe collection with the filtered data
            $filteredCharges = new \stdClass();
            $filteredCharges->data = $dataArray;
            $filteredCharges->has_more = $charges->has_more;
            $filteredCharges->url = $charges->url;
            $filteredCharges->object = $charges->object;

            return $filteredCharges;

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Dobija transakcije na osnovu subscription ID-a
     */
    private function getTransactionsBySubscription($subscriptionId, $filters, $limit = 10)
    {
        try {
            // Prvo dobijamo sve invoice-e za dati subscription
            $invoices = \Stripe\Invoice::all([
                'subscription' => $subscriptionId,
                'limit' => 100,
                'expand' => ['data.charge', 'data.customer', 'data.subscription', 'data.lines']
            ]);

            // Prikupijamo charge ID-eve iz invoice-a
            $chargeIds = [];
            foreach ($invoices->data as $invoice) {
                if (!empty($invoice->charge) && is_string($invoice->charge)) {
                    $chargeIds[] = $invoice->charge;
                } elseif (!empty($invoice->charge) && is_object($invoice->charge)) {
                    $chargeIds[] = $invoice->charge->id;
                }
            }

            if (empty($chargeIds)) {
                $emptyResult = new \stdClass();
                $emptyResult->data = [];
                $emptyResult->has_more = false;
                return $emptyResult;
            }

            // Sada dobijamo sve charge-eve odjednom
            $charges = Charge::all([
                'limit' => $limit,
                'ids' => array_slice($chargeIds, 0, $limit), // Ograničavamo na traženi limit
                'expand' => ['data.customer', 'data.payment_intent', 'data.invoice.subscription', 'data.invoice.lines']
            ]);

            // Dodatno filtriranje po statusu ako je specificiran
            if (!empty($filters['status'])) {
                $charges->data = array_filter($charges->data, function($charge) use ($filters) {
                    return $charge->status === $filters['status'];
                });

                // Resetujemo ključeve niza
                $charges->data = array_values($charges->data);
            }

            // Dodatno filtriranje po emailu ako je specificiran
            if (!empty($filters['customer_email'])) {
                $charges->data = array_filter($charges->data, function($charge) use ($filters) {
                    return isset($charge->customer->email) &&
                           stripos($charge->customer->email, $filters['customer_email']) !== false;
                });

                // Resetujemo ključeve niza
                $charges->data = array_values($charges->data);
            }

            return $charges;

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Greška (getTransactionsBySubscription): '.$e->getMessage());
            return null;
        }
    }

    /**
     * Formatira paginaciju za Stripe transakcije
     */
    private function formatStripePagination($transactions, $currentPage, $perPage)
    {
        if (!$transactions || !isset($transactions->data)) {
            return [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'has_more' => false,
                'first_id' => null,
                'last_id' => null,
                'total' => 0,
            ];
        }

        $hasMore = isset($transactions->has_more) ? $transactions->has_more : false;
        $firstId = count($transactions->data) > 0 ? $transactions->data[0]->id : null;
        $lastId = count($transactions->data) > 0 ? end($transactions->data)->id : null;

        return [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'has_more' => $hasMore,
            'first_id' => $firstId,
            'last_id' => $lastId,
            'total' => count($transactions->data),
        ];
    }

    /**
     * Dobija detalje o specifičnoj transakciji
     */
    public function stripeTransactionDetails(Request $request, $transactionId)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // First, try to retrieve the charge with expansion
            $transaction = Charge::retrieve([
                'id' => $transactionId,
                'expand' => ['customer', 'invoice', 'invoice.subscription', 'invoice.lines']
            ]);

            // If customer is still not expanded, try to fetch it separately
            if (isset($transaction->customer) && is_string($transaction->customer)) {
                // Customer is just an ID string, not expanded - fetch it separately
                try {
                    $customer = \Stripe\Customer::retrieve($transaction->customer);
                    $transaction->customer = $customer;
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch customer details: '.$e->getMessage());
                }
            }

            // If invoice exists but isn't properly expanded, try to fetch it
            if (isset($transaction->invoice) && is_string($transaction->invoice)) {
                try {
                    $invoice = \Stripe\Invoice::retrieve([
                        'id' => $transaction->invoice,
                        'expand' => ['subscription', 'lines']
                    ]);
                    $transaction->invoice = $invoice;
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch invoice details: '.$e->getMessage());
                }
            }

            $details = [
                'id' => $transaction->id,
                'amount' => number_format($transaction->amount / 100, 2),
                'currency' => strtoupper($transaction->currency),
                'status' => $transaction->status,
                'description' => $transaction->description ?? 'Nema opisa',
                'customer' => isset($transaction->customer) && is_object($transaction->customer) ? [
                    'id' => $transaction->customer->id,
                    'email' => $transaction->customer->email ?? 'Nepoznato',
                    'name' => $transaction->customer->name ?? 'Nepoznato',
                ] : null,
                'payment_method' => isset($transaction->payment_method_details->type) ?
                    $transaction->payment_method_details->type : 'Nepoznato',
                'created' => Carbon::createFromTimestamp($transaction->created)->format('d.m.Y. H:i:s'),
                'invoice' => isset($transaction->invoice) && is_object($transaction->invoice) ? [
                    'id' => $transaction->invoice->id,
                    'number' => $transaction->invoice->number ?? 'Nepoznato',
                    'pdf_url' => $transaction->invoice->invoice_pdf ?? null,
                    'subscription' => isset($transaction->invoice->subscription) && is_object($transaction->invoice->subscription) ? [
                        'id' => $transaction->invoice->subscription->id,
                        'status' => $transaction->invoice->subscription->status,
                        'current_period_start' => Carbon::createFromTimestamp($transaction->invoice->subscription->current_period_start)->format('d.m.Y.'),
                        'current_period_end' => Carbon::createFromTimestamp($transaction->invoice->subscription->current_period_end)->format('d.m.Y.'),
                    ] : null,
                    'lines' => isset($transaction->invoice->lines) && is_object($transaction->invoice->lines) ?
                        array_map(function($line) {
                            return [
                                'description' => $line->description ?? 'Nema opisa',
                                'amount' => number_format($line->amount / 100, 2),
                                'currency' => strtoupper($line->currency),
                                'period' => isset($line->period) && is_object($line->period) ? [
                                    'start' => Carbon::createFromTimestamp($line->period->start)->format('d.m.Y.'),
                                    'end' => Carbon::createFromTimestamp($line->period->end)->format('d.m.Y.'),
                                ] : null,
                            ];
                        }, $transaction->invoice->lines->data) : [],
                ] : null,
            ];

            return response()->json([
                'success' => true,
                'transaction' => $details
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Greška: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Greška pri dobijanju detalja transakcije: '.$e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('General error in stripeTransactionDetails: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Došlo je do greške pri obradi zahteva.'
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
