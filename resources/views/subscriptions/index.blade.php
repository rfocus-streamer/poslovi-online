@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<title>Poslovi Online | Subscriptions</title>

@section('content')
<style>
   /* .subscription-row:hover { cursor: pointer; background: #f8f9fa; }
    .subscription-details { background: #f8f9fa; }
    .details-loading { display: none; }*/
</style>
<div class="container py-5">
    <div class="row">
        <!-- Glavni sadržaj -->
        <div class="col-md-8">
            <!-- Prikaz poruka -->
            @if(session('success'))
                <div id="deposit-message" class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="deposit-message-danger" class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header text-center card-header text-white" style="background-color: #198754">
                    <i class="fas fa-credit-card"></i> Odaberi pretplatu
                </div>

                <div class="card-body">
                    <form id="subscriptionForm" method="POST" action="{{ route('subscriptions.store') }}">
                        @csrf

                        <!-- Odabir paketa -->
                        <div class="mb-3">
                            <label for="package" class="form-label"><i class="fas fa-calendar-alt"></i> Godišnji ili mesečni plan</label>
                            <select class="form-select" id="package" name="package_id" required>
                                @foreach($packages as $package)
                                    @php
                                        $translatedDuration = match($package->duration) {
                                            'yearly' => 'godišnje',
                                            'monthly' => 'mesečno',
                                            default => $package->duration,
                                        };
                                    @endphp
                                    <option value="{{ $package->id }}"
                                        data-duration="{{ $package->duration }}"
                                        data-price="{{ $package->price }}"
                                        data-currency="{{ $package->currency }}">
                                        {{ $package->name }} - {{ $package->price }} {{ $package->currency }} / {{ $translatedDuration }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Način plaćanja -->
                        <div class="mb-3 d-flex">
                            <label class="form-label">Način plaćanja:</label>
                            <div class="ms-2 d-flex justify-content-center text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" checked>
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal"></i> PayPal
                                    </label>
                                </div>

                                <div class="form-check ms-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe">
                                    <label class="form-check-label" for="stripe">
                                        <i class="fas fa-credit-card"></i> Kreditna kartica
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Stripe Card Element -->
                        <div id="stripe-container" class="d-none">
                            <div class="mb-3">
                                <label for="card-element" class="form-label">Kartični podaci</label>
                                <div id="card-element">
                                    <!-- Stripe's card input will be inserted here -->
                                </div>
                                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                            </div>
                        </div>

                        <div>
                          <small class="font-weight-bold text-secondary">Napomena o visini naknade (fee):</small><br>
                        <small class="text-secondary">Visina naknade za obradu pretplate zavisi od izabranog platnog sistema. Različiti sistemi mogu primenjivati različite naknade u zavisnosti od njihove politike i uslova.</small>
                        </div>

                        <!-- Uslovi i dugme -->
                        <div class="mt-4">
                            <button type="submit" id="submit-button" class="btn w-100" style="background-color: #198754">
                                <span id="button-text" class="text-white"><i class="fas fa-check"></i> Aktiviraj pretplatu</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled">
                      <li>🔹 <strong>Mesečni paket:</strong><br> &nbsp; &nbsp; &nbsp; Plaćaj mesečno i uživaj u slobodi.</li>
                      <li>🔹 <strong>Godišnji paket:</strong><br> &nbsp; &nbsp; &nbsp; Uštedi više sa godišnjom pretplatom!</li>
                    </ul>

                    <strong>Načini plaćanja:</strong>
                    <ul class="list-unstyled">
                      <li>✅ PayPal</li>
                      <li>✅ Kreditna kartica</li>
                      <li>✅ Debitna kartica</li>
                    </ul>

                    <p class="text-muted mt-5">Pretplatu možeš otkazati u bilo kom trenutku, bez dodatnih troškova ili obaveza.</p>

                </div>
            </div>
        </div>


        <div class="col-md-12">
            @php
                use Carbon\Carbon;
            @endphp

           @if($subscriptions->count() > 0)
                <div class="card mt-4">
                    <div class="card-header btn-poslovi text-white text-center">
                        Tvoje pretplate
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Paket</th>
                                    <th>Status</th>
                                    <th>Početak</th>
                                    <th>Kraj</th>
                                    <th>Način plaćanja</th>
                                    <th>Cena</th>
                                    <th>Subscription ID</th>
                                    <th>Akcija</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr class="subscription-row" data-id="{{ $subscription->id }}">
                                        <td>{{ $subscription->package->name ?? 'Nepoznat paket' }}</td>
                                        <td>
                                            @php
                                                $status = strtolower($subscription->status);
                                                $statusText = match($status) {
                                                    'active' => 'Aktivan',
                                                    'inactive' => 'Neaktivan',
                                                    'failed' => 'Neuspešan',
                                                    'canceled' => 'Otkazan',
                                                    default => ucfirst($subscription->status),
                                                };
                                            @endphp
                                            {{ $statusText }}
                                        </td>
                                        <td>{{ $subscription->created_at ? Carbon::parse($subscription->created_at)->format('d.m.Y') : '-' }}</td>
                                        <td>{{ $subscription->ends_at ? Carbon::parse($subscription->ends_at)->format('d.m.Y') : '-' }}</td>
                                        <td>
                                            @if($subscription->gateway === 'stripe')
                                                Kreditna ili debitna kartica
                                            @elseif($subscription->gateway === 'paypal')
                                                PayPal
                                            @else
                                                {{ ucfirst($subscription->gateway) ?? '-' }}
                                            @endif
                                        </td>
                                        <td>{{ $subscription->package->price ?? '' }}</td>
                                        <td>
                                            <a style="text-decoration: none; display: none;" href="#" class="show-details"
                                               data-subscription-id="{{ $subscription->subscription_id }}">
                                                {{ $subscription->subscription_id ?? '-' }}
                                            </a>
                                            {{ $subscription->subscription_id ?? '-' }}
                                        </td>
                                        <td>
                                            @if($subscription->status === 'active')
                                                <form method="POST" action="{{ $subscription->gateway === 'stripe' ? route('subscription.stripe.cancel', $subscription->id) : route('subscription.paypal.cancel', $subscription->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Da li ste sigurni da želite da otkažete pretplatu?')">
                                                        Otkaži
                                                    </button>
                                                </form>
                                            @elseif($subscription->status === 'canceled')
                                               <!--  <form action="{{ route('subscription.destroy', $subscription->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Brisanjem ćete izgubiti istorijat!')">
                                                        Obriši
                                                    </button>
                                                </form> -->
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="subscription-details" id="details-{{ $subscription->id }}" style="display: none;">
                                        <td colspan="7">
                                            <div class="details-loading text-center py-2">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <div class="details-content"></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center mt-4">
                    Nema trenutno aktivne pretplate.
                </div>
            @endif
        </div>

    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // Prikaz detalja pretplate
    document.querySelectorAll('.subscription-row').forEach(row => {
        return false;
        row.addEventListener('click', function() {
            const detailsRow = document.querySelector(`#details-${this.dataset.id}`);
            const isVisible = detailsRow.style.display === 'table-row';

            // Sakrij sve detalje pre prikaza novih
            document.querySelectorAll('.subscription-details').forEach(d => {
                console.log('here');
                d.style.display = 'none';
                d.querySelector('.details-content').innerHTML = '';
            });

            if (!isVisible) {
                const loading = detailsRow.querySelector('.details-loading');
                const content = detailsRow.querySelector('.details-content');

                detailsRow.style.display = 'table-row';
                loading.style.display = 'block';

                // Poziv API-ja za detalje
                fetch(`/subscription/${this.dataset.id}/details`)
                    .then(response => response.json())
                    .then(data => {
                        loading.style.display = 'none';
                        content.innerHTML = `
                            <h6>Detalji pretplate:</h6>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        `;
                    });
            }
        });
    });
});
</script>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements();
    const card = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
            }
        }
    });
    card.mount('#card-element');

    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const stripeContainer = document.getElementById('stripe-container');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            stripeContainer.classList.toggle('d-none', this.value !== 'stripe');
        });
    });

    const form = document.getElementById('subscriptionForm');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (document.querySelector('input[name="payment_method"]:checked').value === 'stripe') {
            const {error, paymentMethod} = await stripe.createPaymentMethod({
                type: 'card',
                card: card
            });

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                return;
            }

            // Dodajte payment method ID u formu
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method_stripe');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);
        }

        // Submit forma
        form.submit();
    });
});

document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
    const submitButton = document.getElementById('submit-button');
    submitButton.disabled = true;
    submitButton.innerHTML = 'Obrada...';
});
</script>

<script type="text/javascript">
    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('deposit-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('deposit-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }
</script>
@endsection
