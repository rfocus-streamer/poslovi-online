<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\Invoice;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $payPalService;
    protected $stripeService;

    public function __construct(PayPalService $payPalService, StripeService $stripeService)
    {
        $this->payPalService = $payPalService;
        $this->stripeService = $stripeService;
    }

    /**
     * Prikaz svih dostupnih planova
     */
    public function index()
    {
        $user = auth()->user();
        $packages = Package::all();
        $stripeKey = config('services.stripe.public');
        // Pretplate korisnika (možeš filter po statusu ako želiš samo aktivne)
        $subscriptions = $user->subscriptions()->latest()->get();

        return view('subscriptions.index', compact('packages', 'stripeKey', 'subscriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_method' => 'required|in:paypal,stripe'
        ]);

        $user = Auth::user();
        $package = Package::findOrFail($request->package_id);

        // Atomicna operacija sa zaključavanjem
        \DB::transaction(function () use ($package) {
            $package = $package->lockForUpdate()->first();

            if (!$package->stripe_price_id) {
                try {
                    $price = $this->stripeService->createPrice($package);
                    $package->stripe_price_id = $price->id;
                    $package->save();
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    if ($e->getStripeCode() !== 'resource_already_exists') {
                        throw $e;
                    }
                    // Ako cena već postoji, dohvatimo postojeću
                    $price = \Stripe\Price::all([
                        'product' => $package->name,
                        'active' => true
                    ])->first();
                    $package->stripe_price_id = $price->id;
                    $package->save();
                }
            }
        });

        // Osveži podatke o paketu
        $package->refresh();

        // Provera aktivnih pretplata
        if ($user->subscriptions()->active()->forPackage($package->id)->exists()) {
            return redirect()->back()->with('error', 'Već imate aktivnu pretplatu za ovaj paket.');
        }

        // Kreiranje pretplate
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $package->id,
            'status' => 'pending',
            'amount' => $package->price,
            'gateway' => $request->payment_method,
        ]);

        // Obrada plaćanja
        return match($request->payment_method) {
            'paypal' => $this->handlePayPalSubscription($subscription, $package),
            'stripe' => $this->handleStripeSubscription($subscription, $package, $request->payment_method_stripe),
            default => abort(400, 'Nepodržan način plaćanja')
        };
    }

    private function handlePayPalSubscription(Subscription $subscription, Package $package)
    {
        try {
            if (!$package->paypal_plan_id) {
                $paypalPlanId = $this->payPalService->createPlan($package);
                $package->paypal_plan_id = $paypalPlanId;
                $package->save();
            }

            if (!$package->paypal_plan_id) {
                throw new \Exception('PayPal plan ID nije definisan za ovaj paket.');
            }

            $returnUrl = route('paypal.success', ['subscription_id' => $subscription->id]);
            $cancelUrl = route('subscriptions.index');

            $paypalSubscription = $this->payPalService->createSubscriptionLink(
                $package->paypal_plan_id,
                $returnUrl,
                $cancelUrl
            );

            $subscription->update([
                'subscription_id' => $paypalSubscription['id'] ?? null,
                'status' => 'pending',
                'payload' => json_encode($paypalSubscription)
            ]);

            $approvalUrl = collect($paypalSubscription['links'] ?? [])
                                ->firstWhere('rel', 'approve')['href'] ?? null;

            // Redirect na PayPal
            return redirect($approvalUrl);
        } catch (\Exception $e) {
            \Log::error('PayPal Subscription Error: ' . $e->getMessage());
            $subscription->status = 'failed';
            $subscription->save();
            return redirect()->route('subscriptions.index')->with('error', 'Greška prilikom povezivanja sa PayPal-om.');
        }
    }

    private function handleStripeSubscription(Subscription $subscription, Package $package, $paymentMethodId)
    {
        try {
            $user = Auth::user();
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            if (!$package->stripe_price_id) {
                $price = $this->stripeService->createPrice($package);
                $package->stripe_price_id = $price->id;
                $package->save();
                //throw new \Exception("Stripe cena nije definisana za ovaj paket.");
            }

            $priceId = $package->stripe_price_id;
            if (!$priceId) {
                throw new \Exception('Stripe cena nije definisana za ovaj paket.');
            }

            if (!$user->stripe_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->firstname.' '.$user->lastname,
                    'payment_method' => $paymentMethodId,
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId,
                    ],
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Kreiraj pretplatu - UVEK koristi user->stripe_id posle toga!
            $stripeSubscription = \Stripe\Subscription::create([
                'customer' => $user->stripe_id, // NE $customer->id jer možda nije postavljen
                'items' => [[
                    'price' => $package->stripe_price_id,
                ]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            $paymentIntent = $stripeSubscription->latest_invoice->payment_intent;

            // Ažuriraj internu pretplatu
            $subscription->update([
                'subscription_id' => $stripeSubscription->id,
                'status' => $paymentIntent->status === 'succeeded' ? 'active' : 'pending',
                'ends_at' => isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
                'payload' => json_encode($stripeSubscription),
            ]);

            if ($paymentIntent->status === 'requires_action') {
                // 3D Secure — možeš proslediti client_secret korisniku i nastaviti u JS
                return redirect()->route('subscriptions.index')->with('info', 'Potrebna dodatna verifikacija kartice.');
            }

            //Ažuriraj balans
            $user->deposits += $package->price; // Konvertuj iz centi
            $user->save();

            return redirect()->route('subscriptions.index')->with('success', 'Pretplata uspešno aktivirana!');

        } catch (\Exception $e) {
            \Log::error('Stripe Direct Subscription Error: ' . $e->getMessage());
            $subscription->update(['status' => 'failed']);
            return redirect()->route('subscriptions.index')->with('error', 'Greška prilikom plaćanja: ' . $e->getMessage());
        }
    }


    public function paypalSuccess(Request $request)
    {
        $subscriptionId = $request->query('subscription_id');
        $subscription = Subscription::findOrFail($subscriptionId);

        try {
            $paypalSubscription = $this->payPalService->getSubscription($subscription->subscription_id);

            if ($paypalSubscription->status === 'ACTIVE') {
                $subscription->update([
                    'status' => 'active',
                    'ends_at' => isset($paypalSubscription->billing_info->next_billing_time)
                        ? Carbon::parse($paypalSubscription->billing_info->next_billing_time)
                        : null,
                    'payload' => json_encode($paypalSubscription)
                ]);

                $user = $subscription->user;
                $user->deposits += $subscription->amount;
                $user->save();

                return redirect()->route('subscriptions.index')->with('success', 'PayPal pretplata uspešno aktivirana.');
            }

            return redirect()->route('subscriptions.index')->with('info', 'Pretplata nije aktivirana.');
        } catch (\Exception $e) {
            \Log::error('PayPal Success Error: ' . $e->getMessage());
            return redirect()->route('subscriptions.index')->with('error', 'Greška prilikom obrade PayPal pretplate.');
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        try {
            // Dohvati sesiju iz Stripe-a
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // Pronađi lokalnu pretplatu preko session ID-ja
            $subscription = Subscription::where('stripe_session_id', $session->id)->firstOrFail();

            // Dohvati pravu Stripe pretplatu
            $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);

            // Ažuriraj lokalnu pretplatu
            $subscription->update([
                'subscription_id' => $stripeSubscription->id,
                'status' => 'active',
                'ends_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);

            return redirect()->route('subscriptions.index')->with('success', 'Pretplata uspešno aktivirana!');
        } catch (\Exception $e) {
            Log::error('Stripe Success Error: ' . $e->getMessage());
            return redirect()->route('subscriptions.index')->with('error', 'Greška pri aktivaciji pretplate.');
        }
    }


    public function stripeCancel(Subscription $subscription)
    {
        try {
            if ($subscription->gateway !== 'stripe') {
                return redirect()->back()->with('error', 'Ova pretplata nije Stripe pretplata.');
            }

            if (!$subscription->subscription_id) {
                return redirect()->back()->with('error', 'Pretplata nema Stripe ID.');
            }

            // Otkazivanje odmah
            $this->stripeService->cancelSubscription(
                $subscription->subscription_id,
                true // Postavljamo na true za instant otkazivanje
            );

            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Pretplata je uspešno otkazana.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Greška prilikom otkazivanja: ' . $e->getMessage());
        }
    }

    public function details($id)
    {
        $subscription = Subscription::findOrFail($id);

        try {
            $details = $this->stripeService->getSubscription($subscription->subscription_id);
            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function updateSubscriptionEndDate($invoice)
    {
        if ($invoice->billing_reason === 'subscription_cycle') {
            Subscription::where('subscription_id', $invoice->subscription)
                ->update(['ends_at' => Carbon::createFromTimestamp($invoice->lines->data[0]->period->end)]);
        }
    }

    public function handlePayPalWebhook(Request $request)
    {
        $event = $request->all();

        switch ($event['event_type']) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->activatePayPalSubscription($event['resource']['id']);
                break;
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->cancelPayPalSubscription($event['resource']['id']);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    private function activatePayPalSubscription($subscriptionId)
    {
        Subscription::where('subscription_id', $subscriptionId)
            ->update([
                'status' => 'active',
                'ends_at' => now()->addMonth()
            ]);
    }

    public function paypalCancel(Subscription $subscription)
    {
        try {
            if ($subscription->gateway !== 'paypal') {
                return redirect()->back()->with('error', 'Ova pretplata nije PayPal pretplata.');
            }

            if (!$subscription->subscription_id) {
                return redirect()->back()->with('error', 'Pretplata nema PayPal ID.');
            }

            $this->payPalService->cancelSubscription($subscription->subscription_id);

            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);

            return redirect()->back()->with('success', 'PayPal pretplata je uspešno otkazana.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Greška prilikom otkazivanja PayPal pretplate: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);

        // Provera da li je pretplata već otkazana u Stripe-u
        if ($subscription->status !== 'canceled') {
            return redirect()->back()->with('error', 'Prvo otkažite pretplatu pre brisanja');
        }

        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Pretplata obrisana iz baze');
    }
}
