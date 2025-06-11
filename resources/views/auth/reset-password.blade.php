@extends('layouts.app')
<title>Poslovi Online | Resetovanje lozinke</title>
<link href="{{ asset('css/index.css') }}" rel="stylesheet">
@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white">
        <span class="hero-subtitle">Prijavi se na tvoj nalog kroz resetovanje tvoje lozinke</span>
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
            <span class="hero-subtitle text-center text-dark fw-bold">Resetovanje lozinke</span>

            <div class="mb-1 ml-4 text-sm text-gray-600 text-secondary">
                <p>Unesi novu lozinku da bi mogao da pristupiš na <strong>Poslovi Online</strong> platformi.</p>
            </div>

            <div class="modal-body p-4">
                <!-- Prikazivanje poruke iz sesije -->
                @if (session('status'))
                    <div class="alert alert-success text-center">
                        <x-auth-session-status class="mb-1" :status="session('status')" />
                    </div>
                @else
                    <!-- Forma za reset link -->
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="mb-1">
                            <label for="email" class="form-label fw-bold">Email adresa</label>
                            <input type="email"
                                   id="email"
                                   class="form-control form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email', $request->email) }}"
                                   autofocus
                                   style="pointer-events: none; opacity: 0.5;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                         <!-- Lozinka i Potvrda -->
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i> Lozinka
                                    </label>
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                            value="{{ old('password') }}"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-1"></i> Ponovi lozinku
                                    </label>
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="form-control"
                                        value="{{ old('password') }}"
                                           required>
                                    <small id="passwordHelp" class="form-text text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-primary w-100 mb-4 mt-3" style="background-color: #198754 !important; color: white !important">
                            <i class="fas fa-save me-1"></i> Sačuvaj novu lozinku
                        </button>
                    </form>
                @endif
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
