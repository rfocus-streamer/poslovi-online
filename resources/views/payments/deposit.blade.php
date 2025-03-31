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
                <div class="card-header text-center card-header text-white" style="background-color: #198754"><i class="fas fa-credit-card"></i> Depozit novca na tvom balansu !</div>

                <div class="card-body">
                    <form id="depositForm" method="POST" action="{{ route('deposit.create') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">Iznos</label>
                            <input type="number" step="001" class="form-control" id="amount" name="amount" value="1000" required>
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Valuta</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="EUR">EUR</option>
                            </select>
                        </div>

                        <!-- Dodatna polja za Stripe -->
                        <div id="stripe-fields" style="display: none;">
                            <div id="card-element" class="form-control"></div>
                            <input type="hidden" name="payment_method_stripe" id="payment_method_stripe">
                            <br>
                                <div>
                                    <h6 class="text-center">Za testiranje dok traje razvoj, koristite sljedeće test kartice:</h6>
                                    <table class="table table-bordered w-100 text-center">
                                        <thead>
                                            <tr>
                                                <th>Tip Kartice</th>
                                                <th>Broj Kartice</th>
                                                <th>Datum</th>
                                                <th>CVC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Visa</td>
                                                <td>4242 4242 4242 4242</td>
                                                <td>12/34</td>
                                                <td>123</td>
                                            </tr>
                                            <tr>
                                                <td>Mastercard</td>
                                                <td>5555 5555 5555 4444</td>
                                                <td>12/34</td>
                                                <td>123</td>
                                            </tr>
                                            <tr>
                                                <td>American Express</td>
                                                <td>3782 822463 10005</td>
                                                <td>12/34</td>
                                                <td>1234</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Način plaćanja</label>
                            <div class="d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" checked>
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal"></i> PayPal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe">
                                    <label class="form-check-label" for="stripe">
                                        <i class="fab fa-cc-stripe"></i> Stripe
                                    </label>
                                </div>
                               <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="wise" value="wise">
                                    <label class="form-check-label" for="wise" style="margin-top: -2px">
                                        <svg class="np-logo-svg" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 106 24">
                                            <path fill="#163300" d="M58.7377.358803h6.4982L61.9668 23.681h-6.4983L58.7377.358803Zm-8.1922 0L46.1602 13.794 44.2465.358803h-4.5448L33.9608 13.7541 33.2433.358803h-6.2991L29.1369 23.681h5.2226L40.818 8.93022 43.0904 23.681h5.1428L56.7249.358803h-6.1794ZM105.103 13.9136H89.6744c.0798 3.0299 1.8937 5.0233 4.5648 5.0233 2.0133 0 3.608-1.0765 4.8439-3.1296l5.2079 2.3674C102.501 21.7017 98.729 24 94.0798 24 87.741 24 83.535 19.7342 83.535 12.8771 83.535 5.34221 88.4784 0 95.4552 0c6.1398 0 10.0068 4.14618 10.0068 10.6046 0 1.0765-.12 2.1528-.359 3.309Zm-5.7807-4.46512c0-2.71095-1.515-4.42525-3.9468-4.42525-2.5117 0-4.5848 1.79402-5.143 4.42525h9.0898ZM6.63326 7.38685 0 15.1389h11.844l1.3309-3.6553H8.09965l3.10105-3.58555.01-.09511L9.19424 4.3319h9.07196l-7.0323 19.3492h4.8124L24.538.358823H2.60021L6.63326 7.38685Zm69.16744-2.3636c2.2923 0 4.301 1.23273 6.0551 3.34565l.9216-6.57488C81.1429.687707 78.9303 0 76 0c-5.8205 0-9.0896 3.40865-9.0896 7.73421 0 2.99999 1.6744 4.83389 4.4252 6.01989l1.3156.598c2.4518 1.0466 3.1097 1.5649 3.1097 2.6712 0 1.1461-1.1064 1.8737-2.7907 1.8737-2.7808.01-5.0332-1.4153-6.7276-3.8472l-.939 6.699C67.2332 23.2201 69.7067 24 72.9702 24c5.5315 0 8.9302-3.1894 8.9302-7.6147 0-3.0099-1.3355-4.9434-4.7044-6.4584l-1.4351-.67772c-1.9934-.88708-2.6711-1.37543-2.6711-2.35216 0-1.05647.9269-1.87377 2.7109-1.87377Z"></path>
                                        </svg>
                                        Wise
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- <button type="submit" class="btn btn-primary ms-3">Nastavi na plaćanje</button> -->
                        <div class="mt-4">
                            <button type="submit" id="submit-button" class="btn w-100" style="background-color: #198754">
                                <span id="button-text">Plati</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
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
                            <i class="fas fa-credit-card"></i> Trenutni depozit: <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} <i class="fas fa-euro-sign"></i></strong>
                        </h6>

                        @if( Auth::user()->role === 'seller' or  Auth::user()->role === 'both')
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
                                       <i class="fas fa-file-text"></i> Plan opis: <strong class="text-success">{{ Auth::user()->package->description }}</strong>
                                    </h6>
                                    <h6 class="text-secondary">
                                       <i class="fas fa-calendar-times"></i> Plan ističe: <strong class="text-success">{{ \Carbon\Carbon::parse(Auth::user()->package_expires_at)->format('d.m.Y H:i:s') }}</strong>
                                    </h6>
                                </div>
                                <hr>
                            @else
                                <div class="alert alert-danger text-center">
                                    <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemate aktivan paket!
                                </div>

                                <div class="text-warning mb-2">
                                    <a href="{{ route('packages.index') }}" class="btn btn-outline-primary ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> <i class="fas fa-calendar-alt"></i>
                                    </a>
                                    <p class="text-center text-secondary">Odaberite paket</p>
                                </div>
                            @endif


                            <h6 class="text-secondary">
                                <i class="fas fa-user"></i> Nivo prodavca: <strong class="text-success">{{ $sellerLevelName }}</strong>
                            </h6>
                            <h6 class="text-secondary">
                                <i class="fas fa-credit-card"></i> Ukupna mesečna zarada: <strong class="text-success">{{ number_format($totalEarnings, 2) }} <i class="fas fa-euro-sign"></i></strong>
                            </h6>
                        @endif


                    </div>
                </div>
            </div>
        </div>

        <!--  Modal -->


    </div>
</div>
@endsection
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

    const form = document.getElementById('depositForm');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const { error, paymentMethod } = await stripe.createPaymentMethod({
            type: 'card',
            card: card
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            return;
        }

        // Dodajte loading state
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesiram...';

        // Pošaljite paymentMethod.id na server
        const hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_method_stripe');
        hiddenInput.setAttribute('value', paymentMethod.id);
        form.appendChild(hiddenInput);

        // Submit forme
        form.submit();
    });
});
</script>



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

    const stripeRadio = document.getElementById("stripe");
    const paypalRadio = document.getElementById("paypal");
    const wiseRadio = document.getElementById("wise");
    const stripeFields = document.getElementById("stripe-fields");

    function toggleStripeFields() {
        if (stripeRadio.checked) {
            stripeFields.style.display = "block";
        } else {
            stripeFields.style.display = "none";
        }
    }

    stripeRadio.addEventListener("change", toggleStripeFields);
    paypalRadio.addEventListener("change", toggleStripeFields);
    wiseRadio.addEventListener("change", toggleStripeFields);
});
</script>
