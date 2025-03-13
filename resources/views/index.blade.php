<!-- resources/views/index.blade.php -->
@extends('layouts.app')

<link href="{{ asset('css/index.css') }}" rel="stylesheet">

@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white">
        <span class="hero-subtitle">Usluge po Vašoj meri uz</span>
        <h1 class="hero-title">Poslovi<mark>online</mark></h1>

        <!-- Search bar -->
        <div class="search-container mt-4">
            <input type="text"
                   id="searchInput"
                   class="form-control form-control-lg"
                   placeholder="Pretražite kategorije i usluge..."
                   aria-label="Pretraga">
        </div>
    </div>
</div>

<!-- Top usluge -->
<div class="container mt-5">
    <p class="xxs mt-5">Ne propustite!</p>
    <h2 class="mb-4 top-usluge">Top ponude</h2>
    <div class="row" id="servicesContainer">
        @foreach($services as $service)
        <div class="col-md-4 mb-4 service-card"
             data-category="{{ $service->category->name }}"
             data-subcategory="{{ $service->subcategory?->name ?? 'Nema podkategorije' }}">
            <div class="card h-100 shadow">
                <!-- Slika usluge -->
                <img src="{{ asset('service/images/' . $service->serviceImages[0]['image_path']) }}"
                     class="card-img-top service-image"
                     alt="{{ $service->title }}"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250';">

                <div class="card-body d-flex flex-column">
                    <!-- Kategorija -->
                    <h6 class="service-category">{{ $service->category->name }}</h6>

                    <!-- Naslov i opis -->
                    <h5 class="card-title">{{ $service->title }}</h5>
                    <p class="card-text flex-grow-1">{{ Str::limit($service->description, 100) }}</p>

                    <!-- Ocena (zvezdice i broj) -->
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user"></i> &nbsp; {{ $service->user->firstname .' '.$service->user->lastname }} &nbsp;
                        <div class="text-warning ms-auto"> <!-- Dodali smo ms-auto za desno poravnavanje -->
                            @for ($j = 1; $j <= 5; $j++)
                                @if ($j <= rand(3, 5)) <!-- Nasumična ocena između 3 i 5 -->
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="ms-2">({{ 4 }})</small> <!-- Nasumičan broj ocena -->
                    </div>


                    <!-- Cena i dugme -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="service-price">
                            <!-- Cena -->
                            <p class="card-text">
                                <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} RSD
                            </p>
                        </div>
                        <a href="{{ route('services.show', $service->id) }}"
                           class="btn btn-service-details">
                            Detaljnije
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const serviceCards = document.querySelectorAll('.service-card');

        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();

            serviceCards.forEach(card => {
                const category = card.dataset.category.toLowerCase();
                const subcategory = card.dataset.subcategory.toLowerCase();
                const textContent = card.textContent.toLowerCase();

                const matches = category.includes(searchTerm) ||
                              subcategory.includes(searchTerm) ||
                              textContent.includes(searchTerm);

                card.style.display = matches ? 'block' : 'none';
            });
        });
    });
</script>
@endsection
