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
                                        data-currency="{{ $package->currency }}"
                                        {{ (isset($selectedPackageId) && $selectedPackageId == $package->id) ? 'selected' : '' }}>
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
                                        <i class="fas fa-credit-card"></i> Kartično plaćanje
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
                        <table class="table table-bordered align-middle d-none d-md-block">
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
                                                Kartično
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

                         <!-- Mobile & Tablet cards -->
                        <div class="d-md-none">
                            @foreach($subscriptions as $subscription)
                            <div class="card mb-3 subscription-card" data-id="{{ $subscription->id }}">
                                <div class="card-header btn-poslovi-green text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $subscription->package->name ?? 'Nepoznat paket' }}</span>
                                        <span class="badge bg-light text-dark">
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
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Početak</small>
                                            <div>{{ $subscription->created_at ? Carbon::parse($subscription->created_at)->format('d.m.Y') : '-' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Kraj</small>
                                            <div>{{ $subscription->ends_at ? Carbon::parse($subscription->ends_at)->format('d.m.Y') : '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted">Način plaćanja</small>
                                            <div>
                                                @if($subscription->gateway === 'stripe')
                                                    Kreditna kartica
                                                @elseif($subscription->gateway === 'paypal')
                                                    PayPal
                                                @else
                                                    {{ ucfirst($subscription->gateway) ?? '-' }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Cena</small>
                                            <div>{{ $subscription->package->price ?? '' }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">Subscription ID</small>
                                        <div class="text-truncate">
                                            {{ $subscription->subscription_id ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    @if($subscription->status === 'active')
                                        <form method="POST" action="{{ $subscription->gateway === 'stripe' ? route('subscription.stripe.cancel', $subscription->id) : route('subscription.paypal.cancel', $subscription->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Da li ste sigurni da želite da otkažete pretplatu?')">
                                                Otkaži pretplatu
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Details card for mobile (initially hidden) -->
                            <div class="subscription-details-mobile collapse" id="details-mobile-{{ $subscription->id }}">
                                <div class="card card-body bg-light">
                                    <div class="details-loading text-center py-2">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="details-content"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Navigacija za strane -->
                        <div class="mt-4 pagination-buttons text-center">
                            {{ $subscriptions->links() }}
                        </div>
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
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="color:white !important;"></span> Procesiram...';;
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

// Funkcija za prevođenje teksta paginacije
function translatePaginationText() {
   const textElement = document.querySelector("p.text-sm.text-gray-700"); // Selektujemo element koji sadrži tekst
    if (textElement) {
        let text = textElement.textContent.trim();

        // Regex za hvatanje brojeva u stringu
        let matches = text.match(/\d+/g);

        if (matches && matches.length === 3) {
            let translatedText = `Prikazuje od ${matches[0]} do ${matches[1]} od ukupno ${matches[2]} rezultata`;
            textElement.textContent = translatedText;
        }
    }
}
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        translatePaginationText();
    });
</script>
<style>
    /* Custom styles for responsive table */
    .subscription-card {
        cursor: pointer;
        transition: all 0.3s;
    }

    .subscription-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .mobile-subscription-details {
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .table-responsive table {
            font-size: 0.85rem;
        }

        .table-responsive th,
        .table-responsive td {
            padding: 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>

<script>
    // Toggle mobile details
    document.querySelectorAll('.subscription-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const detailsElement = document.getElementById(`details-mobile-${id}`);

            // Toggle collapse
            const bsCollapse = new bootstrap.Collapse(detailsElement, {
                toggle: true
            });

            // Load details if not loaded
            if (!detailsElement.dataset.loaded) {
                fetchDetails(id, 'mobile');
                detailsElement.dataset.loaded = true;
            }
        });
    });

    // Function to fetch details (same as desktop version)
    function fetchDetails(id, type = 'desktop') {
        // Your existing AJAX implementation
        // Update selector based on type:
        // const container = (type === 'mobile')
        //   ? document.querySelector(`#details-mobile-${id} .details-content`)
        //   : document.querySelector(`#details-${id} .details-content`);
    }
</script>
@endsection
