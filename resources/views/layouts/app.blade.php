<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poslovi Online | Platforma za freelance usluge | Pronađite vrhunske talente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Prvi navbar: Logo + opcije -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Poslovi Online Logo" width="160">
            </a>

            <!-- Toggler za mobilni prikaz -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarOptions" aria-controls="navbarOptions" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Opcije (Omiljeno, Poruke, Korpa, Profile) -->
            <!--  <i class="fas fa-comment-dots text-info"></i> chat -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarOptions">
                <ul class="navbar-nav">
                    @auth
                        @if(Auth::user()->role == 'buyer' || Auth::user()->role == 'both')
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-envelope"></i> Poruke
                            </a>
                        </li>
                        @if(Auth::user()->role == 'buyer' || Auth::user()->role == 'both')
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
                                <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-project-diagram {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Projekti</a>
                            </li>
                        @endif
                        @if(Auth::user()->role == 'seller' || Auth::user()->role == 'both')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-file-signature {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Ponude</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('projects.jobs') ? 'active' : '' }}" href="{{ route('projects.jobs') }}">
                                    <i class="fas fa-handshake {{ request()->routeIs('projects.jobs') ? 'text-danger' : '' }}"></i> Poslovi
                                    @if(isset($seller['countProjects']))
                                        <span class="badge bg-danger">{{ $seller['countProjects'] }}</span>
                                    @endif
                                </a>
                            </li>

                        @endif
                    @endauth

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="#">Profil</a></li>
                                <li><a class="dropdown-item" href="#">Podešavanja</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Odjava</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
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
            <!-- Toggler za mobilni prikaz sa FontAwesome ikonicom -->
            <!-- Mobilni header -->
            <div class="mobile-category-header d-lg-none">
                <span>Kategorije usluga</span>
                <button class="navbar-toggler custom-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarCategories">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>

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
                                    <li><a class="dropdown-item" href="#">{{ $subcategory->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>

    <!-- Glavni sadržaj -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white mt-5" style="background-color: #9c1c2c;">
        <div class="container py-5">
            <div class="row justify-content-center"> <!-- Centrira sve kolone -->
                <!-- O nama -->
                <div class="col-md-4 col-10 mb-4 text-center text-md-start">
                    <h5>Poslovi Online</h5>
                    <p class="text-light">
                        Platforma za pružanje i pronalaženje usluga bez fizičkog kontakta.
                    </p>
                </div>

                <!-- Kontakt -->
                <div class="col-md-3 col-10 mb-4 text-center text-md-start">
                    <h5>Kontakt</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i>podrska@poslovionline.com</li>
                        <li><i class="fas fa-phone me-2"></i>+381 11 123 456</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Beograd, Srbija</li>
                    </ul>
                </div>

                <!-- Socijalne mreže -->
                <div class="col-md-3 col-10 mb-4 text-center">
                    <h5>Pratite nas</h5>
                    <div class="social-links d-flex justify-content-center gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>

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

</body>
</html>
