@extends('layouts.app')
@section('content')
<style>
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }

    .blinking-alert {
        animation: blink 1s infinite;
    }

    .form-check-input {
        border-color: #198754;
    }

    .form-check-input:checked {
        background-color: #198754; /* Bootstrap "success" zelena */
    }

    .form-check-input {
        width: 1em;
        height: 1em;
        cursor: pointer;
    }

    .form-check label{
        cursor: pointer;
    }

    .form-control{
        background-color: var(--card-bg) !important;
        color: var(--text-color) !important;
    }

    .form-select {
        background-color: var(--card-bg) !important;
        color: var(--text-color) !important;
        padding: 0.5rem;
        font-size: 1rem;
    }

    /* Stil za option elemente */
    .form-select option {
        background-color: var(--menu-bg) !important;
    }

    /* Za disabled opcije, možete dodati stil */
    .form-select option:disabled {
        color: var(--disabled-text-color); /* Definišite ovu varijablu za boju onemogućenih opcija */
        background-color: var(--disabled-bg-color); /* Definišite ovu varijablu za pozadinu onemogućenih opcija */
    }
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

            <div class="card" style="color: var(--primary-color); background-color: var(--bg-color);">
                <div class="card-header text-center card-header text-white" style="background-color: #198754">
                    <i class="fas fa-credit-card"></i> Depozit novca na tvom balansu !
                </div>

                <div class="card-body">
                    <form id="depositForm" method="POST" action="{{ route('deposit.create') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">Iznos</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="1.00" oninput="formatAmount()" required>
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Valuta</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="EUR">EUR</option>
                               <!--  <option value="USD">USD</option> -->
                            </select>
                        </div>

                        <!-- Mobile  -->
                        <div class="d-md-none text-center">
                                <label class="form-label">Odaberi način plaćanja</label>
                        </div>

                        <div class="mb-3 d-flex">
                            <!-- Desktop -->
                            <div class="d-none d-md-flex">
                                <label class="form-label">Način plaćanja:</label>
                            </div>

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

                        <div id="stripe-container" class="d-none">
                            <div class="mb-3">
                                <label for="card-element" class="form-label">Kartični podaci</label>
                                <div id="card-element">
                                    <!-- Stripe's card input will be inserted here -->
                                </div>
                            </div>
                        </div>

                        <div>
                          <small class="font-weight-bold text-secondary">Napomena o visini naknade (fee):</small><br>
                        <small class="text-secondary">Visina naknade za obradu depozita zavisi od izabranog platnog sistema. Različiti sistemi mogu primenjivati različite naknade u zavisnosti od njihove politike i uslova.</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" id="submit-button" class="btn w-100" style="background-color: #198754">
                                <span id="button-text" class="text-white">Uplati preko PayPal-a</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body" style="color: var(--primary-color); background-color: var(--bg-color);">
                    <div class="row">
                        <div class="text-center mb-4">
                            @if(Auth::user()->role === 'seller')
                                <span class="badge bg-success px-3 py-2">Prodavac</span>
                            @elseif(Auth::user()->role === 'buyer')
                                <span class="badge bg-primary px-3 py-2">Kupac</span>
                            @elseif(Auth::user()->role === 'both')
                                <span class="badge bg-warning text-dark px-3 py-2">Prodavac & Kupac</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Nepoznat</span>
                            @endif
                        </div>

                        <h6 class="text-secondary">
                            <i class="fas fa-credit-card"></i> Trenutni depozit:
                            <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} <i class="fas fa-euro-sign"></i></strong>
                        </h6>

                        @if(Auth::user()->role === 'seller' || Auth::user()->role === 'both')
                            @php
                                $sellerLevels = [
                                    0 => 'Novi prodavac',
                                    1 => 'Novi prodavac',
                                    2 => 'Level 1 prodavac',
                                    3 => 'Level 2 prodavac',
                                    4 => 'Top Rated prodavac',
                                ];
                                $sellerLevelName = $sellerLevels[Auth::user()->seller_level] ?? 'Nepoznat nivo';
                            @endphp

                            @if(Auth::user()->package)
                                <div class="package">
                                    <h6 class="text-secondary">
                                        <i class="fas fa-calendar-alt text-secondary"></i> Mesečni plan:
                                        @if(Auth::user()->package->slug === 'start')
                                            <i class="fas fa-box text-primary"></i>
                                        @elseif(Auth::user()->package->slug === 'pro')
                                            <i class="fas fa-gift text-success"></i>
                                        @elseif(Auth::user()->package->slug === 'premium')
                                            <i class="fas fa-gem text-warning"></i>
                                        @endif
                                        <strong class="text-success">{{ Auth::user()->package->name }}</strong>
                                    </h6>
                                    <h6 class="text-secondary">
                                       <i class="fas fa-file-text"></i> Plan opis:
                                       <strong class="text-success">{{ Auth::user()->package->description }}</strong>
                                    </h6>
                                    <h6 class="text-secondary">
                                       <i class="fas fa-calendar-times"></i> Plan ističe:
                                       <strong class="text-success">{{ \Carbon\Carbon::parse(Auth::user()->package_expires_at)->format('d.m.Y H:i:s') }}</strong>
                                    </h6>
                                </div>
                                <hr>
                            @else
                                <div class="alert alert-danger text-center">
                                    <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemaš aktivan paket!
                                </div>

                                <div class="text-warning mb-2">
                                    <a href="{{ route('packages.index') }}" class="btn btn-outline-primary ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>
                                    <p class="text-center text-secondary">Odaberi paket</p>
                                </div>
                            @endif

                            <h6 class="text-secondary">
                                <i class="fas fa-user"></i> Nivo prodavca:
                                <strong class="text-success">{{ $sellerLevelName }}</strong>
                            </h6>
                            <h6 class="text-secondary">
                                <i class="fas fa-credit-card"></i> Ukupna mesečna zarada:
                                <strong class="text-success">{{ number_format($totalEarnings, 2) }} <i class="fas fa-euro-sign"></i></strong>
                            </h6>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#deposit-message') {
        const element = document.getElementById('deposit-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

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

    // PayPal/Stripe loading state
    const form = document.getElementById('depositForm');
    form.addEventListener('submit', () => {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesiram...';
    });
});
</script>

