@extends('layouts.app')

<link href="{{ asset('css/show.css') }}" rel="stylesheet">

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
                <div id="profile-message" class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="profile-message-danger" class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card-header text-center mb-4"
                style="border-color: #198754; border: 2px solid #198754;">
                <i class="fas fa-calendar-alt"></i> Mesečni plan aktivacije paketa !
            </div>


             <div class="text-center mb-4">
                <small class="text-secondary">Mesto gde veštine postaju prihod!</small><br>
                <small class="text-secondary">Ponudi. Poveži se. Zaradi.</small>
            </div>


            <div class="card mb-1">
                <div class="card-body">
                    <div class="row">

                    @foreach($packages as $key => $package)
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <form method="POST" action="{{ route('package.activate', $package) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH') <!-- Dodajemo PATCH metod jer forma koristi POST -->
                                        <h6 class="card-title text-center package-category"><i class="{{ $key == 0 ? 'fas fa-box text-primary' : ($key == 1 ? 'fas fa-gift text-success' : 'fas fa-gem text-warning') }}"></i> {{$package->name}}</h6>
                                        <div class="text-center mb-5">
                                            <p>{{$package->description}}</p>
                                            <p><strong>Cena: </strong>{{$package->price}} <i class="fas fa-euro-sign"></i></p>
                                        </div>

                                        @if(Auth::user()->deposits >= $package->price)
                                            @if(Auth::user()->package)
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
                                            <a href="{{ route('deposit.form') }}" class="btn btn-warning ms-auto w-100 text-white" data-bs-toggle="tooltip" title="Deponuj novac"> <i class="fas fa-credit-card"></i> Deponuj novac
                                            </a>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <small><i class="fas fa-info-circle"></i> Promena mesečne pretplate je moguća samo ka višem paketu. Smanjenje nivoa pretplate nije dozvoljeno.</small>
                    </div>
                </div>
            </div>
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

                        @if(Auth::user()->package)
                            <div class="package mb-3">
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

                        <div class="text-warning mb-2">
                            <a href="{{ route('deposit.form') }}" class="btn btn-outline-warning ms-auto w-100" data-bs-toggle="tooltip" title="Deponuj novac"> <i class="fas fa-credit-card"></i>
                            </a>
                            <p class="text-center text-secondary">Deponuj novac</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!--  Modal -->


    </div>
</div>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#profile-message') {
        const element = document.getElementById('profile-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('profile-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('profile-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }
});
</script>

@endsection
