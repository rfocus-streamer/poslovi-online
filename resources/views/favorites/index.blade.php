@extends('layouts.app')
<title>Poslovi Online | Tvoje omiljene ponude</title>
<link href="{{ asset('css/favorites.css') }}" rel="stylesheet">
@section('content')
<style type="text/css">
    .billing-period {
        word-wrap: break-word; /* Prelomi reč na sledećoj liniji */
        word-break: break-word; /* Prelomi reč ako je dugačka */
        overflow-wrap: break-word; /* Još jedan način da omogućite prelamanje reči */
    }

    /* Ako koristite Flexbox, omogućite prelamanje redova u row-u */
    .card-body .row {
        flex-wrap: wrap; /* Omogućava prelamanje u redovima */
    }

     .table thead th {
        color: var(--text-color);
        border-color: var(--border-color);
        background-color: var(--menu-bg);
    }

    .table tbody td {
        border-color: var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-color);
    }

    .table tbody td a {
        color: var(--primary-color) !important;
        text-decoration: none;
    }

    .table tbody td a:hover {
        color: var(--primary-color);
    }

    .mobile-div div {
        color: var(--text-color);
    }

    .mobile-div a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .alert {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .alert-danger {
        background-color: var(--danger-bg);
        border-color: var(--danger);
        color: var(--danger);
    }

    .alert-success {
        background-color: var(--success-bg);
        border-color: var(--success);
        color: var(--success);
    }

    .card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
        box-shadow: 0 0.125rem 0.25rem var(--shadow);
    }

    .card-header {
        background-color: var(--menu-bg) !important;
        border-bottom-color: var(--border-color);
        color: var(--text-color);
    }

    .bg-light {
        background-color: var(--menu-bg) !important;
        color: var(--text-color);
    }

    .border {
        border-color: var(--border-color) !important;
    }

    .rounded {
        border-color: var(--border-color);
    }

    .btn-outline-light {
        border-color: var(--border-color);
        color: var(--text-color);
        background-color: transparent;
    }

    .btn-outline-light:hover {
        background-color: var(--menu-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .btn-link {
        color: var(--primary);
    }

    .btn-link:hover {
        color: var(--primary);
    }

    .modal-content {
        background-color: var(--menu-bg);
        color: var(--text-color);
        border-color: var(--border-color);
    }

    #modalUserImage {
        width: 130px;
        height: 130px;
        border-radius: 50%; /* Okrugli oblik */
        object-fit: cover; /* Obezbeđuje da slika popuni celu površinu bez distorzije */
    }


    .form-control{
        background-color: var(--card-bg) !important;
        color: var(--text-color) !important;
    }

    .form-select {
        background-color: var(--card-bg) !important;
        color: var(--text-color) !important;
        border: 1px solid var(--border-color) !important;
        padding: 0.5rem;
        font-size: 1rem;
    }

    /* Stil za option elemente */
    .form-select option {
        background-color: var(--menu-bg) !important;
    }

    /* Za disabled opcije, možete dodati stil */
    .form-select option:disabled {
        color: var(--disabled-text-color); /* Definišite ovu varijablu za boju onemogućenih opcija */
        background-color: var(--disabled-bg-color); /* Definišite ovu varijablu za pozadinu onemogućenih opcija */
    }
</style>
<div class="container">
    <!-- Prikaz poruke sa anchor ID -->
    @if(session('success'))
        <div id="cart-message" class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="cart-message" class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

        <!-- Desktop naslov + info -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-1">
            <!-- Naslov omiljeno levo -->
            <h5><i class="fas fa-heart"></i> Tvoje omiljene ponude</h5>
            @if($favoriteServices->isNotEmpty())
                <!-- pretraga omiljenih ponuda desno -->
                <input type="text" id="searchInput" placeholder="Pretraži omiljene ponude..." class="form-control w-25 d-none d-md-table"">
            @endif
        </div>

        <!-- Mobile naslov + info -->
        <div class="d-flex d-md-none flex-column text-center w-100 mb-1">
            <!-- Naslov omiljeno levo -->
            <h6><i class="fas fa-heart"></i> Tvoje omiljene ponude</h6>
            @if($favoriteServices->isNotEmpty())
                <!-- pretraga omiljenih ponuda desno -->
                <input type="text" id="searchInput" placeholder="Pretraži omiljene ponude..." class="form-control w-25 d-none d-md-table"">
            @endif
        </div>


    @if($favoriteServices->isEmpty())
        <!-- Desktop -->
        <div class="d-none d-md-flex">
            <p>Tvoja omiljena lista je prazna.</p>
        </div>

        <!-- Mobile  -->
        <div class="d-md-none text-center">
            <p>Tvoja omiljena lista je prazna.</p>
        </div>
    @else
        <table class="table table-bordered align-middle d-none d-md-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Usluga</th>
                    <th>Opis</th>
                    <th>Paket</th>
                    <th>Prodavac</th>
                    <th class="text-center">Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach($favoriteServices as $key => $favorite)
                    <tr data-service-id="{{ $favorite->service->id }}">
                        <td>{{ $key +1 }}</td>
                        <td><a class="text-dark" href="{{ route('services.show', ['id' => $favorite->service->id, 'slug' => Str::slug($favorite->service->title)]) }}">{{ $favorite->service->title }}</a></td>
                        <td>
                            {{ Str::limit($favorite->service->description, 20) }}  <a class="text-dark" href="{{ route('services.show', ['id' => $favorite->service->id, 'slug' => Str::slug($favorite->service->title)]) }}"><i class="fa fa-info-circle ml-2 text-primary mt-1" ></i></a>
                        </td>
                        <td>
                            <select class="form-select package-select">
                                <option selected disabled>Odaberi paket</option>
                                <option value="Basic">Basic</option>
                                <option value="Standard">Standard</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </td>
                        <td>{{ $favorite->service->user->firstname .' '.$favorite->service->user->lastname }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <form class="cart-form" method="POST">
                                    @csrf
                                    <button class="btn btn-outline-success w-100 add-to-cart-btn" data-bs-toggle="tooltip" title="Dodaj u korpu" disabled><i class="fas fa-shopping-cart"></i> Dodaj</button>
                                </form>

                                <form action="{{ route('favorites.destroy', $favorite) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Ukloni iz omiljeno"><i class="fas fa-trash"></i> Ukloni</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Mobile verzija kartica -->
        <div class="d-md-none">
            @foreach($favoriteServices as $key => $favorite)
            <div class="card mb-3 favorite-card" data-id="{{ $favorite->service->id }}">
                <a href="{{ route('services.show', ['id' => $favorite->service->id, 'slug' => Str::slug($favorite->service->title)]) }}" class="text-dark">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center" style="background-color: #198754 !important; color: white !important">
                        <span class="mr-3">#{{ $key + 1 }}</span>
                        <span><strong>{{ $favorite->service->title }}</strong></span>
                    </div>
                </a>
                <div class="card-body">
                    <p class="mb-1"><strong>Opis:</strong> {{ Str::limit($favorite->service->description, 50) }}</p>
                    <p class="mb-1"><strong>Prodavac:</strong> {{ $favorite->service->user->firstname }} {{ $favorite->service->user->lastname }}</p>

                    <div class="mb-2">
                        <label class="form-label mb-0"><strong>Paket:</strong></label>
                        <select class="form-select package-select mt-1">
                            <option selected disabled>Odaberi paket</option>
                            <option value="Basic">Basic</option>
                            <option value="Standard">Standard</option>
                            <option value="Premium">Premium</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <form class="cart-form w-100" method="POST">
                            @csrf
                            <button class="btn btn-outline-success w-100 add-to-cart-btn" title="Dodaj u korpu" disabled>
                                <i class="fas fa-shopping-cart"></i> Dodaj
                            </button>
                        </form>
                        <form action="{{ route('favorites.destroy', $favorite) }}" method="POST" class="w-100">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" title="Ukloni iz omiljeno">
                                <i class="fas fa-trash"></i> Ukloni
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>


        <!-- Paginacija -->
        <div class="d-flex justify-content-center pagination-buttons">
            {{ $favoriteServices->links() }}
        </div>

        <div class="mt-4 p-3 border rounded bg-light">
            <h5><i class="fas fa-info-circle"></i> Opis akcija</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-shopping-cart text-success"></i>
                    <strong>Dodavanje u korpu:</strong> Klikom na dugme usluga se dodaje u tvoju korpu (predhodno odaberi paket).
                </li>
                <li class="mb-2">
                    <i class="fas fa-trash text-danger"></i>
                    <strong>Uklanjanje iz omiljeno:</strong> Klikom na dugme brišeš uslugu iz tvoje omiljene liste (trajno).
                </li>
            </ul>
        </div>
    @endif
</div>
@endsection
<style>
.favorite-card {
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}
.favorite-card:hover {
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
}
</style>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".package-select").forEach(select => {
        select.addEventListener("change", function() {
            let selectedPackage = this.value;

            // Pronađi najbliži kontejner — može biti <tr> (desktop) ili .card (mobile)
            let container = this.closest("tr") || this.closest(".card");
            if (!container) return;

            let addToCartBtn = container.querySelector(".add-to-cart-btn");
            let cartForm = container.querySelector(".cart-form");
            let serviceId = container.getAttribute("data-service-id") || container.getAttribute("data-id");

            if (selectedPackage && serviceId && cartForm) {
                // Postavi novu action vrednost
                let newAction = `/cart/${serviceId}/${selectedPackage}`;
                cartForm.setAttribute("action", newAction);
                addToCartBtn?.removeAttribute("disabled");
            } else {
                addToCartBtn?.setAttribute("disabled", "disabled");
            }
        });
    });

    // Pretraga tabele
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('.table tbody');
    let rows = [];
    if (tableBody) {
        rows = tableBody.querySelectorAll('tr');
        searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();

        rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        match = true;
                    }
                });

                if (match) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    } else {
        //console.warn("Tabela nije pronađena u DOM-u.");
    }


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
</script>
