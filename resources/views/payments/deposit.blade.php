@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

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

                <div class="card-header text-center"><i class="fas fa-credit-card"></i> Depozit novca na vašem balansu !</div>

                <div class="card-body">
                    <form id="depositForm" method="POST" action="{{ route('deposit.paypal.create') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">Iznos</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="10.00" required>
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Valuta</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="RSD">RSD</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>

                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-label">Način plaćanja</label>
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
                                    <label class="form-check-label" for="wise">
                                        <i class="fas fa-money-bill-transfer"></i> Wise
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary ms-3">Nastavi na plaćanje</button>
                        </div>
                    </form>
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
});
</script>
