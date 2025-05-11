@extends('layouts.app')
<title>Poslovi Online | Registracija</title>
<link href="{{ asset('css/index.css') }}" rel="stylesheet">
<!-- Dodajte CSS za intl-tel-input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">

<!-- Dodajte JS za intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<!-- Dodajte jQuery (ako već nije uključeno u vašem projektu) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('content')

<!-- Hero sekcija sa pozadinom -->
<div class="hero-section">
    <div class="hero-content text-center text-white">
        <span class="hero-subtitle">Kreiraj novi nalog</span>
        <h1 class="hero-title text-center">Poslovi<mark>online</mark></h1>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content col-md-8 mx-auto">
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
            <span class="hero-subtitle text-center text-dark fw-bold">Registracija novog korisnika</span>
            <div class="modal-body p-5">

                <!-- Prikaz poruke sa anchor ID -->
                @error('captcha')
                    <div id="register-message" class="alert alert-danger text-center">
                        {{ $message }}
                    </div>
                @enderror

                @if(session('success'))
                    <div id="register-message" class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @else

                <form method="POST" action="{{ route('register') }}" onsubmit="return validatePasswords()">
                    @csrf

                    <!-- Dodajte ovo na početak forme -->
                    <input type="hidden" name="affiliateCode" value="{{ request('affiliateCode') }}">

                    <!-- Ime i Prezime -->
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname" class="form-label">
                                    <i class="fas fa-user me-1"> </i>Ime
                                </label>
                                <input type="text"
                                       id="firstname"
                                       name="firstname"
                                       class="form-control @error('firstname') is-invalid @enderror"
                                       value="{{ old('firstname') }}"
                                       required>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname" class="form-label">
                                    <i class="fas fa-user-tag me-1"></i> Prezime
                                </label>
                                <input type="text"
                                       id="lastname"
                                       name="lastname"
                                       class="form-control @error('lastname') is-invalid @enderror"
                                    value="{{ old('lastname') }}"
                                       required>
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-1">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               required>
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
                                    <i class="fas fa-lock me-1"></i> Ponovite lozinku
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

                    <div class="row mb-3">
                        <!-- Telefon -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="hidden" name="phone" id="full_phone">
                                <label for="phone_input" class="form-label">
                                    <i class="fas fa-phone me-1"></i> Telefon
                                </label>
                                <input type="tel"
                                       id="phone_input"
                                       name="phone_input"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="XX/XXX-XXX"
                                       value="{{ old('phone') }}"
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                         <!-- Ulica -->
                        <div class="col-12 col-md-6">
                            <label for="street" class="form-label"><i class="fas fa-road me-1"></i> Ulica i broj</label>
                            <input type="text" id="street" name="street" class="form-control" value="{{ old('street') }}" required>
                        </div>


                    </div>

                    <div class="row mb-3">
                         <!-- Grad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label">
                                    <i class="fas fa-city me-1"></i> Grad
                                </label>
                                <input type="text"
                                       id="city"
                                       name="city"
                                       class="form-control"
                                       value="{{ old('city') }}"
                                       required>
                            </div>
                        </div>

                        <!-- Zemlja -->
                        <div class="col-12 col-md-6">
                            <label for="country" class="form-label"><i class="fas fa-globe me-1"></i> Zemlja</label>
                            <input type="text" id="country" name="country" class="form-control" value="{{ old('country') }}" required>
                        </div>
                    </div>


                    <!-- CAPTCHA -->
                    <div class="form-group d-flex align-items-center w-100">
                        <div class="d-flex align-items-center flex-grow-1">
                            <span id="math-question" class="mb-0"></span>
                            <input type="text" name="captcha" class="form-control form-control-sm py-0" style="width: 20% !important; margin-left: 5px;" required> &nbsp; <span class="ms-auto">nov zadatak</span>
                        </div>
                        <div class="text-end">
                            <a href="javascript:void(0);" id="refresh-captcha" class="ms-1 fa-xs text-decoration-none">
                                <i class="fa fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Prihvatam uslove -->
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" oninvalid="this.setCustomValidity('Prihvati uslove korišćenja.')" oninput="this.setCustomValidity('')" required>
                        <label class="form-check-label" for="terms">
                            Prihvatam <a href="{{ route('terms') }}" target="_blank">uslove korišćenja</a>
                        </label>
                        @error('terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mb-4">
                        <i class="fas fa-user-plus me-1"></i>Registruj se
                    </button>

                    <!-- Već imate nalog? -->
                    <div class="text-center">
                        <p>Već imaš nalog?
                            <a href="{{ route('login') }}" class="text-decoration-none">Prijavi se ovde</a>
                        </p>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    // Automatski prikaz modalnog prozora
    window.onload = function() {
        const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
    };

    // Provera lozinki
    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const passwordHelp = document.getElementById('passwordHelp');

        if(password !== confirmPassword) {
            passwordHelp.textContent = 'Lozinke se ne poklapaju!';
            return false;
        }
        return true;
    }

    // Provera u realnom vremenu
    document.getElementById('password_confirmation').addEventListener('input', function() {
        validatePasswords();
    });

    // Restrikcije za telefon
    document.getElementById('phone').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    // Onemogući zatvaranje modala klikom izvan
    document.getElementById('registerModal').addEventListener('show.bs.modal', function(event) {
        document.querySelector('.modal-backdrop').style.backgroundColor = 'rgba(0,0,0,0.7)';
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pošaljemo GET zahtev na server za novu CAPTCHA sliku
        fetch('{{ route('captcha') }}')
            .then(response => response.json())
            .then(data => {
                // Ažuriraj pitanje na stranici
                document.getElementById('math-question').textContent = data.question;
            })
            .catch(error => {
                console.error('Greška pri osvežavanju CAPTCHA:', error);
            });

    // Kada korisnik klikne na "Osveži CAPTCHA"
    document.getElementById('refresh-captcha').addEventListener('click', function() {
        // Pošaljemo GET zahtev na server za novu CAPTCHA sliku
        fetch('{{ route('captcha') }}')
            .then(response => response.json())
            .then(data => {
                // Ažuriraj pitanje na stranici
                document.getElementById('math-question').textContent = data.question;
            })
            .catch(error => {
                console.error('Greška pri osvežavanju CAPTCHA:', error);
            });
    });
});

// Kada se stranica učita, inicijalizujte intl-tel-input
document.addEventListener("DOMContentLoaded", function() {
    const phoneInput = document.querySelector("#phone_input");
    const hiddenPhone = document.querySelector("#full_phone");

    const iti = window.intlTelInput(phoneInput, {
        preferredCountries: ['rs', 'hr', 'ba', 'si', 'mk', 'gb', 'de', 'fr', 'it', 'es', 'ru'],
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });

    // Ažuriraj hidden polje pri svakoj promeni
    phoneInput.addEventListener('input', updateHiddenPhone);
    phoneInput.addEventListener('countrychange', updateHiddenPhone);

    // Obavezno ažuriraj pre slanja forme
    document.querySelector('form').addEventListener('submit', function(e) {
        updateHiddenPhone();
        if (!iti.isValidNumber()) {
            e.preventDefault();
            alert('Unesite validan broj telefona!');
        }
    });

    function updateHiddenPhone() {
        hiddenPhone.value = iti.getNumber();
    }
});
</script>
