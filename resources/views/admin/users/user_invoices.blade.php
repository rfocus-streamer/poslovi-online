<style>
    .invoice-card {
        transition: transform 0.2s;
        border: 1px solid rgba(0,0,0,0.125);
        border-radius: 0.5rem;
    }

    .invoice-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    @media (max-width: 575.98px) {
        .card-footer .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .card-footer .btn i {
            margin-right: 0.2rem;
        }
    }
</style>
<div class="container-fluid">
    <h4 class="mb-3">{{ $user->firstname }} {{ $user->lastname }}</h4>

    @if($invoices->isEmpty())
        <div class="alert alert-info">
            Korisnik nema generisanih računa.
        </div>
    @else
        <!-- Desktop table view -->
        <div class="d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Račun</th>
                            <th>Kreiran</th>
                            <th>Opis</th>
                            <th>Status</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>#{{ $invoice->number }}</td>
                                <td>{{ $invoice->issue_date->format('d.m.Y') }}</td>
                                @foreach($invoice->items as $item)
                                    <td>
                                        @if(strlen($item['description']) > 25)
                                            {{ \Illuminate\Support\Str::limit($item['description'], 25) }}
                                        @else
                                            {{ $item['description'] }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>{{ ucfirst($invoice->status) }}</td>
                                <td>
                                    <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}"
                                       class="btn btn-sm btn-primary" target="_blank" title="Pregledaj PDF">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}?download=1"
                                       class="btn btn-sm btn-danger" target="_blank" title="Preuzmi PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile card view -->
        <div class="d-md-none">
            @foreach($invoices as $index => $invoice)
                <div class="card mb-3 invoice-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">#{{ $invoice->number }}</span>
                        <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Redni broj:</small>
                                <div>{{ $index + 1 }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Kreiran:</small>
                                <div>{{ $invoice->issue_date->format('d.m.Y') }}</div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Opis:</small>
                            <div>
                                @foreach($invoice->items as $item)
                                    @if(strlen($item['description']) > 40)
                                        {{ \Illuminate\Support\Str::limit($item['description'], 40) }}
                                    @else
                                        {{ $item['description'] }}
                                    @endif
                                    @if(!$loop->last)<br>@endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-around">
                            <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}"
                               class="btn btn-sm btn-primary" target="_blank" title="Pregledaj PDF">
                                <i class="fas fa-eye me-1"></i> Pregled
                            </a>
                            <a href="{{ route('invoice.download', ['id' => $invoice->id]) }}?download=1"
                               class="btn btn-sm btn-danger" target="_blank" title="Preuzmi PDF">
                                <i class="fas fa-download me-1"></i> Preuzmi
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $invoices->appends(request()->except('page'))->links() }}
        </div>
    @endif
</div>
