@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')
<style>
    .package-item {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .package-category {
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    #add-package {
        margin-bottom: 15px;
    }

    .remove-package {
        font-size: 0.7rem;
        padding: 0.15rem 0.4rem;
    }
</style>
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-file-signature"></i> Uredi ponudu</h4>
        <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Nazad</a>
    </div>

    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data" id="serviceForm">
        @csrf
        @method('PUT')

        <!-- Status Message -->
        <div id="statusMessage" class="alert alert-danger text-center" style="display: none;"></div>

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
        <div class="row" id="packages-container">
            @php
                $existingPackages = [];
                foreach (['basic', 'standard', 'premium'] as $type) {
                    if (!is_null($service->{$type.'_price'})) {
                        $existingPackages[] = $type;
                    }
                }
            @endphp
            @foreach($existingPackages as $type)
                <div class="col-md-4 package-item" data-package-type="{{ $type }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="package-category text-left">
                            <i class="fas
                                @if($type == 'basic') fa-box text-primary
                                @elseif($type == 'standard') fa-gift text-success
                                @elseif($type == 'premium') fa-gem text-warning
                                @endif">
                            </i>
                            {{ ucfirst($type) }} paket
                        </h5>
                        @if($type != 'basic')
                            <button type="button" class="btn btn-sm btn-danger remove-package">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_price" class="form-label"><i class="fas fa-credit-card text-secondary"></i> Cena</label>
                        <input type="number" name="{{ $type }}_price" id="{{ $type }}_price"
                               class="form-control" value="{{ $service->{$type.'_price'} }}" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_delivery_days" class="form-label"><i class="fas fa-hourglass-start text-secondary"></i> Rok isporuke</label>
                        <input type="number" name="{{ $type }}_delivery_days" id="{{ $type }}_delivery_days"
                               class="form-control" value="{{ $service->{$type.'_delivery_days'} }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $type }}_inclusions" class="form-label"><i class="fa fa-info-circle"></i> Šta je uključeno</label>
                        <textarea name="{{ $type }}_inclusions" id="{{ $type }}_inclusions"
                                  class="form-control" rows="2" required>{{ $service->{$type.'_inclusions'} }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Dugme za dodavanje novog paketa -->
        <div class="text-center">
            <small class="text-muted ms-2">Maksimalno 3 paketa</small><br>
            <button type="button" id="add-package" class="btn btn-poslovi btn-sm">
                <i class="fas fa-plus"></i> Dodaj paket
            </button>
        </div>

        <!-- Upload slika -->
        <div class="mb-3">
            <label class="form-label">Dodaj slike (maks. <span class="remaining-slots">{{ 10 - $service->serviceImages->count() }}</span>)</label>
            <input type="file" name="serviceImages[]" class="form-control" multiple accept="image/*">
            <div class="d-flex justify-content-between">
                <small class="text-muted">* Možeš dodati još <span class="remaining-slots">{{ 10 - $service->serviceImages->count() }}</span> slika.</small>
                <small class="text-muted">* Dozvoljena maksimalna veličina slike 2MB</small>
            </div>
        </div>

        <!-- Progress Bar Container -->
        <div class="upload-progress" id="upload-progress" style="display: none; margin: 20px 0; position: relative; height: 30px; border-radius: 5px; background-color: #f0f0f0;">
            <div class="progress-bar" style="width: 0%; height: 100%; background-color: #4CAF50; transition: width 0.3s ease; border-radius: 5px; position: relative;">
                <div class="progress-text" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); color: white; font-weight: bold; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">0%</div>
            </div>
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

        <button type="submit" class="btn btn-success w-100" style="background-color: #198754" id="submitBtn"><i class="fa fa-floppy-disk me-1"></i> Sačuvaj promene</button>
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
                <div class="modal-body text-center" id="notificationMessage">
                    <!-- Poruka će biti dinamički postavljena ovde -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal" aria-label="Zatvori"><i class="fas fa-check-circle"></i> U redu</button>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const packagesContainer = document.getElementById('packages-container');
            const addPackageBtn = document.getElementById('add-package');
            let packageCount = {{ count($existingPackages) }};
            const existingPackages = @json($existingPackages);

            const packageTypes = {
                basic: { icon: 'fa-box', color: 'text-primary', name: 'Osnovni' },
                standard: { icon: 'fa-gift', color: 'text-success', name: 'Standardni' },
                premium: { icon: 'fa-gem', color: 'text-warning', name: 'Premium' }
            };

            function addPackage(packageKey, data = {}) {
                const packageType = packageTypes[packageKey];
                const packageHtml = `
                    <div class="col-md-4 package-item" data-package-type="${packageKey}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="package-category text-left">
                                <i class="fas ${packageType.icon} ${packageType.color}"></i> ${packageType.name} paket
                            </h5>
                            ${packageKey !== 'basic' ? '<button type="button" class="btn btn-sm btn-danger remove-package"><i class="fas fa-times"></i></button>' : ''}
                        </div>
                        <div class="mb-3">
                            <label for="${packageKey}_price" class="form-label"><i class="fas fa-credit-card text-secondary"></i> Cena</label>
                            <input type="number" name="${packageKey}_price" value="${data.price || ''}" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="${packageKey}_delivery_days" class="form-label"><i class="fas fa-hourglass-start text-secondary"></i> Rok isporuke</label>
                            <input type="number" name="${packageKey}_delivery_days" value="${data.delivery_days || ''}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="${packageKey}_inclusions" class="form-label"><i class="fa fa-info-circle"></i> Šta je uključeno</label>
                            <textarea name="${packageKey}_inclusions" class="form-control" rows="2" required>${data.inclusions || ''}</textarea>
                        </div>
                    </div>
                `;

                packagesContainer.insertAdjacentHTML('beforeend', packageHtml);
                packageCount++;

                if (packageCount >= 3) {
                    addPackageBtn.disabled = true;
                }
            }

            addPackageBtn.addEventListener('click', function() {
                if (packageCount >= 3) {
                    alert('Možete dodati maksimalno 3 paketa.');
                    return;
                }

                const availablePackages = ['basic', 'standard', 'premium'].filter(p =>
                    !existingPackages.includes(p) &&
                    !Array.from(packagesContainer.querySelectorAll('.package-item'))
                         .some(item => item.dataset.packageType === p)
                );

                if (availablePackages.length > 0) {
                    addPackage(availablePackages[0]);
                    existingPackages.push(availablePackages[0]);
                }
            });

            packagesContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-package')) {
                    const packageItem = e.target.closest('.package-item');
                    const packageType = packageItem.dataset.packageType;

                    if (packageType === 'basic') return;

                    packageItem.remove();
                    packageCount--;
                    const index = existingPackages.indexOf(packageType);
                    if (index > -1) existingPackages.splice(index, 1);

                    addPackageBtn.disabled = false;
                }
            });
        });
