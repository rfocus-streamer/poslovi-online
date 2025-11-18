<!-- resources/views/index.blade.php -->
@extends('layouts.app')

<link href="{{ asset('css/index.css') }}" rel="stylesheet">
<style type="text/css">
/* Stil za loading spinner */
#loading-spinner {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255, 255, 255, 0.9);
    padding: 10px 20px;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
}

/* Stil za "nema više rezultata" */
#no-more-results {
    text-align: center;
    padding: 20px;
    color: #666;
}

.service-card .card {
    position: relative;
}

.service-card .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
    cursor: pointer;
}

.service-card .card a:not(.stretched-link) {
    position: relative;
    z-index: 2;
}
</style>

@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white mt-2">
        <span class="hero-subtitle">Tvoja online platforma za sigurno poslovanje</span>
        <h1 class="hero-title mt-1">Poslovi<mark>online</mark></h1>

        <!-- Search bar -->
        <div class="search-container mt-4">
               <form action="{{ route('home') }}" method="GET">
                <input type="text"
                       name="search"
                       id="searchInput"
                       class="form-control form-control-lg"
                       placeholder="Pretražite kategorije i usluge..."
                       value="{{ (isset($searchTerm) && empty($searchCategory)) ? $searchTerm : '' }}">
                <button type="submit" class="btn btn-primary mt-2" style="display: none;">Pretraži</button>
            </form>
        </div>
    </div>
</div>

@isset($searchTerm)
    <!-- Prikaz rezultata pretrage -->
    <div class="container mt-5">
        @isset($searchCategory)
            <p class="xxs mt-5">{{ $searchCategory }}</p>
        @else
            <p class="xxs mt-5">Prikaz rezultata</p>
        @endisset
        <h2 class="mb-4 top-usluge">{{ $searchTerm }}</h2>
        <div class="row">
            @foreach($services as $service)
                <div class="col-md-4 mb-4 service-card"
                 data-category="{{ $service->category->name }}"
                 data-subcategory="{{ $service->subcategory?->name ?? 'Nema podkategorije' }}">
                <div class="card h-100 shadow">
                    <!-- Stretched-link na glavni anchor -->
                    <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}" class="stretched-link"></a>
                    <!-- Slika usluge -->
                    @if($service->serviceImages->count())
                        <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}">
                            <img src="{{ asset('storage/services/' . $service->serviceImages[0]['image_path']) }}"
                                class="card-img-top service-image"
                                alt="{{ $service->title }}"
                                onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250';">
                        </a>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <!-- Kategorija -->
                        <h6 class="service-category">{{ $service->category->name }}</h6>

                        <!-- Naslov i opis -->
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text flex-grow-1">{{ Str::limit($service->description, 100) }}</p>

                        <!-- Ocena (zvezdice i broj) -->
                        <div class="d-flex align-items-center mb-2">
                            <img src="{{ Storage::url('user/' . $service->user->avatar) }}"
                                         alt="Avatar" class="rounded-circle avatar-img" width="30" height="30"> &nbsp; {{ $service->user->firstname .' '.$service->user->lastname }} &nbsp;
                            <div class="text-warning ms-auto"> <!-- Dodali smo ms-auto za desno poravnavanje -->
                                @for ($j = 1; $j <= 5; $j++)
                                    @if ($j <= $service->average_rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <small class="ms-2">({{ $service->average_rating }})</small> <!-- Nasumičan broj ocena -->
                        </div>


                        <!-- Cena i dugme -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="service-price">
                                <!-- Cena -->
                                <p class="card-text">
                                    <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} <i class="fas fa-euro-sign"></i>
                                </p>
                            </div>
                            <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}"
                               class="btn btn-service-details">
                                Detaljnije
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginacija -->
        <div class="d-flex justify-content-center mt-4">
            {{ $services->appends(['search' => $searchTerm])->links() }}
        </div>
    </div>
@else

