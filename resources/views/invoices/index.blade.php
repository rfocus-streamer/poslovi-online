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
    <div class="d-md-none">
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