</script>

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


document.querySelectorAll('.delete-image').forEach(button => {
    button.addEventListener('click', function () {
        const imageId = this.getAttribute('data-image-id');
        const imageElement = document.getElementById('image-' + imageId);
        const deleteImageUrl = "{{ route('services.image.delete', ':imageId') }}".replace(':imageId', imageId);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        deleteModal.show();

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            fetch(deleteImageUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));

                if (data.success) {
                    imageElement.remove();

                    // Ažuriranje preostalih slotova
                    const currentImages = document.querySelectorAll('[id^="image-"]').length;
                    const remainingSlots = 10 - currentImages;

                    document.querySelectorAll('.remaining-slots').forEach(element => {
                        element.textContent = remainingSlots;
                    });

                    const fileInput = document.querySelector('input[name="serviceImages[]"]');
                    fileInput.disabled = remainingSlots <= 0;
                    fileInput.parentNode.querySelector('label').style.opacity = remainingSlots <= 0 ? 0.5 : 1;

                    document.getElementById('notificationMessage').innerHTML =
                        '<i class="fas fa-check-circle text-success"></i> Slika je uspešno obrisana.';
                    notificationModal.show();
                    deleteModal.hide();
                } else {
                    document.getElementById('notificationMessage').innerHTML =
                        '<i class="fas fa-exclamation-triangle text-danger"></i> ' + data.message;
                    notificationModal.show();
                }
            })
            .catch(error => {
                console.error('Greška:', error);
                const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                document.getElementById('notificationMessage').innerHTML =
                    '<i class="fas fa-exclamation-triangle text-danger"></i> Došlo je do greške.';
                notificationModal.show();
            });
        }, {once: true});
    });
});

</script>
@endsection
