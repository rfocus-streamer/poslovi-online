@extends('layouts.app')
<link href="{{ asset('css/show.css') }}" rel="stylesheet">
@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Glavni sadržaj -->
        <div class="col-md-8">
            <!-- Naslov i osnovne informacije -->
            <h1 class="mb-4">{{ $service->title }}</h1>
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-primary me-2">{{ $service->category->name }}</span>
                @if($service->subcategory)
                    <span class="badge bg-secondary">{{ $service->subcategory->name }}</span>
                @endif
                <button class="btn btn-outline-dark ms-auto" data-bs-toggle="tooltip" title="Dodaj u omiljeno"><i class="fas fa-heart"></i></button>
            </div>

            <!-- Slike usluge -->
            <div class="service-images mb-4">
                <div class="row">
                    @foreach($service->serviceImages as $image)
                        <div class="col-md-4 mb-3">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $loop->index }}">
                                <img src="{{ asset('service/images/' . $image->image_path) }}"
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
                            <img src="{{ asset('service/images/' . $image->image_path) }}"
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

            <!-- Opis usluge -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <!-- Ocena -->
                        <div class="col-md-4 text-center">
                            <div class="text-warning mb-2">
                                @for ($j = 1; $j <= 5; $j++)
                                    @if ($j <= rand(3, 5)) <!-- Nasumična ocena između 3 i 5 -->
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p>
                                <p>({{ count($service->reviews) }} ocena)</p>
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

            <!-- Paketi -->
            <div class="card mb-4">
                <div class="card-body">
                    <p class="choos-label">Odaberite</p>
                    <h5 class="card-title mb-4 service-package">Paket</h5>
                    <div class="row">
                        <!-- Basic paket -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <h6 class="card-title text-center package-category">Basic</h6>
                                    <p class="text-muted">{{ $service->basic_inclusions }}</p>
                                    <p><strong ><i class="fas fa-credit-card text-secondary"></i> Cena:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} RSD</p>
                                    <p><strong><i class="fas fa-hourglass-start text-secondary"></i> Rok:</strong> {{ $service->basic_delivery_days }} dana</p>
                                    <button class="btn btn-service-choose w-100" data-bs-toggle="tooltip" title="Dodaj u korpu"><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                </div>
                            </div>
                        </div>

                        <!-- Standard paket -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <h6 class="card-title text-center package-category">Standard</h6>
                                    <p class="text-muted">{{ $service->standard_inclusions }}</p>
                                    <p><strong><i class="fas fa-credit-card text-secondary"></i> Cena:</strong> {{ number_format($service->standard_price, 0, ',', '.') }} RSD</p>
                                    <p><strong><i class="fas fa-hourglass-start text-secondary"></i> Rok:</strong> {{ $service->standard_delivery_days }} dana</p>
                                    <button class="btn btn-service-choose w-100" data-bs-toggle="tooltip" title="Dodaj u korpu"><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                </div>
                            </div>
                        </div>

                        <!-- Premium paket -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body card-text">
                                    <h6 class="card-title text-center package-category">Premium</h6>
                                    <p class="text-muted">{{ $service->premium_inclusions }}</p>
                                    <p><strong><i class="fas fa-credit-card text-secondary"></i> Cena:</strong> {{ number_format($service->premium_price, 0, ',', '.') }} RSD</p>
                                    <p><strong><i class="fas fa-hourglass-start text-secondary"></i> Rok:</strong> {{ $service->premium_delivery_days }} dana</p>
                                    <button class="btn btn-service-choose w-100" data-bs-toggle="tooltip" title="Dodaj u korpu"><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Recenzije -->
        <div class="card mb-4">
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
                            <h6>{{ $service->user->name }}</h6>
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

                    <!-- Dugme za kontakt -->
                    <a href="#" class="btn btn-success w-100">
                        <i class="fas fa-envelope me-2"></i>Kontaktirajte prodavca
                    </a>
                </div>
            </div>
        </div>
</div>

<script type="text/javascript">
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
@endsection
