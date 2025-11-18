<!-- resources/views/index.blade.php -->
@extends('layouts.app')
<style type="text/css">
/* Stil za loading spinner */
#loading-spinner {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--dropdown-bg);
    color: var(--text-color);
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
    color: var(--text-color);
}

.service-card .card {
    position: relative;
    background-color: var(--menu-bg);
    color: var(--text-color);
    border: 1px solid var(--border-color);
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
    color: white;
}

/* Hero sekcija sa tematskim bojama */
.hero-section {
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                    url('https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
    padding: 70px 0;
    margin-bottom: 40px;
    background-position: center center; /* Centriraj sliku u sredini */
    background-size: cover; /* Zumiraj sliku tako da pokrije celu površinu */
}

.hero-title {
    font-size: 3.5rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-title mark {
    background: linear-gradient(120deg, #4CAF50 0%, #45a049 100%);
    color: white;
    padding: 0 10px;
    border-radius: 5px;
}

.hero-subtitle{
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}


/* Stilovi za Top Ponude sekciju sa badge-om */
.top-section-with-badge {
    position: relative;
    margin-top: 60px;
    margin-bottom: 30px;
}

.top-section-badge {
    position: absolute;
    top: -19px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, var(--primary-color), #267c3e);
    color: white;
    padding: 8px 25px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 10px rgba(47, 155, 75, 0.3);
    z-index: 10;
    white-space: nowrap;
}

.top-section-content {
    background: var(--menu-bg);
    border: 2px solid var(--primary-color);
    border-radius: 15px;
    padding: 15px 20px 5px 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    position: relative;
}

.top-section-header {
    text-align: center;
}

.top-section-subtitle {
    font-size: 1.1rem;
    color: var(--text-color);
    opacity: 0.9;
    font-weight: 500;
    margin: 0;
    line-height: 1.4;
}

/* Stilovi za Novosti sekciju sa badge-om */
.new-section-with-badge {
    position: relative;
    margin-top: 60px;
    margin-bottom: 30px;
}

.new-section-badge {
    position: absolute;
    top: -19px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, var(--secondary-color));
    color: white;
    padding: 8px 25px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 10px rgba(41, 128, 185, 0.3);
    z-index: 10;
    white-space: nowrap;
}

.new-section-content {
    background: var(--menu-bg);
    border: 2px solid var(--secondary-color);
    border-radius: 15px;
    padding: 15px 20px 5px 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    position: relative;
}

.new-section-header {
    text-align: center;
}

.new-section-subtitle {
    font-size: 1.1rem;
    color: var(--text-color);
    opacity: 0.9;
    font-weight: 500;
    margin: 0;
    line-height: 1.4;
}

/* Prilagodba kartica za temu */
.card {
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.card-title {
    color: var(--text-color);
    font-weight: 600;
}

.card-text {
    color: var(--text-color);
    opacity: 0.8;
}

.service-category {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: auto;
}

.service-price {
    color: var(--text-color);
}

.btn-service-details {
    background: linear-gradient(135deg, var(--primary-color), #267c3e);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    z-index: 3;
}

.btn-service-details:hover {
    background: linear-gradient(135deg, #267c3e, var(--primary-color));
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(47, 155, 75, 0.3);
}

.avatar-img {
    border: 2px solid var(--primary-color);
}

/* Paginacija */
.pagination .page-link {
    background-color: var(--bg-color);
    color: var(--text-color);
    border: 1px solid var(--border-color);
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination .page-link:hover {
    background-color: var(--dropdown-hover);
    color: var(--text-color);
}

/* Spinner za tamnu temu */
.spinner-border.text-primary {
    color: var(--primary-color) !important;
}

/* Placeholder slika */
.service-image {
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid var(--border-color);
}

/* Responsive dizajn */
@media (max-width: 768px) {
    .top-section-content,
    .new-section-content {
        padding: 30px 15px 15px 15px;
    }

    .top-section-badge,
    .new-section-badge {
        padding: 6px 20px;
        font-size: 0.8rem;
        top: -10px;
    }

    .top-section-subtitle,
    .new-section-subtitle {
        font-size: 1rem;
    }

    .hero-section {
        padding: 10px 0;
    }

    .hero-title{
        font-size: 2.5rem;
    }

    .hero-subtitle{
        font-size: 0.8rem;
    }
}
</style>

@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white mt-2">
        <span class="hero-subtitle">Tvoja online platforma za sigurno poslovanje</span>
        <h1 class="hero-title mt-1">Poslovi<mark>online</mark></h1>
    </div>
</div>

@isset($searchTerm)
    <!-- Prikaz rezultata pretrage -->
    <div class="container mt-5">
        <div class="top-section-with-badge">
            <div class="top-section-badge">REZULTAT PRETRAGE</div>
            <div class="top-section-content">
                <div class="top-section-header">
                    @isset($searchCategory)
                        <p class="top-section-subtitle">{{ $searchCategory }}</p>
                    @else
                        <p class="top-section-subtitle">Prikaz rezultata</p>
                    @endisset
                    <p class="top-section-subtitle">{{ $searchTerm }}</p>
                </div>
            </div>
        </div>

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
                            <div class="text-warning ms-auto">
                                @for ($j = 1; $j <= 5; $j++)
                                    @if ($j <= $service->average_rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <small class="ms-2">({{ $service->average_rating }})</small>
                        </div>

                        <!-- Cena i dugme -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="service-price">
                                <!-- Cena -->
                                <p class="card-text" style="color: var(--primary-color)">
                                    <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} €
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

<!-- Top usluge sa badge-om -->
<div class="container">
            <div class="row">

                <div class="top-section-with-badge">
                    <div class="top-section-badge">TOP PONUDE</div>
                    <div class="top-section-content">
                        <div class="top-section-header">
                            <p class="top-section-subtitle">Najbolji prodavci, najtraženije usluge!</p>
                        </div>
                    </div>
                </div>

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
                                <div class="text-warning ms-auto">
                                    @for ($j = 1; $j <= 5; $j++)
                                        @if ($j <= $service->average_rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="ms-2">({{ $service->average_rating }})</small>
                            </div>

                            <!-- Cena i dugme -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="service-price">
                                    <!-- Cena -->
                                    <p class="card-text" style="color: var(--primary-color)">
                                        <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} €
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
    </div>
</div>

<!-- Poslednje ponude sa badge-om -->
<div class="container mt-3">
    <div class="row">
        <div class="new-section-with-badge">
            <div class="new-section-badge">Najnovije ponude su stigle!</div>
            <div class="new-section-content">
                <div class="new-section-header">
                    <p class="new-section-subtitle">Pogledaj šta je novo i dostupno.</p>
                </div>
            </div>
        </div>
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
                                <div class="text-warning ms-auto">
                                    @for ($j = 1; $j <= 5; $j++)
                                        @if ($j <= $service->average_rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="ms-2">({{ $service->average_rating }})</small>
                            </div>

                            <!-- Cena i dugme -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="service-price">
                                    <!-- Cena -->
                                    <p class="card-text" style="color: var(--primary-color)">
                                        <strong>Cena od:</strong> {{ number_format($service->basic_price, 0, ',', '.') }} €
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
                            <p class="card-text" style="color: var(--primary-color)">
                                <strong>Cena od:</strong> ${service.basic_price} €
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
    const maxPages = 5;

    async function loadMoreServices() {
        if (loading || isSearchActive) return;

        if (page <= maxPages) {
            loading = true;
            spinner.style.display = 'block';
        }else{
            return;
        }

        try {
            await new Promise(resolve => setTimeout(resolve, loadDelay));

            const url = new URL(`/api/load-more-services`, window.location.origin);
            url.searchParams.append('page', page);
            excludedIds.forEach(id => {
                url.searchParams.append('excluded_ids[]', id);
            });

            const response = await fetch(url, {
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
                    window.removeEventListener('scroll', handleScroll);
                }
            } else {
                window.removeEventListener('scroll', handleScroll);
            }
        } catch (error) {
            console.error('Fetch error:', error);
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
    }
});
</script>

@section('scripts')
@endsection

@endsection
