@extends('layouts.app')

<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<title>Poslovi Online | Računi</title>

@section('content')

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
    @endif
</div>

@endsection
