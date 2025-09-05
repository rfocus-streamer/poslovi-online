<div class="modal fade" id="affiliateStatsModal" tabindex="-1" aria-labelledby="affiliateStatsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100 text-center">
                            <h5 class="modal-title" id="affiliateStatsModalLabel">
                                <i class="fas fa-users"></i> Tvoji affiliate korisnici
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">Ukupno referala</h6>
                                        <h3 class="card-title">{{ Auth::user()->referrals->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">Ukupna zarada</h6>
                                        <h3 class="card-title">{{ number_format(Auth::user()->commissionsEarned->sum('amount'), 2) }}€</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">Aktivnih paketa</h6>
                                        <h3 class="card-title">{{ Auth::user()->referrals->filter(function($user) { return $user->package; })->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop -->
                        <div class="d-none d-md-table table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Registrovan</th>
                                        <th>Paket</th>
                                        <th>Cena</th>
                                        <th>Zarada</th>
                                        <th>Aktiviran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(Auth::user()->referrals as $key => $referral)

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{ $key +1 }}
                                            </div>
                                        </td>
                                        <td>{{ $referral->created_at->format('d.m.Y.') }}</td>
                                        <td>
                                            @if($referral->package)
                                                {{ $referral->package->name }}
                                            @else
                                               -
                                            @endif
                                        </td>
                                        <td>
                                            @if($referral->package)
                                                {{ number_format($referral->package->price, 2) }}€
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-success fw-bold">
                                            @php
                                                $commission = $referral->referralCommissions->sum('amount');
                                            @endphp
                                            {{ $commission ? number_format($commission, 2).'€' : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $referral->package ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $referral->referralCommissions->isNotEmpty() ? $referral->referralCommissions->first()->created_at->format('d.m.Y H:i') : 'Neaktivan' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nema registrovanih korisnika</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile & Tablet Cards -->
                        <div class="d-md-none">
                            @forelse(Auth::user()->referrals as $key => $referral)
                            <div class="card mb-3 subscription-card" data-id="{{ $referral->id }}">
                                <div class="card-header btn-poslovi-green text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Referal #{{ $key+1 }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Registrovan</small>
                                            <div>{{ $referral->created_at->format('d.m.Y.') }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Paket</small>
                                            <div>
                                                @if($referral->package)
                                                    {{ $referral->package->name }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Cena</small>
                                            <div>
                                                @if($referral->package)
                                                    {{ number_format($referral->package->price, 2) }}€
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Zarada</small>
                                            <div class="text-success fw-bold">
                                                @php
                                                    $commission = $referral->referralCommissions->sum('amount');
                                                @endphp
                                                {{ $commission ? number_format($commission, 2).'€' : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">Aktiviran</small>
                                            <div>
                                                <span class="badge {{ $referral->package ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $referral->referralCommissions->isNotEmpty() ? $referral->referralCommissions->first()->created_at->format('d.m.Y H:i') : 'Neaktivan' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="card mb-3 subscription-card">
                                <div class="card-body text-center">
                                    <p class="text-muted">Nema registrovanih korisnika</p>
                                </div>
                            </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>
        </div>