<!-- Top usluge -->
<div class="container mt-5">
    <p class="xxs mt-5">Najbolji prodavci, najtraženije usluge!</p>
    <h2 class="mb-4 top-usluge">Top ponude</h2>
    <div class="row" id="servicesContainer">
        @foreach($topServices as $service)
        <div class="col-md-4 mb-4 service-card"
             data-category="{{ $service->category->name }}"
             data-subcategory="{{ $service->subcategory?->name ?? 'Nema podkategorije' }}">
            <div class="card h-100 shadow">
                <!-- Stretched-link na glavni anchor -->
                <a href="{{ route('services.show', [$service->id, 'slug' => Str::slug($service->title)]) }}" class="stretched-link"></a>

                <!-- Slika usluge -->
                @if($service->serviceImages->count())
                    <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}">
                        <img src="{{ asset('storage/services/' . $service->serviceImages[0]['image_path']) }}"
                            class="card-img-top service-image"
                            alt="{{ $service->title }}"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250';">
                    </a>
                @endif

                <div class="card-body d-flex flex-column">
                    <!-- Kategorija -->
                    <h6 class="service-category">{{ $service->category->name }}</h6>

                    <!-- Naslov i opis -->
                    <h5 class="card-title">{{ $service->title }}</h5>
                    <p class="card-text flex-grow-1">{{ Str::limit($service->description, 100) }}</p>

                    <!-- Ocena (zvezdice i broj) -->
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ Storage::url('user/' . $service->user->avatar) }}"
                                     alt="Avatar" class="rounded-circle avatar-img" width="30" height="30"> &nbsp; {{ $service->user->firstname .' '.$service->user->lastname }} &nbsp;
                        <div class="text-warning ms-auto"> <!-- Dodali smo ms-auto za desno poravnavanje -->
                            @for ($j = 1; $j <= 5; $j++)
                                @if ($j <= $service->average_rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="ms-2">({{ $service->average_rating }})</small> <!-- Nasumičan broj ocena -->
                    </div>


                    <!-- Cena i dugme -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="service-price">
                            <!-- Cena -->
                            <p class="card-text">
                                <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} <i class="fas fa-euro-sign"></i>
                            </p>
                        </div>
                        <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}"
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


<!-- Poslednje ponude -->
<div class="container mt-5">
    <p class="xxs mt-5">Najnovije ponude su stigle!</p>
    <h2 class="mb-4 top-usluge">Pogledaj šta je novo i dostupno.</h2>
    <div class="row" id="servicesContainer">
        @foreach($lastServices as $service)
        <div class="col-md-4 mb-4 service-card"
             data-category="{{ $service->category->name }}"
             data-subcategory="{{ $service->subcategory?->name ?? 'Nema podkategorije' }}">
            <div class="card h-100 shadow">
                <!-- Stretched-link na glavni anchor -->
                <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}" class="stretched-link"></a>
                <!-- Slika usluge -->
                @if($service->serviceImages->count())
                    <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}">
                        <img src="{{ asset('storage/services/' . $service->serviceImages[0]['image_path']) }}"
                            class="card-img-top service-image"
                            alt="{{ $service->title }}"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250';">
                    </a>
                @endif

                <div class="card-body d-flex flex-column">
                    <!-- Kategorija -->
                    <h6 class="service-category">{{ $service->category->name }}</h6>

                    <!-- Naslov i opis -->
                    <h5 class="card-title">{{ $service->title }}</h5>
                    <p class="card-text flex-grow-1">{{ Str::limit($service->description, 100) }}</p>

                    <!-- Ocena (zvezdice i broj) -->
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ Storage::url('user/' . $service->user->avatar) }}"
                                     alt="Avatar" class="rounded-circle  avatar-img" width="30" height="30"> &nbsp; {{ $service->user->firstname .' '.$service->user->lastname }} &nbsp;
                        <div class="text-warning ms-auto"> <!-- Dodali smo ms-auto za desno poravnavanje -->
                            @for ($j = 1; $j <= 5; $j++)
                                @if ($j <= $service->average_rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="ms-2">({{ $service->average_rating }})</small> <!-- Nasumičan broj ocena -->
                    </div>


                    <!-- Cena i dugme -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="service-price">
                            <!-- Cena -->
                            <p class="card-text">
                                <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} <i class="fas fa-euro-sign"></i>
                            </p>
                        </div>
                        <a href="{{ route('services.show', ['id' => $service->id, 'slug' => Str::slug($service->title)]) }}"
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
@endisset

<!-- Infinite scroll container -->
<div id="infinite-scroll-container" class="container mt-1"></div>

<!-- Loading spinner -->
<div id="loading-spinner" class="text-center my-5" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Učitavanje još ponuda...</p>
</div>

