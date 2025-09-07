<div class="tab-pane fade {{ $activeTab === 'finances' ? 'show active' : '' }}" id="finances">
    <h2 class="mb-4 text-center">Finansije</h2>
    <div class="card shadow-lg rounded">
        <form method="GET" action="{{ route('admin.dashboard') }}">
            <div class="d-flex mt-2 justify-content-center align-items-center">
                <input type="hidden" name="tab" value="finances">
                <div class="col-md-2">
                    <select name="report_month" class="form-select form-select-lg">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="report_year" class="form-select form-select-lg">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Izračunaj</button>
                </div>
            </div>
        </form>

        <hr class="my-4">

<div class="container">
    <div class="row mb-4 justify-content-center">
        <!-- Stripe statistika Header -->
        <div class="col-12 text-center mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Stripe statistika</h5>
            </div>
        </div>

        <!-- Trenutni Balans Card -->
        <div class="col-md-3 col-lg-3 mb-3">
            <div class="card shadow-sm rounded">
                <div class="card-body text-center">
                    <h5 class="card-title">Trenutni Balans</h5>
                    <p class="card-text">
                        @if (is_object($stripeBalance) && isset($stripeBalance->available) && is_array($stripeBalance->available) && isset($stripeBalance->available[0]))
                            <span class="display-4 text-success">
                                {{ number_format($stripeBalance->available[0]->amount / 100, 2) }}
                                {{ strtoupper($stripeBalance->available[0]->currency) }}
                            </span>
                        @else
                            <span class="text-muted">Podaci nisu dostupni.</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Mesec i Godina Card -->
        <div class="col-md-3 col-lg-3 mb-3">
            <div class="card shadow-sm rounded">
                <div class="card-body text-center">
                    @php
                        $month = request()->query('report_month', date('n'));
                        $year = request()->query('report_year', date('Y'));
                        $months = [
                            1 => 'Januar', 2 => 'Februar', 3 => 'Mart', 4 => 'April', 5 => 'Maj', 6 => 'Jun',
                            7 => 'Jul', 8 => 'Avgust', 9 => 'Septembar', 10 => 'Oktobar', 11 => 'Novembar', 12 => 'Decembar'
                        ];
                        $monthName = $months[$month] ?? 'Nepoznat mesec';
                    @endphp
                    <h5 class="card-title text-primary">{{ $monthName }} {{ $year }}</h5>
                    <p class="card-text display-4">
                        {{ number_format($monthlyStripeReport['total_amount'], 2) }}
                        {{ strtoupper($monthlyStripeReport['currency']) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Uspešne transakcije Card -->
        <div class="col-md-3 col-lg-3 mb-3">
            <div class="card shadow-sm rounded">
                <div class="card-body text-center">
                    <h5 class="card-title">Uspešnih transakcija</h5>
                    <p class="card-text display-4 text-success">{{ $monthlyStripeReport['successful_charges'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
