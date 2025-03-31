@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-file-signature"></i> Dodaj ponudu</h4>
        <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Nazad</a>
    </div>

    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="row">
            <!-- Glavni sadržaj -->
            <div class="col-md-4 g-0">
                 <!-- Kategorije -->
               <div class="form-group">
                    <label for="category">Kategorija</label>
                    <select id="category" name="category" class="form-control">
                        <option value="" disabled selected>Izaberite kategoriju</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4 g-0">
                <div class="form-group">
                    <label for="subcategory">Podkategorija</label>
                    <select id="subcategory" name="subcategory" class="form-control" disabled>
                        <option value="" disabled selected>Izaberite podkategoriju</option>
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
                        <input type="text" name="title" id="title" class="form-control" value="" required>
                    </div>
                </div>
            </div>

            <!-- Vidljivost -->
            <div class="col-md-4 g-0">
                <div class="form-group">
                    @if(Auth::user()->package)
                        @if($seller['countPublicService'] < Auth::user()->package->quantity)
                            <div class="form-check text-end mt-5">
                                <input type="checkbox" name="visible" id="visiblee" class="form-check-input">
                                <label for="visiblee" class="form-check-label">Aktiviraj ponudu da bude javno vidljiva</label>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label"><i class="fa fa-align-left text-primary"></i> Opis</label>
            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Cene i dani isporuke -->
        <div class="row">
            @foreach (['start', 'standard', 'premium'] as $type)
                <div class="col-md-4">
                    <h5 class="package-category text-left">
                        <i class="fas
                            @if($type == 'basic') fa-box text-primary
                            @elseif($type == 'standard') fa-gift text-success
                            @elseif($type == 'premium') fa-gem text-warning
                            @endif">
                        </i>
                        {{ ucfirst($type) }} paket
                    </h5>
                    <div class="mb-3">
                        <label for="{{ $type }}_price" class="form-label"><i class="fas fa-credit-card text-secondary"></i> Cena</label>
                        <input type="number" name="{{ $type }}_price" id="{{ $type }}_price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_delivery_days" class="form-label"><i class="fas fa-hourglass-start text-secondary"></i> Rok isporuke</label>
                        <input type="number" name="{{ $type }}_delivery_days" id="{{ $type }}_delivery_days" class="form-control"  required>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_inclusions" class="form-label"><i class="fa fa-info-circle"></i> Šta je uključeno</label>
                        <textarea name="{{ $type }}_inclusions" id="{{ $type }}_inclusions" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Vidljivost -->
        <div class="mb-3 form-check">
            <input type="checkbox" name="visible" id="visiblee" class="form-check-input">
            <label for="visiblee" class="form-check-label">Javno vidljivo</label>
        </div>

        <!-- Upload slika -->
        <div class="mb-3">
            <label class="form-label">Dodaj slike (maks. 10)</label>
            <input type="file" name="serviceImages[]" class="form-control" multiple accept="image/*">
        </div>


        <button type="submit" class="btn btn-success w-100" style="background-color: #198754">Dodaj ponudu</button>
    </form>

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

</script>
@endsection
