@extends('layouts.app')
<link href="{{ asset('css/show.css') }}" rel="stylesheet">
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<title>Poslovi Online | {{ $title }}</title>
@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Glavni sadržaj -->
        <div class="col-md-8">
            <!-- Naslov i osnovne informacije -->
            <h1 class="mb-4">{{ $service->title }}</h1>
            <div class="col-12 text-end">
                <span class="fw-bold">Podeli na:</span>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm rounded-circle"
                   title="Podeli na Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>

                <!-- Twitter (X) -->
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($title ?? 'Pogledajte ovo') }}"
                   target="_blank"
                   class="btn btn-outline-info btn-sm rounded-circle"
                   title="Podeli na Twitter (X)">
                    <i class="fab fa-twitter"></i>
                </a>

                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm rounded-circle"
                   title="Podeli na LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/?text={{ urlencode(($title ?? 'Pogledajte ovo') . ' ' . url()->current()) }}"
                   target="_blank"
                   class="btn btn-outline-success btn-sm rounded-circle"
                   title="Podeli na WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>

                <!-- Copy Link -->
                <button onclick="copyLink()"
                        class="btn btn-outline-secondary btn-sm rounded-circle"
                        title="Kopiraj link">
                    <i class="fas fa-link"></i>
                </button>
            </div>

            <!-- Bootstrap Toast Obaveštenje -->
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ✅ Link je uspešno kopiran!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-primary me-2">{{ $service->category->name }}</span>
                @if($service->subcategory)
                    <span class="badge bg-secondary">{{ $service->subcategory->name }}</span>
                @endif

                @auth
                <div class="ms-auto mt-3"> <!-- Ovo gura dugmad na desno -->
                    @if(Auth::user()->favorites->contains('service_id', $service->id))
                        <form action="{{ route('favorites.destroy', $service) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger ms-auto" data-bs-toggle="tooltip" title="Dodaj u omiljeno">Ukloni <i class="fas fa-heart"></i></button>
                        </form>
                    @else
                        <form action="{{ route('favorites.store', $service) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-success ms-auto" data-bs-toggle="tooltip" title="Dodaj u omiljeno">Dodaj <i class="fas fa-heart"></i></button>
                        </form>
                    @endif
                </div>
                @endauth
            </div>

            <!-- Slike usluge -->
            <div class="service-images mb-4">
                <div class="row">
                    @foreach($service->serviceImages as $image)
                        <div class="col-md-4 mb-3">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $loop->index }}">
                                <img src="{{ asset('storage/services/' . $image->image_path) }}"
                                     class="img-fluid rounded service-image"
                                     alt="Slika usluge">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modali za svaku sliku -->
            @foreach($service->serviceImages as $image)
            <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $loop->index }}" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/services/' . $image->image_path) }}"
                                 class="img-fluid"
                                 alt="Slika usluge">
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="prevImage({{ $loop->index }})">
                                    <i class="fas fa-chevron-left"></i> Prethodna
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="nextImage({{ $loop->index }})">
                                    Sledeća <i class="fas fa-chevron-right"></i>
                                </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
        <div class="col-md-8">

            <!-- Ocene usluge -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <!-- Ocena -->
                        <div class="col-md-4 text-center">
                            <div class="text-warning mb-2">
                                @for ($j = 1; $j <= 5; $j++)
                                    @if ($j <= $service->average_rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p>
                                <p>{{ $service->average_rating }}/5 ( od {{count($service->reviews) }} recenzija)</p>
                            </p>
                        </div>

                        <!-- Mogućnos reklamacije -->
                        <div class="col-md-4 text-center">
                            <i class="fas fa-clipboard-list fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">Mogućnos reklamacije</p>
                        </div>

                        <!-- Dostupnost -->
                        <div class="col-md-4 text-center">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0">Dostupno</p>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Opis usluge -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-success">Opis</h5>
                    <p>{{ $service->description }}</p>
                </div>
            </div>

            <!-- Paketi -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Prikaz poruke sa anchor ID -->
                    @if(session('success'))
                        <div id="cart-message" class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('danger'))
                        <div id="cart-message-danger" class="alert alert-danger text-center">
                            {{ session('danger') }}
                        </div>
                    @endif

                    @php
                        $cartItemCount = 0;
                        if (Auth::check()) {
                            $cartItemCount = optional(Auth::user()->cartItems)->where('service_id', $service->id)->count() ?? 0;
                        }
                    @endphp


                    <p class="choos-label">Odaberite</p>
                    <h5 class="card-title mb-4 service-package">Paket</h5>
                    <div class="row">
                        <!-- Basic paket -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <h6 class="card-title text-center package-category">Basic</h6>
                                    <!-- Prikazivanje skraćenog teksta i ikone -->
                                    <p class="text-center d-flex align-items-center">
                                        {{ Str::limit($service->basic_inclusions, 15) }}
                                        <i class="fa fa-info-circle ml-2 text-primary mt-1" data-toggle="modal" data-target="#basicInclusionsModal" style="cursor: pointer;"></i>
                                    </p>
                                    <p><strong ><i class="fas fa-credit-card text-secondary"></i> Cena:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} <i class="fas fa-euro-sign"></i></p>
                                    <p><strong><i class="fas fa-hourglass-start text-secondary"></i> Rok:</strong> {{ $service->basic_delivery_days }} dana</p>

                                    @auth
                                        @if(Auth::user()->cartItems->where('service_id', $service->id)->contains('package', 'Basic'))
                                            <form action="{{ route('cart.destroy', $service->cartItems->where('user_id', Auth::id())->where('package', 'Basic')->first()->id ?? 0) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Ukloni iz korpe"><i class="fas fa-trash"></i> Ukloni iz <i class="fas fa-shopping-cart"></i></button>
                                            </form>
                                        @elseif($cartItemCount == 0)
                                            <form action="{{ route('cart.store', ['service' => $service, 'package' => 'Basic']) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-service-choose w-100" data-bs-toggle="tooltip" title="Dodaj u korpu"><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Standard paket -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <h6 class="card-title text-center package-category">Standard</h6>
                                    <!-- Prikazivanje skraćenog teksta i ikone -->
                                    <p class="text-center d-flex align-items-center">
                                        {{ Str::limit($service->standard_inclusions, 15) }}
                                        <i class="fa fa-info-circle ml-2 text-primary mt-1" data-toggle="modal" data-target="#standardInclusionsModal" style="cursor: pointer;"></i>
                                    </p>
                                    <p><strong><i class="fas fa-credit-card text-secondary"></i> Cena:</strong> {{ number_format($service->standard_price, 0, ',', '.') }} <i class="fas fa-euro-sign"></i></p>
                                    <p><strong><i class="fas fa-hourglass-start text-secondary"></i> Rok:</strong> {{ $service->standard_delivery_days }} dana</p>

                                    @auth
                                        @if(Auth::user()->cartItems->where('service_id', $service->id)->contains('package', 'Standard'))
                                            <form action="{{ route('cart.destroy', $service->cartItems->where('user_id', Auth::id())->where('package', 'Standard')->first()->id ?? 0) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Ukloni iz korpe"><i class="fas fa-trash"></i> Ukloni iz <i class="fas fa-shopping-cart"></i></button>
                                            </form>
                                        @elseif($cartItemCount == 0)
                                            <form action="{{ route('cart.store', ['service' => $service, 'package' => 'Standard']) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-service-choose w-100" data-bs-toggle="tooltip" title="Dodaj u korpu"><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Premium paket -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <h6 class="card-title text-center package-category">Premium</h6>
                                    <!-- Prikazivanje skraćenog teksta i ikone -->
                                    <p class="text-center d-flex align-items-center">
                                        {{ Str::limit($service->premium_inclusions, 15) }}
                                        <i class="fa fa-info-circle ml-2 mt-1 text-primary" data-toggle="modal" data-target="#premiumInclusionsModal" style="cursor: pointer;"></i>
                                    </p>
                                    <p><strong><i class="fas fa-credit-card text-secondary"></i> Cena:</strong> {{ number_format($service->premium_price, 0, ',', '.') }} <i class="fas fa-euro-sign"></i></p>
                                    <p><strong><i class="fas fa-hourglass-start text-secondary"></i> Rok:</strong> {{ $service->premium_delivery_days }} dana</p>
                                    @auth
                                        @if(Auth::user()->cartItems->where('service_id', $service->id)->contains('package', 'Premium'))
                                            <form action="{{ route('cart.destroy', $service->cartItems->where('user_id', Auth::id())->where('package', 'Premium')->first()->id ?? 0) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Ukloni iz korpe"><i class="fas fa-trash"></i> Ukloni iz <i class="fas fa-shopping-cart"></i></button>
                                            </form>
                                        @elseif($cartItemCount == 0)
                                            <form action="{{ route('cart.store', ['service' => $service, 'package' => 'Premium']) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-service-choose w-100" data-bs-toggle="tooltip" title="Dodaj u korpu"><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Bootstrap Basic Inclusions Modal -->
        <div class="modal fade" id="basicInclusionsModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Detaljne Informacije</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ $service->basic_inclusions }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Standard Inclusions Modal -->
        <div class="modal fade" id="standardInclusionsModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Detaljne Informacije</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ $service->standard_inclusions }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Premium Inclusions Modal -->
        <div class="modal fade" id="premiumInclusionsModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Detaljne Informacije</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ $service->premium_inclusions }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Recenzije -->
        <div class="card mb-4" id="recenzije">
            <div class="card-body">
                <h5 class="card-title mb-4 text-success">Recenzije</h5>

                <!-- Lista recenzija -->
                @foreach($reviews as $review)
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Korisnik i ocena -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ asset('user/' . $review->user->avatar) }}"
                                     class="rounded-circle"
                                     alt="Avatar korisnika"
                                     width="50"
                                     height="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6>{{ $review->user->name }}</h6>
                                <div class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Komentar -->
                        <p class="card-text">{{ $review->comment }}</p>

                        <!-- Datum i vreme -->
                        <small class="text-secondary">
                            <i class="fas fa-clock"></i> {{ $review->created_at->format('d.m.Y H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach

                <!-- Paginacija -->
                <div class="mt-4 pagination-buttons text-center">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>

    </div>
     <!-- Sidebar sa informacijama o prodavcu -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center text-success">O prodavcu</h4>

                    <!-- Osnovne informacije -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('user/' . $service->user->avatar) }}"
                                 class="rounded-circle"
                                 alt="Avatar prodavca"
                                 width="50"
                                 height="50">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>{{ $service->user->firstname .' '.$service->user->lastname }}</h6>
                            @php
                                $sellerLevels = [
                                    0 => 'Kupac',
                                    1 => 'Novi prodavac',
                                    2 => 'Level 1 prodavac',
                                    3 => 'Level 2 prodavac',
                                    4 => 'Top Rated prodavac',
                                ];

                                $sellerLevelName = $sellerLevels[$service->user->seller_level] ?? 'Nepoznat nivo';
                            @endphp
                            <small class="text-center">{{ $sellerLevelName }}</small>
                        </div>
                    </div>

                    <div class="text-warning ms-auto mb-4">
                            <p class="text-secondary">Ukupno ponuda: {{ $userServiceCount }}</p>
                            <p class="text-secondary">Ukupna ocena:
                                @if ($service->user->stars > 0)
                                    @for ($j = 1; $j <= $service->user->stars; $j++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                @elseif ($service->user->stars == 0)
                                    <p>No stars available</p>
                                @endif

                                <small class="ms-2">({{ $service->user->stars }})</small>
                            </p>
                    </div>

                    @auth
                        <!-- Dugme za kontakt -->
                        <a href="#" class="btn btn-success w-100">
                            <i class="fas fa-envelope me-2"></i>Kontaktirajte prodavca
                        </a>
                    @endauth
                </div>
            </div>
        </div>
</div>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#cart-message') {
        const element = document.getElementById('cart-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Automatsko sakrivanje poruke
    const messageElement = document.getElementById('cart-message');
    if (messageElement) {
        // Dodajemo klasu za tranziciju
        messageElement.classList.add('fade-out');

        // Sakrijemo poruku nakon 5 sekundi
        setTimeout(() => {
            messageElement.classList.add('hide');

            // Uklonimo element iz DOM-a nakon što animacija završi
            setTimeout(() => {
                messageElement.remove();
            }, 1000); // Vreme trajanja animacije (1s)
        }, 5000); // Poruka će početi da nestaje nakon 5s
    }

    // Automatsko sakrivanje poruke prilikom brisanja
    const messageElementDanger = document.getElementById('cart-message-danger');
    if (messageElementDanger) {
        // Dodajemo klasu za tranziciju
        messageElementDanger.classList.add('fade-out');

        // Sakrijemo poruku nakon 5 sekundi
        setTimeout(() => {
            messageElementDanger.classList.add('hide');

            // Uklonimo element iz DOM-a nakon što animacija završi
            setTimeout(() => {
                messageElementDanger.remove();
            }, 1000); // Vreme trajanja animacije (1s)
        }, 5000); // Poruka će početi da nestaje nakon 5s
    }

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

    // Selektuj sve paginacione linkove
    document.querySelectorAll('nav[role="navigation"] a').forEach(function (link) {
        // Proveri da li href već ima hash deo
        if (!link.href.includes("#recenzije")) {
            link.href += "#recenzije";
        }
    });
});

function prevImage(currentIndex) {
    const prevIndex = currentIndex - 1;
    if (prevIndex >= 0) {
        const currentModal = bootstrap.Modal.getInstance(document.getElementById(`imageModal${currentIndex}`));
        currentModal.hide();

        const prevModal = new bootstrap.Modal(document.getElementById(`imageModal${prevIndex}`));
        prevModal.show();
    }
}

function nextImage(currentIndex) {
    const nextIndex = currentIndex + 1;
    if (nextIndex < {{ count($service->serviceImages) }}) {
        const currentModal = bootstrap.Modal.getInstance(document.getElementById(`imageModal${currentIndex}`));
        currentModal.hide();

        const nextModal = new bootstrap.Modal(document.getElementById(`imageModal${nextIndex}`));
        nextModal.show();
    }
}
</script>

<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(function() {
            let copyToast = new bootstrap.Toast(document.getElementById('copyToast'));
            copyToast.show();
        }, function(err) {
            console.error("Greška pri kopiranju: ", err);
        });
    }
</script>
@endsection
