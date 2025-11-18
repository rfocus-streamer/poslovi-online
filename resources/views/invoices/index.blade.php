@extends('layouts.app')

<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<title>Poslovi Online | Računi</title>

@section('content')
<style type="text/css">
    .billing-period {
        word-wrap: break-word; /* Prelomi reč na sledećoj liniji */
        word-break: break-word; /* Prelomi reč ako je dugačka */
        overflow-wrap: break-word; /* Još jedan način da omogućite prelamanje reči */
    }

    /* Ako koristite Flexbox, omogućite prelamanje redova u row-u */
    .card-body .row {
        flex-wrap: wrap; /* Omogućava prelamanje u redovima */
    }

     .table thead th {
        color: var(--text-color);
        border-color: var(--border-color);
        background-color: var(--menu-bg);
    }

    .table tbody td {
        border-color: var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-color);
    }

    .table tbody td a {
        color: var(--primary-color) !important;
        text-decoration: none;
    }

    .table tbody td a:hover {
        color: var(--primary-color);
    }

    .mobile-div div {
        color: var(--text-color);
    }

    .mobile-div a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .alert {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .alert-danger {
        background-color: var(--danger-bg);
        border-color: var(--danger);
        color: var(--danger);
    }

    .alert-success {
        background-color: var(--success-bg);
        border-color: var(--success);
        color: var(--success);
    }

    .card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
        box-shadow: 0 0.125rem 0.25rem var(--shadow);
    }

    .card-header {
        background-color: var(--menu-bg) !important;
        border-bottom-color: var(--border-color);
        color: var(--text-color);
    }

    .bg-light {
        background-color: var(--menu-bg) !important;
        color: var(--text-color);
    }

    .border {
        border-color: var(--border-color) !important;
    }

    .rounded {
        border-color: var(--border-color);
    }

    .btn-outline-light {
        border-color: var(--border-color);
        color: var(--text-color);
        background-color: transparent;
    }

    .btn-outline-light:hover {
        background-color: var(--menu-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .btn-link {
        color: var(--primary);
    }

    .btn-link:hover {
        color: var(--primary);
    }

    .modal-content {
        background-color: var(--menu-bg);
        color: var(--text-color);
        border-color: var(--border-color);
    }

    #modalUserImage {
        width: 130px;
        height: 130px;
        border-radius: 50%; /* Okrugli oblik */
        object-fit: cover; /* Obezbeđuje da slika popuni celu površinu bez distorzije */
    }
</style>

<div class="container">
    <!-- Prikaz poruka -->
    @if(session('success'))
        <div id="invoice-message" class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="invoice-message-danger" class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <!-- Desktop naslov + info -->
        <div class="d-none d-md-flex align-items-center gap-3 w-100">
            <h4 class="mb-0"><i class="fas fa-file-invoice"></i> Tvoji računi</h4>
        </div>

        <!-- Mobile naslov + info -->
        <div class="d-flex d-md-none flex-column text-center w-100">
            <h6 class="mb-0"><i class="fas fa-file-invoice"></i> Tvoji računi</h6>
        </div>
    </div>


    @if($invoices->isEmpty())
        <p>Nemaš generisanih računa.</p>
    @else
    <!-- Desktop tabela -->
    <div class="d-none d-md-flex">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Račun</th>
                    <th>Kreiran</th>
                    <th class="text-center">Opis</th>
                    <th>Obračunski period</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $key => $invoice)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>#{{ $invoice->number }} </td>
                        <td>{{ $invoice->issue_date->format('d.m.Y') }}</td>
                        @foreach($invoice->items as $item)
                            <td>
                                @if(strlen($item['description']) > 25)
                                    {{ \Illuminate\Support\Str::limit($item['description'], 25) }}
                                @else
                                    {{ $item['description'] }}
                                @endif
                            </td>
                            <td>
                                @if(strlen($item['billing_period']) > 25)
                                    {{ \Illuminate\Support\Str::limit($item['billing_period'], 25) }}
                                @else
                                    {{ $item['billing_period'] }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-center">{{ ucfirst($invoice->status) }}</td>
                        <td class="text-center">
                            <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}" style="text-decoration: none;" target="_blank" title="Pogledaj PDF"><button type="submit" class="btn btn-sm btn-warning">Pogledaj <i class="fa fa fa-eye"></i></button></a>

                            <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}" style="text-decoration: none;" target="_blank" title="Preuzmi PDF"><button type="submit" class="btn btn-sm btn-danger">Preuzmi <i class="fa fa-file-pdf" download></i></button></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginacija -->
        <div class="d-flex justify-content-center">
            {{ $invoices->links() }} <!-- Ovo dodaje dugmadi za navigaciju -->
        </div>
    </div>

    <!-- Mobile & Tablet kartice -->
    <div class="d-md-none mt-2">
        <div class="invoice-cards-container" style="overflow-x: auto; white-space: nowrap;">
            @foreach($invoices as $invoice)
                @foreach($invoice->items as $item)
                    <div class="card mb-3 subscription-card">
                        <div class="card-header btn-poslovi-green text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>#{{ $invoice->number }}</span>
                                <span class="badge bg-light text-dark">{{ ucfirst($invoice->status) }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Kreiran</small>
                                    <div>{{ $invoice->issue_date->format('d.m.Y') }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Opis</small>
                                    <div>
                                        @if(strlen($item['description']) > 25)
                                            {{ \Illuminate\Support\Str::limit($item['description'], 25) }}
                                        @else
                                            {{ $item['description'] }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Obračunski period</small>
                                <div class="billing-period">
                                    @if(strlen($item['billing_period']) > 35)
                                        {{ \Illuminate\Support\Str::limit($item['billing_period'], 35) }}
                                    @else
                                        {{ $item['billing_period'] }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}" class="btn btn-warning btn-sm text-dark" target="_blank">
                                    Pogledaj <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}" class="btn btn-sm btn-danger" target="_blank">
                                    Preuzmi <i class="fa fa-file-pdf" download></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection
