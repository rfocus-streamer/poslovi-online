@section('content')
<style>
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }

    .blinking-alert {
        animation: blink 1s infinite;
    }

    .avatar-img {
        object-fit: cover;
    }
</style>



<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <form method="POST" action="{{ route('admin.profile.update', $user) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH') <!-- Dodajemo PATCH metod jer forma koristi POST -->
                <!-- Avatar Upload -->
                <div class="form-group mb-3 text-center">
                    <img src="{{ Storage::url('user/' . $user->avatar) }}"
                                         alt="Avatar" class="rounded-circle avatar-img" width="100" height="100">
                </div>

                <!-- Ime i Prezime -->
                <div class="row mb-1">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label"><i class="fas fa-user me-1"></i> Ime</label>
                        <input type="text" id="firstname" name="firstname" class="form-control" value="{{ $user->firstname }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label"><i class="fas fa-user-tag me-1"></i> Prezime</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" value="{{ $user->lastname }}" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group mb-1">
                    <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" disabled="">
                </div>

                <!-- Telefon -->
                <div class="form-group mb-1">
                    <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i> Telefon</label>
                    <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{9,15}" placeholder="06X/XXX-XXX" value="{{ $user->phone }}" required>
                </div>

                <div class="row mb-3">
                    <!-- Ulica -->
                    <div class="col-12 col-md-4">
                        <label for="street" class="form-label"><i class="fas fa-road me-1"></i> Ulica i broj</label>
                        <input type="text" id="street" name="street" class="form-control" value="{{ $user->street ?? '' }}" required>
                    </div>

                    <!-- Grad -->
                    <div class="col-12 col-md-4">
                        <label for="city" class="form-label"><i class="fas fa-city me-1"></i> Grad</label>
                        <input type="text" id="city" name="city" class="form-control" value="{{ $user->city ?? '' }}" required>
                    </div>

                    <!-- Zemlja -->
                    <div class="col-12 col-md-4">
                        <label for="country" class="form-label"><i class="fas fa-globe me-1"></i> Zemlja</label>
                        <input type="text" id="country" name="country" class="form-control" value="{{ $user->country ?? '' }}" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn text-white w-100 mb-4" style="background-color: #198754">
                    <i class="fa fa-floppy-disk me-1"></i> Sačuvaj
                </button>
            </form>
        </div>
    </div>
</div>
