@extends('layouts.app')
<title>Poslovi Online | Login stranica</title>
<link href="{{ asset('css/index.css') }}" rel="stylesheet">
@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white">
        <span class="hero-subtitle">Prijavi se na tvoj nalog</span>
        <h1 class="hero-title text-center">Poslovi<mark>online</mark></h1>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content col-md-6 mx-auto">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/preloader.png') }}"
                                     class="rounded-circle"
                                     alt="Poslovi Online"
                                     width="50"
                                     height="50">
            </div>
            <span class="hero-subtitle text-center text-dark fw-bold">Prijavi se na nalog</span>

            <div class="mt-3">
                @if(session('success'))
                    <div id="login-message" class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                 <!-- Prikaz poruka -->
                @if(session('error'))
                    <div id="login-message-danger" class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <div class="modal-body p-4">
                <!-- Session Status -->
                <x-auth-session-status class="mb-1" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email Address -->
                    <div class="mb-1">
                        <label for="email" class="form-label fw-bold">Email adresa</label>
                        <input type="email"
                               id="email"
                               class="form-control form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-1">
                        <label for="password" class="form-label fw-bold">Lozinka</label>
                        <input type="password"
                               id="password"
                               class="form-control form-control @error('password') is-invalid @enderror"
                               name="password"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="mb-4 text-right">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none">Zaboravljena lozinka?</a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn btn-primary w-100 mb-4">
                        <i class="fas fa-sign-in-alt me-1"></i>Prijavi se
                    </button>

                    <!-- Social Login -->
                    <div class="text-center mb-3 fw-bold">Prijavi se putem</div>
                    <div class="d-grid gap-3">
                        <a href="{{ route('login.google') }}" class="btn btn-outline-danger">
                            <i class="fab fa-google me-1"></i>Google
                        </a>
                       <!--  <a href="{{ route('login.facebook') }}" class="btn btn-outline-primary">
                            <i class="fab fa-facebook-f me-1"></i>Facebook
                        </a> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


<script>
     // Automatsko sakrivanje poruke prilikom brisanja
    const messageElementDanger = document.getElementById('login-message-danger');
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

    const messageElement = document.getElementById('login-message');
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

    // Automatski prikaz modalnog prozora kada se stranica učita
    window.onload = function() {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    };

    // Opciono: Onemogući zatvaranje modala klikom izvan
    document.getElementById('loginModal').addEventListener('show.bs.modal', function (event) {
        document.querySelector('.modal-backdrop').style.backgroundColor = 'rgba(0,0,0,0.7)';
    });
</script>
