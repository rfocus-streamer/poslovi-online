<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online | Platforma za freelance usluge | Pronađite vrhunske talente</title>
    <meta name="description" content="Pronađite ili ponudite digitalne usluge na najbržoj domaćoj freelance platformi.">
    <meta name="google-site-verification" content="sbt7BLiUQl1OCgkhcEY9oMlj_hPM4vjnyK-jKosZFCU" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user_id" content="{{ auth()->user()->id }}">
        @vite(['resources/js/app.js'])
    @endauth
     <!-- Dodaj favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 160px;
    height: 20px;
    top: -8px !important;
    cursor: pointer;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc; /* Osnovna boja pozadine */
    transition: 0.4s;
    border-radius: 24px;
    background: linear-gradient(to right, #ccc 50%, #4CAF50 50%); /* Leva siva, desna zelena */
    justify-content: space-between;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    border-radius: 50%;
    left: 2px;
    bottom: 1px;
    background-color: white;
    transition: 0.4s;
}

/* Stilizacija za label-text */
.label-text {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    font-weight: bold;
    color: #fff;
    margin-left: 27px;
}

.label-text.left {
    left: 12px;
}

.label-text.right {
    right: 22px;
}

/* Kada je dugme prebačeno na desnu stranu (Kupac neaktivan, Prodavac aktivan) */
input:checked + .slider {
    background: linear-gradient(to right, #ccc 50%, #4CAF50 50%); /* Leva polovina siva, desna polovina zelena */
}

input:checked + .slider:before {
    transform: translateX(138px); /* Premesti dugme u desno */
}

/* Tekst */
input:checked + .slider .label-text.left {
    color: #ccc; /* Disabled tekst za Kupca */
}

input:checked + .slider .label-text.right {
    color: #fff; /* Aktivna desna strana za Prodavca */
}

/* Kada je dugme prebačeno na levu stranu (Prodavac neaktivan, Kupac aktivan) */
input:not(:checked) + .slider {
    /*background: linear-gradient(to right, #007bff 50%, #ccc 50%); /* Leva polovina narandžasta, desna polovina siva */
    background: linear-gradient(to right, #9c1c2c 50%, #ccc 50%);
}

input:not(:checked) + .slider:before {
    transform: translateX(0px); /* Premesti dugme u levo */
}

/* Tekst */
input:not(:checked) + .slider .label-text.left {
    color: #fff; /* Aktivna leva strana za Kupca */
}

input:not(:checked) + .slider .label-text.right {
    color: #ccc; /* Disabled tekst za Prodavca */
}

.add-service-title {
    color: #9c1c2c;
    font-weight: bold;
    position: relative; /* Omogućava pozicioniranje pseudo-elementa */
    top: 7px;
    font-size: 0.81rem;
}

.add-service-title:hover {
    color: #4CAF50;
    text-decoration: none;
}

.add-service-title mark {
  background: linear-gradient(120deg, #4CAF50 0%, #45a049 100%);
  color: white;
  padding: 0 10px;
  border-radius: 5px;
}

.add-service-title2 {
    color: #9c1c2c;
    font-weight: bold;
    position: relative; /* Omogućava pozicioniranje pseudo-elementa */
    top: 7px;
    font-size: 0.81rem;
}

.add-service-title2:hover {
    color: #4CAF50;
    text-decoration: none;
}

.add-service-title2 mark {
  background: linear-gradient(120deg, #4CAF50 0%, #45a049 100%);
  color: white;
  padding: 0 10px;
  border-radius: 5px;
}

.btn-poslovi
{
    color: #fff !important;
    background-color: #9c1c2c !important;
    border-color: #9c1c2c !important;
}

.btn-poslovi-green
{
    color: #fff !important;
    background-color: #198754 !important;
    border-color: #198754 !important;
}

/* Prilagodbe za mobilne uređaje */
@media (max-width: 767px) {
    .add-service-title2 {
        font-size: 0.85rem; /* Veći font za mobilne uređaje */
        top: -12px !important; /* Ukloni vertikalno pomeranje */
    }
}
</style>
<body>
    <!-- Dekstop view -->
    <div class="d-none d-md-block">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Poslovi Online Logo" width="160">
                </a>

                <div class="collapse navbar-collapse justify-content-end" id="navbarOptions">
                    <ul class="navbar-nav">
                        @auth
                            <li class="nav-item">
                                @if(!in_array(Auth::user()->role, ['support', 'admin']))
                                    @if(Auth::user()->package)
                                        @if($seller['countPublicService'] < Auth::user()->package->quantity)
                                            <a href="{{ route('services.create') }}" class="add-service-title">Dodaj <mark>ponudu</mark></a>
                                        @else
                                            <a href="{{ route('packages.index') }}" class="add-service-title">Dodaj <mark>ponudu</mark></a>
                                        @endif
                                    @else
                                            <a href="{{ route('packages.index') }}" class="add-service-title">Dodaj <mark>ponudu</mark></a>
                                    @endif
                                @endif
                            </li>

                            @if(Auth::user()->role == 'support' || Auth::user()->role == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('complaints.index') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                                        @if(isset($complaintCount) && $complaintCount > 0)
                                            <i class="fas fa-balance-scale {{ request()->routeIs('complaints.index') ? 'text-danger' : '' }}"></i> Arbitraža <span class="badge bg-danger">{{ $complaintCount }}</span>
                                        @else
                                            <i class="fas fa-balance-scale {{ request()->routeIs('complaints.index') ? 'text-danger' : '' }}"></i> Arbitraža
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                                        <i class="fas fa-ticket {{ request()->routeIs('tickets.index') ? 'text-danger' : '' }}"></i> Tiketi
                                        @if($ticketCount > 0)
                                            <span class="badge bg-danger">{{ $ticketCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            @if(Auth::user()->role == 'buyer')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('favorites.index') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                                        @if(isset($favoriteCount) && $favoriteCount > 0)
                                            <i class="fas fa-heart {{ request()->routeIs('favorites.index') ? 'text-danger' : '' }}"></i> Omiljeno <span class="badge bg-danger">{{ $favoriteCount }}</span>
                                        @else
                                            <i class="fas fa-heart {{ request()->routeIs('favorites.index') ? 'text-danger' : '' }}"></i> Omiljeno
                                        @endif
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item" id="messages">
                                <a class="nav-link {{ request()->routeIs('messages.index') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                                    <i class="fas fa-envelope {{ request()->routeIs('messages.index') ? 'text-danger' : '' }}"></i> Poruke
                                    <!-- Dodajemo span za broj novih poruka -->
                                    <span class="badge bg-danger" id="unread-count-id-{{ Auth::user()->id }}" style="display: {{ $messagesCount > 0 ? 'inline-block' : 'none' }}">{{$messagesCount}}</span>
                                </a>
                            </li>
                            @if(Auth::user()->role == 'buyer')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}"" href="{{ route('cart.index') }}">
                                        @if(isset($cartCount) && $cartCount > 0)
                                            <i class="fas fa-shopping-cart {{ request()->routeIs('cart.index') ? 'text-danger' : '' }}"></i> Korpa <span class="badge bg-danger">{{ $cartCount }}</span>
                                        @else
                                            <i class="fas fa-shopping-cart {{ request()->routeIs('cart.index') ? 'text-danger' : '' }}"></i> Korpa
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    @if(isset($projectCount) && $projectCount > 0)
                                        <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-handshake {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Poslovi <span class="badge bg-danger">{{ $projectCount }}</span></a>
                                    @else
                                        <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-handshake {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Poslovi</a>
                                    @endif
                                </li>
                            @endif
                            @if(Auth::user()->role == 'seller')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('services.index', 'services.view') ? 'active' : '' }}" href="{{ route('services.index') }}"><i class="fas fa-file-signature {{ request()->routeIs('services.index', 'services.view') ? 'text-danger' : '' }}"></i> Ponude</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('projects.jobs') ? 'active' : '' }}" href="{{ route('projects.jobs') }}">
                                        <i class="fas fa-handshake {{ request()->routeIs('projects.jobs') ? 'text-danger' : '' }}"></i> Poslovi
                                        @if(isset($seller['countProjects']) and $seller['countProjects'] > 0)
                                            <span class="badge bg-danger">{{ $seller['countProjects'] }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('packages.index') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                                        <i class="fas fa-calendar-alt {{ request()->routeIs('packages.index') ? 'text-danger' : '' }}"></i> Plan
                                    </a>
                                </li>
                            @endif
                        @endauth

                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link {{ request()->routeIs('deposit.form', 'profile.edit', 'invoices.index', 'affiliate.index','tickets.index') ? 'active' : '' }}" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user {{ request()->routeIs('deposit.form', 'profile.edit', 'invoices.index', 'affiliate.index','tickets.index') ? 'text-danger' : '' }}"></i> Profil
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Uredi profil</a></li>
                                    @if(!in_array(Auth::user()->role, ['support', 'admin']))
                                        <li><a class="dropdown-item" href="{{ route('deposit.form') }}">Depozit</a></li>
                                        <li><a class="dropdown-item" href="{{ route('invoices.index') }}">Računi</a></li>
                                        <li><a class="dropdown-item" href="{{ route('affiliate.index') }}">Preporuči i Zaradi</a></li>
                                        <li><a class="dropdown-item" href="{{ route('tickets.index') }}">Tiketi</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('subscriptions.index') }}">Pretplate</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Odjava</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->role == 'seller' or Auth::user()->role == 'buyer')
                                <!-- Switch za izbor Kupac/Prodavac -->
                                <li class="nav-item">
                                    <label class="switch">
                                        <input type="checkbox" id="roleSwitch"
                                            {{ Auth::user()->role == 'seller' ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                        <span class="label-text left">Kupac</span>
                                        <span class="label-text right">Prodavac</span>
                                    </label>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i> Prijava
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i> Registracija
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Drugi navbar: Kategorije -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">

                <!-- Kategorije -->
                <div class="collapse navbar-collapse justify-content-center" id="navbarCategories">
                    <ul class="navbar-nav">
                        @foreach ($categories as $category)
                            <li class="nav-item dropdown mx-2"> <!-- Dodajte horizontalni margin -->
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{ $category->id }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $category->name }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{ $category->id }}">
                                    @foreach ($category->subcategories as $subcategory)
                                        <li>
                                            <a class="dropdown-item"
                                   href="{{ route('home', ['search' => $subcategory->name, 'category' => $category->name]) }}">
                                    {{ $subcategory->name }}
                                </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- Kartice za mobilne uređaje -->
    <div class="d-block d-md-none">
        <div class=" d-flex">
            <!-- Logo -->
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Poslovi Online Logo" width="160">
            </a>

            @guest
                <div class="d-flex mt-5 gap-3 ms-auto mr-3" style="color: rgba(0,0,0,.5) !important;">
                    <a class="nav-link-mobile {{ request()->routeIs('login') ? 'active' : '' }}"" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Prijava
                    </a>

                    <a class="nav-link-mobile" href="{{ route('register') }}">
                        <i class="fas fa-user-plus"></i> Registracija
                    </a>
                </div>
            @else
                <!-- Hamburger meni ikona -->
                <div class="d-flex justify-content-end mt-2 ms-auto mr-2">
                    <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas" style="color: rgba(0, 0, 0, .5) !important;">
                        <i class="fas fa-bars"></i> Profil
                    </button>
                </div>

                <!-- Offcanvas meni sa profil opcijama -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="profileOffcanvas" aria-labelledby="profileOffcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title text-danger" id="profileOffcanvasLabel">
                            <i class="fas fa-user me-2"></i>Profil
                        </h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="list-group">
                            <li class="list-group-item border-0">
                                <a class="text-dark" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-circle me-2"></i> Uredi profil
                                </a>
                            </li>

                            @if(!in_array(Auth::user()->role, ['support', 'admin']))
                            <li class="list-group-item border-0">
                                <a class="text-dark" href="{{ route('deposit.form') }}">
                                    <i class="fas fa-wallet me-2"></i> Depozit
                                </a>
                            </li>
                            <li class="list-group-item border-0">
                                <a class="text-dark" href="{{ route('invoices.index') }}">
                                    <i class="fas fa-file-invoice me-2"></i> Računi
                                </a>
                            </li>
                            <li class="list-group-item border-0">
                                <a class="text-dark" href="{{ route('affiliate.index') }}">
                                    <i class="fas fa-share-alt me-2"></i> Preporuči i Zaradi
                                </a>
                            </li>
                            <li class="list-group-item border-0">
                                <a class="text-dark" href="{{ route('tickets.index') }}">
                                    <i class="fas fa-ticket-alt me-2"></i> Tiketi
                                </a>
                            </li>
                            @endif

                            <li class="list-group-item border-0">
                                <a class="text-dark" href="{{ route('subscriptions.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i> Pretplate
                                </a>
                            </li>

                            <li class="list-group-item border-0 mt-3">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-poslovi w-100">
                                        <i class="fas fa-sign-out-alt me-2"></i> Odjava
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endguest
        </div>

        @auth
            <div class="d-flex justify-content-between align-items-center">
                @if(Auth::user()->role == 'seller' || Auth::user()->role == 'buyer')
                    <div class="d-flex align-items-center ml-4">
                        @if(!in_array(Auth::user()->role, ['support', 'admin']))
                            @if(Auth::user()->package)
                                @if($seller['countPublicService'] < Auth::user()->package->quantity)
                                    <a href="{{ route('services.create') }}" class="add-service-title2">Dodaj <mark>ponudu</mark></a>
                                @else
                                    <a href="{{ route('packages.index') }}" class="add-service-title2">Dodaj <mark>ponudu</mark></a>
                                @endif
                            @else
                                <a href="{{ route('packages.index') }}" class="add-service-title2">Dodaj <mark>ponudu</mark></a>
                            @endif
                        @endif
                    </div>

                    <!-- Switch za izbor Kupac/Prodavac -->
                    <div class="ms-auto mr-3">
                        <label class="switch">
                            <input type="checkbox" id="roleSwitch2"
                                {{ Auth::user()->role == 'seller' ? 'checked' : '' }}>
                            <span class="slider"></span>
                            <span class="label-text left">Kupac</span>
                            <span class="label-text right">Prodavac</span>
                        </label>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-center gap-3">
                    @if(Auth::user()->role == 'support' || Auth::user()->role == 'admin')
                        <a class="nav-link-mobile {{ request()->routeIs('complaints.index') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                            @if(isset($complaintCount) && $complaintCount > 0)
                                <i class="fas fa-balance-scale {{ request()->routeIs('complaints.index') ? 'text-danger' : '' }}"></i> Arbitraža <span class="badge bg-danger">{{ $complaintCount }}</span>
                            @else
                                <i class="fas fa-balance-scale {{ request()->routeIs('complaints.index') ? 'text-danger' : '' }}"></i> Arbitraža
                            @endif
                        </a>

                        <a class="nav-link-mobile {{ request()->routeIs('tickets.index') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                            <i class="fas fa-ticket {{ request()->routeIs('tickets.index') ? 'text-danger' : '' }}"></i> Tiketi
                            @if($ticketCount > 0)
                                <span class="badge bg-danger">{{ $ticketCount }}</span>
                            @endif
                        </a>

                    @endif

                    @if(Auth::user()->role == 'buyer')

                        <a class="nav-link-mobile {{ request()->routeIs('favorites.index') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                            @if(isset($favoriteCount) && $favoriteCount > 0)
                                <i class="fas fa-heart {{ request()->routeIs('favorites.index') ? 'text-danger' : '' }}"></i> Omiljeno <span class="badge bg-danger">{{ $favoriteCount }}</span>
                            @else
                                <i class="fas fa-heart {{ request()->routeIs('favorites.index') ? 'text-danger' : '' }}"></i> Omiljeno
                            @endif
                        </a>

                    @endif

                    <a class="nav-link-mobile {{ request()->routeIs('messages.index') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                            <i class="fas fa-envelope {{ request()->routeIs('messages.index') ? 'text-danger' : '' }}"></i> Poruke
                                <!-- Dodajemo span za broj novih poruka -->
                                <span class="badge bg-danger" id="unread-count-id-{{ Auth::user()->id }}" style="display: {{ $messagesCount > 0 ? 'inline-block' : 'none' }}">{{$messagesCount}}</span>
                    </a>

                    @if(Auth::user()->role == 'buyer')
                        <a class="nav-link-mobile {{ request()->routeIs('cart.index') ? 'active' : '' }}"" href="{{ route('cart.index') }}">
                            @if(isset($cartCount) && $cartCount > 0)
                                <i class="fas fa-shopping-cart {{ request()->routeIs('cart.index') ? 'text-danger' : '' }}"></i> Korpa <span class="badge bg-danger">{{ $cartCount }}</span>
                            @else
                                <i class="fas fa-shopping-cart {{ request()->routeIs('cart.index') ? 'text-danger' : '' }}"></i> Korpa
                            @endif
                        </a>



                        @if(isset($projectCount) && $projectCount > 0)
                            <a class="nav-link-mobile {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-handshake {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Poslovi <span class="badge bg-danger">{{ $projectCount }}</span></a>
                        @else
                            <a class="nav-link-mobile {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-handshake {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Poslovi</a>
                        @endif

                    @endif

                    @if(Auth::user()->role == 'seller')

                        <a class="nav-link-mobile {{ request()->routeIs('services.index', 'services.view') ? 'active' : '' }}" href="{{ route('services.index') }}"><i class="fas fa-file-signature {{ request()->routeIs('services.index', 'services.view') ? 'text-danger' : '' }}"></i> Ponude</a>

                        <a class="nav-link-mobile {{ request()->routeIs('projects.jobs') ? 'active' : '' }}" href="{{ route('projects.jobs') }}">
                            <i class="fas fa-handshake {{ request()->routeIs('projects.jobs') ? 'text-danger' : '' }}"></i> Poslovi
                            @if(isset($seller['countProjects']) and $seller['countProjects'] > 0)
                                <span class="badge bg-danger">{{ $seller['countProjects'] }}</span>
                            @endif
                        </a>

                        <a class="nav-link-mobile {{ request()->routeIs('packages.index') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                            <i class="fas fa-calendar-alt {{ request()->routeIs('packages.index') ? 'text-danger' : '' }}"></i> Plan
                        </a>
                    @endif
            </div>
        @endauth

        <!-- Toggler za mobilni prikaz sa FontAwesome ikonicom -->
        <!-- Mobilni header -->
        <div class="mobile-category-header d-lg-none mt-2">
            <span>Kategorije usluga</span>
            <button class="navbar-toggler custom-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarCategories">
                    <i class="fas fa-chevron-down"></i>
            </button>

            <!-- Kategorije -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarCategories">
                <ul class="navbar-nav">
                    @foreach ($categories as $category)
                        <li class="nav-item dropdown mx-2"> <!-- Dodajte horizontalni margin -->
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{ $category->id }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $category->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{ $category->id }}">
                                @foreach ($category->subcategories as $subcategory)
                                    <li>
                                        <a class="dropdown-item"
                               href="{{ route('home', ['search' => $subcategory->name, 'category' => $category->name]) }}">
                                {{ $subcategory->name }}
                            </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Glavni sadržaj -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white mt-5" style="background-color: #9c1c2c;">
        <div class="container py-5">
            <div class="row justify-content-center"> <!-- Centrira sve kolone -->
                <!-- O nama -->
                <!-- <div class="col-md-4 col-10 mb-4 text-center text-md-start">
                    <h5>Poslovi Online</h5>
                    <p class="text-light">
                        Platforma za pružanje i pronalaženje usluga bez fizičkog kontakta.
                    </p>
                </div> -->

                 <!-- Pravne informacije -->
                <div class="col-md-6 text-center">
                    <h5>Pravne informacije</h5>
                    <ul class="list-unstyled d-flex justify-content-center gap-3">
                        <li><a class="text-white" href="{{ route('terms') }}"><i class="fas fa-file-alt me-2"></i>Uslovi korišćenja</a></li>
                        <li><a class="text-white" href="{{ route('privacy-policy') }}"><i class="fas fa-shield-alt me-2"></i>Politika privatnosti</a></li>
                        <li><a class="text-white" href="{{ route('cookies') }}"><i class="fas fa-cookie-bite me-2"></i>Politika kolačića</a></li>
                    </ul>
                </div>

                <!-- Kontakt -->
               <!--  <div class="col-md-3 col-10 mb-4 text-center text-md-start">
                    <h5>Kontakt</h5>
                    <ul class="list-unstyled">
                        <li><a class="text-white" href="{{ route('tickets.create') }}"><i class="fas fa-envelope me-2"></i>Kontaktirajte nas</a></li>
                    </ul>
                </div> -->

                <!-- Socijalne mreže -->
                <div class="col-md-3 col-10 mb-4 text-center">
                    <h5>Pratite nas</h5>
                    <div class="social-links d-flex justify-content-center gap-3 mt-4">
                        <a href="https://www.facebook.com/profile.php?id=61551626835206" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fwww.instagram.com%2Fposlovionline%2F%3Ffbclid%3DIwZXh0bgNhZW0CMTAAYnJpZBExR3JyTW9lRDlqR1dCOUZzTQEeL1mP9GyDIUexLr0pTEfePBh2SH2CdFgNAzXVvXfCKZx-9FcdjEXeHQGFH6Y_aem_Oipf62RZ-wvm0udGZmYSRQ&h=AT1FjqUsQTkzNnJy8pWYebNPKfQrnquUie8OeayO4RR9IloHbZTi3_kHOVTRbAnVrm6kAopEGALeiuRPEsK5-IPbGUDn6j_aAuPPepYzyZ5mitBUFd83kI4emE3bLbtVCXE7Mw" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fwww.linkedin.com%2Fcompany%2Fposlovi-online%2F%3Ffbclid%3DIwZXh0bgNhZW0CMTAAYnJpZBExR3JyTW9lRDlqR1dCOUZzTQEeq3K6W7hPnx8d8UMx4wyI1oDXDJFZ2uwobG5a3hFl4xW33nlCEx7dglStslI_aem_qnZQKQXyLatU2oVhLNnF7A&h=AT1FjqUsQTkzNnJy8pWYebNPKfQrnquUie8OeayO4RR9IloHbZTi3_kHOVTRbAnVrm6kAopEGALeiuRPEsK5-IPbGUDn6j_aAuPPepYzyZ5mitBUFd83kI4emE3bLbtVCXE7Mw" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>

           <!--  <p class="text-light text-center">
                        Platforma za pružanje i pronalaženje usluga bez fizičkog kontakta.
                    </p> -->

            <!-- Copyright -->
            <div class="border-top pt-4">
                <p class="text-center text-light mb-0">
                    &copy; {{ date('Y') }} Poslovi Online. Sva prava zadržana.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
document.querySelector('.add-service-title').addEventListener('click', function(event) {
    event.preventDefault(); // Sprečava default akciju (navigaciju)

    // Proveri da li je korisnik ulogovan
    @auth
        var currentRole = '{{ Auth::user()->role }}'; // PHP varijabla za trenutnu ulogu
    @endauth

    // Proveri da li korisnik nije ulogovan
    @guest
        var currentRole = ''; // Prazna varijabla ako korisnik nije ulogovan
    @endguest

    if (currentRole === 'buyer') {
        // Ako je trenutna uloga 'buyer', promeni je u 'seller'

        // Slanje AJAX zahteva za promenu uloge korisnika
        fetch('/update-role', {
            method: 'POST',
            body: JSON.stringify({ role: 'seller' }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())  // Parsira JSON odgovor
        .then(data => {
            if (data.success) {
                console.log('Uloga uspešno promenjena!');

                // Ažuriraj switcher da prikazuje 'seller' stanje
                var roleSwitch = document.getElementById('roleSwitch');
                var leftLabel = document.querySelector('.label-text.left');
                var rightLabel = document.querySelector('.label-text.right');

                // Postavi 'checked' za 'seller' (desna strana)
                roleSwitch.checked = true;

                // Ažuriraj tekst na switcher-u
                leftLabel.textContent = 'Kupac';
                rightLabel.textContent = 'Prodavac';

                // Nakon uspešne promene uloge, nastavi sa navigacijom
                var link = event.target.closest('a'); // Selektuj 'a' tag
                if (link && link.href) {
                    window.location.href = link.href;  // Redirektuje na originalni link
                }
            } else {
                console.error('Došlo je do greške.');
            }
        })
        .catch(error => {
            console.error('Greška u AJAX pozivu:', error);
        });
    } else {
        // Ako je trenutna uloga 'seller' ili neka druga, samo nastavi sa navigacijom
        var link = event.target.closest('a'); // Selektuj 'a' tag
        if (link && link.href) {
            window.location.href = link.href;  // Redirektuje na originalni link
        }
    }
});

document.querySelector('.add-service-title2').addEventListener('click', function(event) {
    event.preventDefault(); // Sprečava default akciju (navigaciju)

    // Proveri da li je korisnik ulogovan
    @auth
        var currentRole = '{{ Auth::user()->role }}'; // PHP varijabla za trenutnu ulogu
    @endauth

    // Proveri da li korisnik nije ulogovan
    @guest
        var currentRole = ''; // Prazna varijabla ako korisnik nije ulogovan
    @endguest

    if (currentRole === 'buyer') {
        // Ako je trenutna uloga 'buyer', promeni je u 'seller'

        // Slanje AJAX zahteva za promenu uloge korisnika
        fetch('/update-role', {
            method: 'POST',
            body: JSON.stringify({ role: 'seller' }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())  // Parsira JSON odgovor
        .then(data => {
            if (data.success) {
                console.log('Uloga uspešno promenjena!');

                // Ažuriraj switcher da prikazuje 'seller' stanje
                var roleSwitch = document.getElementById('roleSwitch2');
                var leftLabel = document.querySelector('.label-text.left');
                var rightLabel = document.querySelector('.label-text.right');

                // Postavi 'checked' za 'seller' (desna strana)
                roleSwitch.checked = true;

                // Ažuriraj tekst na switcher-u
                leftLabel.textContent = 'Kupac';
                rightLabel.textContent = 'Prodavac';

                // Nakon uspešne promene uloge, nastavi sa navigacijom
                var link = event.target.closest('a'); // Selektuj 'a' tag
                if (link && link.href) {
                    window.location.href = link.href;  // Redirektuje na originalni link
                }
            } else {
                console.error('Došlo je do greške.');
            }
        })
        .catch(error => {
            console.error('Greška u AJAX pozivu:', error);
        });
    } else {
        // Ako je trenutna uloga 'seller' ili neka druga, samo nastavi sa navigacijom
        var link = event.target.closest('a'); // Selektuj 'a' tag
        if (link && link.href) {
            window.location.href = link.href;  // Redirektuje na originalni link
        }
    }
});


document.getElementById('roleSwitch').addEventListener('change', function() {
    var newRole = this.checked ? 'seller' : 'buyer';

    // Slanje AJAX zahteva za promenu uloge korisnika
    fetch('/update-role', {
        method: 'POST',
        body: JSON.stringify({ role: newRole }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())  // Parsira JSON odgovor
    .then(data => {
        //console.log(data);  // Ispisuje JSON odgovor u konzolu

        if (data.success) {
            console.log('Uloga uspešno promenjena!');
            // Preusmeri korisnika na "profile.edit" rutu nakon promene uloge
            window.location.href = "{{ route('profile.edit') }}";  // Redirektuje na profil
        } else {
            console.error('Došlo je do greške.');
        }
    })
    .catch(error => {
        console.error('Greška u AJAX pozivu:', error);
    });
});

document.getElementById('roleSwitch2').addEventListener('change', function() {
    var newRole = this.checked ? 'seller' : 'buyer';

    // Slanje AJAX zahteva za promenu uloge korisnika
    fetch('/update-role', {
        method: 'POST',
        body: JSON.stringify({ role: newRole }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())  // Parsira JSON odgovor
    .then(data => {
        //console.log(data);  // Ispisuje JSON odgovor u konzolu

        if (data.success) {
            console.log('Uloga uspešno promenjena!');
            // Preusmeri korisnika na "profile.edit" rutu nakon promene uloge
            window.location.href = "{{ route('profile.edit') }}";  // Redirektuje na profil
        } else {
            console.error('Došlo je do greške.');
        }
    })
    .catch(error => {
        console.error('Greška u AJAX pozivu:', error);
    });
});

</script>
</body>
</html>