<!-- No more results -->
<div id="no-more-results" class="text-center my-5" style="display: none;">
    <p>Nema više ponuda za prikaz</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let page = 1;
    let loading = false;
    const container = document.getElementById('infinite-scroll-container');
    const spinner = document.getElementById('loading-spinner');
    const noMoreResults = document.getElementById('no-more-results');
    const footer = document.querySelector('footer');
    const loadDelay = 800;
    let lastScrollPosition = 0;

    // Proveravamo da li je u toku pretraga
    const isSearchActive = window.location.search.includes('search=');

    // Helper functions
    const createServiceCard = (service) => `
        <div class="col-md-4 mb-4 service-card"
             data-category="${service.category}"
             data-subcategory="${service.subcategory}">
            <div class="card h-100 shadow">
                <!-- Stretched-link overlay -->
                <a href="${service.details_url}" class="stretched-link"></a>

                <!-- Slika usluge (bez anchor taga jer je sve pokriveno stretched-link) -->
                <img src="${service.image_url}"
                     class="card-img-top service-image"
                     alt="${service.title}"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250';">

                <div class="card-body d-flex flex-column">
                    <h6 class="service-category">${service.category}</h6>
                    <h5 class="card-title">${service.title}</h5>
                    <p class="card-text flex-grow-1">${service.description}</p>
                    <div class="d-flex align-items-center mb-2">
                        <img src="${service.user.avatar}"
                             alt="Avatar" class="rounded-circle avatar-img" width="30" height="30">
                        &nbsp; ${service.user.name} &nbsp;
                        <div class="text-warning ms-auto">
                            ${generateRatingStars(service.average_rating)}
                        </div>
                        <small class="ms-2">(${service.average_rating.toFixed(1)})</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="service-price">
                            <p class="card-text">
                                <strong>Cena od:</strong> ${service.basic_price} <i class="fas fa-euro-sign"></i>
                            </p>
                        </div>
                        <a href="${service.details_url}"
                           class="btn btn-service-details" style="position: relative; z-index: 2;">
                            Detaljnije
                        </a>
                    </div>
                </div>
            </div>
        </div>`;

    const generateRatingStars = (rating) => {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
        }
        return stars;
    };

    const excludedIds = @json($excludedIds ?? []);
    const maxPages = 5; // Možete ovo dinamički proslediti iz PHP-a ako želite

    async function loadMoreServices() {
        //console.log(excludedIds);
        if (loading || isSearchActive) return;

        if (page <= maxPages) {
            loading = true;
            spinner.style.display = 'block';
        }else{
            return;
        }

        try {
            await new Promise(resolve => setTimeout(resolve, loadDelay));

            // Dodaj excludedIds u URL kao query parametar
            const url = new URL(`/api/load-more-services`, window.location.origin);
            url.searchParams.append('page', page);
            excludedIds.forEach(id => {
                url.searchParams.append('excluded_ids[]', id);
            });

            const response = await fetch(url, { // Korišćenje 'url' ovde
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();

            if (data.services?.length) {
                const existingCards = container.querySelectorAll('.service-card').length;
                let currentRow = container.querySelector('.row:last-child');

                if (!currentRow || existingCards % 3 === 0) {
                    currentRow = document.createElement('div');
                    currentRow.className = 'row';
                    container.appendChild(currentRow);
                }

                data.services.forEach(service => {
                    currentRow.insertAdjacentHTML('beforeend', createServiceCard(service));
                });

                page = data.next_page || page + 1;

                if (!data.next_page) {
                    //noMoreResults.style.display = 'block';
                    window.removeEventListener('scroll', handleScroll);
                }
            } else {
                //noMoreResults.style.display = 'block';
                window.removeEventListener('scroll', handleScroll);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            //noMoreResults.style.display = 'block';
        } finally {
            loading = false;
            spinner.style.display = 'none';
        }
    }

    function isFooterVisible() {
        if (isSearchActive) return false;

        const rect = footer.getBoundingClientRect();
        return (
            rect.top <= window.innerHeight &&
            rect.bottom >= 0 &&
            rect.height > 0
        );
    }

    function handleScroll() {
        if (loading || isSearchActive) return;

        const currentScrollPosition = window.scrollY;
        const scrollingDown = currentScrollPosition > lastScrollPosition;
        lastScrollPosition = currentScrollPosition;

        if (!scrollingDown) return;

        if (isFooterVisible()) {
            loadMoreServices();
        }
    }

    // Initial setup
    if (container && !isSearchActive) {
        const initialRow = document.createElement('div');
        initialRow.className = 'row';
        container.appendChild(initialRow);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !loading && !isSearchActive) {
                    loadMoreServices();
                }
            });
        }, {
            threshold: 0.1
        });

        if (footer) {
            observer.observe(footer);
        }

        window.addEventListener('scroll', () => {
            if (!('IntersectionObserver' in window)) {
                handleScroll();
            }
        });

        //loadMoreServices();
    }
});
</script>

@section('scripts')
@endsection

<!-- <script>
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
</script> -->
@endsection