<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
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

    const stripeContainer = document.getElementById('stripe-container');
    const paypalRadio = document.getElementById('paypal');
    const stripeRadio = document.getElementById('stripe');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const form = document.getElementById('depositForm');

    stripeRadio.addEventListener('change', function () {
        if (stripeRadio.checked) {
            stripeContainer.classList.remove('d-none');
            submitButton.classList.add('text-white');
            submitButton.innerHTML = 'Uplati preko kartice';
        }
    });

    paypalRadio.addEventListener('change', function () {
        if (paypalRadio.checked) {
            stripeContainer.classList.add('d-none');
            submitButton.classList.add('text-white');
            submitButton.innerHTML = 'Uplati preko PayPal-a';
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (stripeRadio.checked) {
            const { error, paymentMethod } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
            });

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                return;
            }

            // Add loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesiram...';

            // Send paymentMethod.id to server
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method_stripe');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);

            // Submit form
            form.submit();
        } else {
            form.submit();
        }
    });
});
</script>

<script>
    let timeout;

    function formatAmount() {
        // Ako je prethodni timeout postavljen, brišemo ga
        clearTimeout(timeout);

        // Postavljamo novi timeout koji će se izvršiti nakon 300ms
        timeout = setTimeout(function() {
            let amountInput = document.getElementById('amount');
            let value = amountInput.value;

            // Ako je unos celo broj, formatiramo ga sa dve decimale
            if (value && value % 1 === 0) {
                amountInput.value = parseFloat(value).toFixed(2);
            }
        }, 500);  // 500ms čekanja
    }
</script>
