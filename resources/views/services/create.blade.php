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

    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" id="serviceForm">
        @csrf
        @method('POST')

        <!-- Status Message -->
        <div id="statusMessage" class="alert alert-danger text-center" style="display: none;"></div>

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

        <!-- Upload slika -->
        <div class="mb-3">
            <label class="form-label">Dodaj slike (maks. 10)</label>
            <input type="file" name="serviceImages[]" class="form-control" multiple accept="image/*">
            <div class="d-flex">
                <small class="text-muted ms-auto">* Dozvoljena maksimalna veličina slike 2MB</small>
            </div>
        </div>

        <!-- Progress Bar Container -->
        <div class="upload-progress" id="upload-progress" style="display: none; margin: 20px 0; position: relative; height: 30px; border-radius: 5px; background-color: #f0f0f0;">
            <div class="progress-bar" style="width: 0%; height: 100%; background-color: #4CAF50; transition: width 0.3s ease; border-radius: 5px; position: relative;">
                <div class="progress-text" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); color: white; font-weight: bold; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">0%</div>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100" style="background-color: #198754" id="submitBtn"><i class="fa fa-floppy-disk me-1"></i> Dodaj ponudu</button>
    </form>

</div>

<script>
document.getElementById('serviceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    const progressContainer = document.querySelector('.upload-progress');
    const statusMessage = document.getElementById('statusMessage');

    // Prikaži progress bar
    progressContainer.style.display = 'block';
    submitBtn.disabled = true;
    statusMessage.style.display = 'none';

    try {
        const formData = new FormData(form);

        // Provera broja slika pre slanja
        const files = formData.getAll('serviceImages[]');
        if (files.length > 10) {
            showError('Možete uploadovati najviše 10 slika odjednom.');
            return;
        }

        // Računanje ukupne veličine za progress
        let totalSize = 0;
        for (const [name, value] of formData.entries()) {
            if (name === 'serviceImages[]' && value instanceof File) {
                totalSize += value.size;
            }
        }

        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
        });

        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 422) {
                showError('Jedna ili više slika premašuje maksimalnu dozvoljenu veličinu od 2MB');
                return;
            }
            throw new Error(errorData.message || 'Došlo je do greške');
        }

        const reader = response.body.getReader();
        let received = 0;
        let chunks = [];

        while(true) {
            const {done, value} = await reader.read();

            if(done) {
                break;
            }

            chunks.push(value);
            received += value.length;

            // Update progress bara
            const progress = Math.round((received / totalSize) * 100);
            progressBar.style.width = `${progress}%`;
            progressText.textContent = `${progress}%`;
        }

        // Finalni rezultat
        const body = new Uint8Array(received);
        let position = 0;
        for(const chunk of chunks) {
            body.set(chunk, position);
            position += chunk.length;
        }

        const result = new TextDecoder("utf-8").decode(body);
        const jsonResponse = JSON.parse(result);

        if(jsonResponse.redirect) {
            window.location.href = jsonResponse.redirect;
        } else if(jsonResponse.error) {
            showError(jsonResponse.error);
        }

    } catch (error) {
        showError('Došlo je do greške prilikom upload-a');
    } finally {
        submitBtn.disabled = false;
    }
});

function showError(message) {
    const statusMessage = document.getElementById('statusMessage');
    statusMessage.style.display = 'block';
    statusMessage.textContent = message;
      // Dodajemo klasu za tranziciju
    statusMessage.classList.remove('hide');

     // Skrol do "upload-progress"
    document.getElementById('statusMessage')?.scrollIntoView({ behavior: 'smooth' });
    document.getElementById('upload-progress').style.display = 'none';

    const messageElementDanger = document.getElementById('statusMessage');
    if (messageElementDanger) {
        // Dodajemo klasu za tranziciju
        messageElementDanger.classList.add('fade-out');

        // Sakrijemo poruku nakon 5 sekundi
        setTimeout(() => {
            messageElementDanger.classList.add('hide');
        }, 5000); // Poruka će početi da nestaje nakon 5s
    }
    // Resetujemo file input
    resetFileInput();
}

function resetFileInput() {
    const fileInput = document.querySelector('input[name="serviceImages[]"]');
    if(fileInput) {
        // Kreirajemo novi input element da bismo resetovali stanje
        const newInput = document.createElement('input');
        newInput.type = 'file';
        newInput.name = 'serviceImages[]';
        newInput.multiple = true;
        newInput.accept = 'image/*';
        newInput.className = fileInput.className;

        // Zameni stari input sa novim
        fileInput.parentNode.replaceChild(newInput, fileInput);
    }
}
</script>

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
