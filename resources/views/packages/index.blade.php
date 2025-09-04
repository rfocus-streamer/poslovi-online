@extends('layouts.app')

<link href="{{ asset('css/default.css') }}" rel="stylesheet">

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

            <!-- Prikaz poruke sa anchor ID -->
            @if(session('success'))
                <div id="package-message" class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="package-message-danger" class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card-header text-center mb-4"
                style="border-color: #198754; border: 2px solid #198754;">
                <i class="fas fa-calendar-alt"></i> Godišnji ili mesečni plan aktivacije paketa !
            </div>


             <div class="text-center mb-4">
                <small class="text-secondary">Mesto gde veštine postaju prihod!</small><br>
                <small class="text-secondary">Ponudi. Poveži se. Zaradi.</small>
            </div>

           <!--  Dekstop -->
            <div class="card d-none d-md-flex mb-1">
                <div class="card-body">
                    <div class="row">

                    @php
                        $packageExpired = \Carbon\Carbon::parse(Auth::user()->package_expires_at)->isPast();
                    @endphp

                    @foreach($packages as $key => $package)
                        <div class="col-md-4 mb-4">  <!-- Dodajemo mb-4 za razmak između redova -->
                            <div class="card h-100 position-relative">
                                <!-- Ukoso u gornjem levom uglu - Godisnji popust -->
                                @if($package->duration == 'yearly')
                                    <div class="position-absolute top-0 start-0 p-1 bg-danger text-white" style="transform: rotate(-45deg); transform-origin: top left; font-size: 0.75rem; margin-top: 35px; margin-left: -10px;">
                                        <strong>20% popust</strong>
                                    </div>
                                @endif

                                <div class="card-body card-text">
                                    <form method="POST" action="{{ route('package.activate', $package) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH') <!-- Dodajemo PATCH metod jer forma koristi POST -->
                                         <!-- Ikona i naziv paketa -->
                                        <h6 class="card-title text-center package-category">
                                            <i class="
                                                @if($key % 3 == 0)
                                                    fas fa-box text-primary
                                                @elseif($key % 3 == 1)
                                                    fas fa-gift text-success
                                                @else
                                                    fas fa-gem text-warning
                                                @endif
                                            "></i>
                                            {{$package->name}}
                                        </h6>
                                        <div class="text-center mb-5">
                                            <p>{{$package->description}}</p>
                                            <p>Plan:
                                                @if($package->duration == 'monthly')
                                                    Mesečni
                                                @elseif($package->duration == 'yearly')
                                                    Godišnji
                                                @endif
                                            </p>
                                            <p><strong>Cena: </strong>{{$package->price}} <i class="fas fa-euro-sign"></i></p>
                                        </div>

                                        @if(Auth::user()->deposits >= $package->price)
                                            @if(Auth::user()->package and !$packageExpired)
                                                @if(Auth::user()->package->price < $package->price)
                                                    <!-- Submit Button -->
                                                    <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                        <i class="fa fas fa-shopping-cart me-1"></i> Kupi
                                                    </button>
                                                @endif

                                                @if(Auth::user()->package->id === $package->id and !$packageExpired)
                                                   <button type="button" class="btn text-white w-100 btn-secondary">
                                                        <i class="fa fa-check-circle me-1"></i> Kupljen
                                                    </button>
                                                @endif
                                            @else
                                                <!-- Submit Button -->
                                                <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                    <i class="fa fas fa-shopping-cart me-1"></i> Kupi
                                                </button>
                                            @endif
                                        @else
                                            @if(Auth::user()->package)
                                                @if(Auth::user()->deposits >= $package->price)
                                                    <!-- Submit Button -->
                                                    <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                        <i class="fa fas fa-shopping-cart me-1"></i> Kupi
                                                    </button>
                                                @elseif(Auth::user()->package->price <= $package->price and Auth::user()->package->id != $package->id)
                                                    <a href="{{ route('subscriptions.index', ['package_id' => $package->id]) }}"    class="btn ms-auto w-100 text-white" data-bs-toggle="tooltip" title="Pretplati se" style="background-color: #198754">
                                                        <i class="fas fa-credit-card"></i> Pretplati se
                                                    </a>
                                                @endif

                                                @if(Auth::user()->package->id === $package->id and !$packageExpired)
                                                   <button type="button" class="btn text-white w-100 btn-secondary">
                                                        <i class="fa fa-check-circle me-1"></i> Kupljen
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('subscriptions.index', ['package_id' => $package->id]) }}" class="btn ms-auto w-100 text-white" data-bs-toggle="tooltip" title="Pretplati se" style="background-color: #198754">
                                                        <i class="fas fa-credit-card"></i> Pretplati se
                                                </a>
                                            @endif
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <small><i class="fas fa-info-circle"></i> Promena godišnje ili mesečne pretplate je moguća samo ka višem paketu. Smanjenje nivoa pretplate nije dozvoljeno.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile -->
        <div class="d-md-none mb-1">
             <div class="card mb-3">
                <small class="ml-2"><i class="fas fa-info-circle"></i> Promena godišnje ili mesečne pretplate je moguća samo ka višem paketu. Smanjenje nivoa pretplate nije dozvoljeno.</small>
            </div>
            @foreach($packages as $key => $package)
                <div class="mb-4">  <!-- Dodajemo mb-4 za razmak između redova -->
                    <div class="card position-relative">
                        <!-- Ukoso u gornjem levom uglu - Godisnji popust -->
                        @if($package->duration == 'yearly')
                            <div class="position-absolute top-0 start-0 p-1 bg-danger text-white" style="transform: rotate(-45deg); transform-origin: top left; font-size: 0.75rem; margin-top: 35px; margin-left: -10px;">
                                        <strong>20% popust</strong>
                            </div>
                        @endif

                        <div class="card-body card-text mb-1">
                            <form method="POST" action="{{ route('package.activate', $package) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH') <!-- Dodajemo PATCH metod jer forma koristi POST -->
                                    <!-- Ikona i naziv paketa -->
                                    <h6 class="card-title text-center package-category">
                                            <i class="
                                                @if($key % 3 == 0)
                                                    fas fa-box text-primary
                                                @elseif($key % 3 == 1)
                                                    fas fa-gift text-success
                                                @else
                                                    fas fa-gem text-warning
                                                @endif
                                            "></i>
                                            {{$package->name}}
                                    </h6>
                                    <div class="text-center mb-5">
                                            <p>{{$package->description}}</p>
                                            <p>Plan:
                                                @if($package->duration == 'monthly')
                                                    Mesečni
                                                @elseif($package->duration == 'yearly')
                                                    Godišnji
                                                @endif
                                            </p>
                                            <p><strong>Cena: </strong>{{$package->price}} <i class="fas fa-euro-sign"></i></p>
                                    </div>

                                    @if(Auth::user()->deposits >= $package->price)
                                        @if(Auth::user()->package and !$packageExpired)
                                            @if(Auth::user()->package->price < $package->price)
                                                <!-- Submit Button -->
                                                <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                        <i class="fa fas fa-shopping-cart me-1"></i> Kupi
                                                </button>
                                            @endif

                                            @if(Auth::user()->package->id === $package->id)
                                                <button type="button" class="btn text-white w-100 btn-secondary">
                                                        <i class="fa fa-check-circle me-1"></i> Kupljen
                                                </button>
                                            @endif
                                        @else
                                            <!-- Submit Button -->
                                            <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                    <i class="fa fas fa-shopping-cart me-1"></i> Kupi
                                            </button>
                                        @endif
                                    @else
                                        @if(Auth::user()->package)
                                            @if(Auth::user()->deposits >= $package->price)
                                                <!-- Submit Button -->
                                                <button type="submit" class="btn text-white w-100" style="background-color: #198754">
                                                        <i class="fa fas fa-shopping-cart me-1"></i> Kupi
                                                </button>
                                            @elseif(Auth::user()->package->price <= $package->price and Auth::user()->package->id != $package->id)
                                                <a href="{{ route('subscriptions.index', ['package_id' => $package->id]) }}"    class="btn ms-auto w-100 text-white" data-bs-toggle="tooltip" title="Pretplati se" style="background-color: #198754">
                                                        <i class="fas fa-credit-card"></i> Pretplati se
                                                </a>
                                            @endif

                                            @if(Auth::user()->package->id === $package->id)
                                                <button type="button" class="btn text-white w-100 btn-secondary">
                                                        <i class="fa fa-check-circle me-1"></i> Kupljen
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ route('subscriptions.index', ['package_id' => $package->id]) }}" class="btn ms-auto w-100 text-white" data-bs-toggle="tooltip" title="Pretplati se" style="background-color: #198754">
                                                        <i class="fas fa-credit-card"></i> Pretplati se
                                                </a>
                                        @endif
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>


        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
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

                        @if(Auth::user()->package and !$packageExpired)
                            <div class="package">
                                <h6 class="text-secondary">
                                    @if(Auth::user()->package->duration  === 'yearly')
                                        <i class="fas fa-calendar-alt text-secondary"></i> Godišnji plan:
                                    @else
                                        <i class="fas fa-calendar-alt text-secondary"></i> Mesečni plan:
                                    @endif

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

                                <div class="text-warning mb-3 modal-header">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#packageStatsModal">
                                        <i class="fas fa-chart-line me-1"></i> Statistika aktiviranih paketa
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">
                                <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemaš aktivan paket!
                                </div>

                                <div class="text-warning mb-2">
                                    <p class="text-center text-secondary">Odaberi paket</p>
                                </div>
                        @endif


                        <h6 class="text-secondary">
                            <i class="fas fa-user"></i> Nivo prodavca: <strong class="text-success">{{ $sellerLevelName }}</strong>
                        </h6>
                        <h6 class="text-secondary">
                            <i class="fas fa-credit-card"></i> Ukupna mesečna zarada: <strong class="text-success">{{ number_format($totalEarnings, 2) }} <i class="fas fa-euro-sign"></i></strong>
                        </h6>


                        <h6 class="text-secondary">
                            <i class="fas fa-credit-card"></i> Trenutni depozit: <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} <i class="fas fa-euro-sign"></i></strong>
                        </h6>

                        @if(Auth::user()->role === 'buyer')
                            <div class="text-warning mb-2">
                                <a href="{{ route('deposit.form') }}" class="btn btn-outline-warning ms-auto w-100" data-bs-toggle="tooltip" title="Deponuj novac"> <i class="fas fa-credit-card"></i>
                                </a>
                                <p class="text-center text-secondary">Deponuj novac</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
        <!-- Package Stats Modal -->
        <div class="modal fade" id="packageStatsModal" tabindex="-1" aria-labelledby="packageStatsModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="w-100 text-center">
                            <h5 class="modal-title" id="packageStatsModalLabel">
                                <i class="fas fa-calendar-alt"></i> Statistika aktiviranih paketa
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Desktop -->
                    <div class="d-none d-md-flex modal-body">
                        <div class="table-responsive" id="subscriptions-table">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Paket</th>
                                        <th>Cena</th>
                                        <th>Plan</th>
                                        <th>Aktiviran</th>
                                        <th>Ističe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $subscription)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $subscription->package->name }}</td>
                                            <td>{{ $subscription->amount }} €</td>
                                            <td>
                                                @if($subscription->package->duration == 'monthly')
                                                    Mesečni
                                                @elseif($subscription->package->duration == 'yearly')
                                                    Godišnji
                                                @endif
                                            </td>
                                            <td>{{ Carbon\Carbon::parse($subscription->created_at)->format('d.m.Y') }}</td>
                                            <td>{{ Carbon\Carbon::parse($subscription->expires_at)->format('d.m.Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginacija -->
                        <div class="d-flex justify-content-center pagination-buttons" id="pagination-links">
                            {{ $orders->links() }}
                        </div>
                    </div>

                   <!-- Mobile & Tablet cards -->
                    <div class="d-md-none">
                        @foreach($orders as $subscription)
                            <div class="card mb-3 subscription-card" data-id="{{ $subscription->id }}">
                                <div class="card-header btn-poslovi-green text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $subscription->package->name }}</span>
                                        <span class="badge bg-light text-dark">
                                            {{ $subscription->amount }} €
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Plan</small>
                                            <div>
                                                @if($subscription->package->duration == 'monthly')
                                                    Mesečni
                                                @elseif($subscription->package->duration == 'yearly')
                                                    Godišnji
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Aktiviran</small>
                                            <div>{{ Carbon\Carbon::parse($subscription->created_at)->format('d.m.Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">Ističe</small>
                                        <div>{{ Carbon\Carbon::parse($subscription->expires_at)->format('d.m.Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        // Poziv funkcije za prevođenje teksta paginacije
        translatePaginationText();

        // Kada korisnik klikne na link paginacije
        $('#pagination-links').on('click', 'a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');  // Preuzmi URL sa linka za paginaciju

            // Napravi AJAX poziv
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    // Ažuriraj sadržaj tabele i paginacije
                    $('#subscriptions-table').html($(response).find('#subscriptions-table').html());
                    $('#pagination-links').html($(response).find('#pagination-links').html());
                    // Poziv funkcije za prevođenje teksta paginacije
                    translatePaginationText();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data: ", error);
                }
            });
        });
    });

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

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#package-message') {
        const element = document.getElementById('package-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('package-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('package-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }
});
</script>

@endsection
