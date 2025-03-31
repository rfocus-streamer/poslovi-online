@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-file-signature"></i> Uredi ponudu</h4>
        <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Nazad</a>
    </div>

    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Glavni sadržaj -->
            <div class="col-md-4 g-0">
                 <!-- Kategorije -->
                <div class="form-group">
                    <label for="category"><i class="fa fa-sitemap"></i> Kategorija</label>
                    <select id="category" name="category" class="form-control">
                        <option value="" disabled>Izaberite kategoriju</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @if($category->name == $service->category->name) selected @endif>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4 g-0">
                <div class="form-group">
                    <label for="subcategory"><i class="fa fa-caret-right"></i> Podkategorija</label>
                    <select id="subcategory" name="subcategory" class="form-control">
                        <option value="" disabled>Izaberite podkategoriju</option>
                        @foreach ($service->category->subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}"
                                    @if($subcategory->id == $service->subcategory->id) selected @endif>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div class="row">
            <!-- Title -->
            <div class="col-md-8 g-0">
                <div class="form-group">
                    <div class="mb-3">
                        <label for="title" class="form-label"><i class="fa fa-certificate text-primary"></i> Naslov</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ $service->title }}" required>
                    </div>
                </div>
            </div>

            <!-- Vidljivost -->
            <div class="col-md-4 g-0">
                <div class="form-group">
                    @if(Auth::user()->package)
                        @php
                            $expired = \Carbon\Carbon::parse($service->visible_expires_at)->isPast();
                        @endphp

                        @if($expired)
                            <div class="form-check text-end mt-5">
                                <input type="checkbox" name="visible" id="visiblee" class="form-check-input" {{ $service->visible ? 'checked' : '' }}>
                                <label for="visiblee" class="form-check-label">Aktiviraj ponudu da bude javno vidljiva</label>
                            </div>
                        @else
                            <div class="form-check text-end mt-4">
                                <i class="fa fa-eye text-success" title="Javno vidljivo do {{\Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y')}}"></i>
                                <strong>Ponuda je javno dostupna do:</strong><br>
                                {{\Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y H:i')}}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label"><i class="fa fa-align-left text-primary"></i> Opis</label>
            <textarea name="description" id="description" class="form-control" rows="4" required>{{ $service->description }}</textarea>
        </div>

        <!-- Cene i dani isporuke -->
        <div class="row">
            @foreach (['start', 'standard', 'premium'] as $type)
                <div class="col-md-4">
                    <h5 class="package-category text-left">
                        <i class="fas
                            @if($type == 'start') fa-box text-primary
                            @elseif($type == 'standard') fa-gift text-success
                            @elseif($type == 'premium') fa-gem text-warning
                            @endif">
                        </i>
                        {{ ucfirst($type) }} paket
                    </h5>
                    <div class="mb-3">
                        <label for="{{ $type }}_price" class="form-label"><i class="fas fa-credit-card text-secondary"></i> Cena</label>
                        <input type="number" name="{{ $type }}_price" id="{{ $type }}_price" class="form-control" value="{{ $service[str_replace('start','basic',$type).'_price'] }}" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_delivery_days" class="form-label"><i class="fas fa-hourglass-start text-secondary"></i> Rok isporuke</label>
                        <input type="number" name="{{ $type }}_delivery_days" id="{{ $type }}_delivery_days" class="form-control" value="{{ $service[str_replace('start','basic',$type).'_delivery_days'] }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_inclusions" class="form-label"><i class="fa fa-info-circle"></i> Šta je uključeno</label>
                        <textarea name="{{ $type }}_inclusions" id="{{ $type }}_inclusions" class="form-control" rows="2" required>{{ $service[str_replace('start','basic',$type).'_inclusions'] }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Upload slika -->
        <div class="mb-3">
            <label class="form-label">Dodaj slike (maks. {{ (10 - $service->serviceImages->count()) }})</label>
            <input type="file" name="serviceImages[]" class="form-control" multiple accept="image/*">
            <small class="text-muted">Možeš dodati još {{ (10 - $service->serviceImages->count()) }} slika.</small>
        </div>

       <!-- Prikaz trenutnih slika -->
        @if($service->serviceImages->count())
            <div class="mb-3">
                <h5>Postojeće slike</h5>
                <div class="d-flex flex-wrap">
                    @foreach($service->serviceImages as $image)
                        <div class="position-relative me-2 mb-2" id="image-{{ $image->id }}">
                            <img src="{{ asset('storage/services/' . $image->image_path) }}" alt="Service Image" width="100" class="rounded">
                            <!-- Dugme za brisanje slike -->
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image" data-image-id="{{ $image->id }}">✕</button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn-success w-100" style="background-color: #198754">Sačuvaj promene</button>
    </form>

    <!-- Modal za potvrdu brisanja -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Potvrda brisanja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <i class="fas fa-trash-alt text-danger"></i> Da li ste sigurni da želite da obrišete ovu sliku?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Obriši</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal za obaveštenje -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Obaveštenje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="notificationMessage">
                    <!-- Poruka će biti dinamički postavljena ovde -->
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    // Podaci o kategorijama i podkategorijama
    const categories = @json($categories); // Laravel JSON encoding kategorija sa podkategorijama

    document.getElementById('category').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategorySelect = document.getElementById('subcategory');

        // Resetujemo podkategorije
        subcategorySelect.innerHTML = '<option value="" disabled selected>Izaberite podkategoriju</option>';

        if (categoryId) {
            // Omogućiti selektovanje podkategorije
            subcategorySelect.disabled = false;

            // Pronaći odgovarajuću kategoriju
            const selectedCategory = categories.find(category => category.id == categoryId);

            // Dodati podkategorije za izabranu kategoriju
            selectedCategory.subcategories.forEach(function(subcategory) {
                const option = document.createElement('option');
                option.value = subcategory.id;
                option.textContent = subcategory.name;
                subcategorySelect.appendChild(option);
            });
        } else {
            // Ako nije izabrana kategorija, onemogućiti podkategoriju
            subcategorySelect.disabled = true;
        }
    });


document.querySelectorAll('.delete-image').forEach(button => {
    button.addEventListener('click', function () {
        const imageId = this.getAttribute('data-image-id'); // ID slike koju treba obrisati
        const imageElement = document.getElementById('image-' + imageId); // Element slike na stranici

        // Generisanje URL-a za brisanje slike
        const deleteImageUrl = "{{ route('services.image.delete', ':imageId') }}";
        // Zamenjujemo placeholder ":imageId" sa stvarnim ID-jem
        const url = deleteImageUrl.replace(':imageId', imageId);

        // Otvorite modal za potvrdu
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();

        // Kada korisnik potvrdi brisanje
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            // Slanje DELETE zahteva putem AJAX-a
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));

                if (data.success) {
                    // Ako je uspešno obrisana, ukloni sliku sa stranice
                    imageElement.remove();

                    // Prikazivanje uspešne poruke u modalnom oknu
                    document.getElementById('notificationMessage').innerHTML = '<i class="fas fa-check-circle text-success"></i> Slika je uspešno obrisana.';
                    notificationModal.show();
                    deleteModal.hide();

                    // Reload stranice nakon uspešnog brisanja
                    setTimeout(function() {
                        location.reload();
                    }, 2500); // Reload nakon 2.5 sekunde
                } else {
                    // Ako dođe do greške, prikaži grešku u modalnom oknu
                    document.getElementById('notificationMessage').innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Došlo je do greške pri brisanju slike.';
                    notificationModal.show();
                }
            })
            .catch(error => {
                console.error('Greška:', error);

                // Ako se desi greška pri slanju zahteva
                const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                document.getElementById('notificationMessage').innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Došlo je do greške.';
                notificationModal.show();
            });
        });
    });
});

</script>
@endsection
