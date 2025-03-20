@extends('layouts.app')
<link href="{{ asset('css/show.css') }}" rel="stylesheet">
<title>Poslovi Online | {{ $title }}</title>
@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Glavni sadržaj -->
        <div class="col-md-8 g-0">
            <!-- Naslov i osnovne informacije -->
             <h4><i class="fas fa-project-diagram"></i> <a class="text-dark" href="{{ route('services.show', $project->service->id) }}">{{ $project->service->title }}</a></h4>
            <div class="d-flex align-items-center mb-1">

                @auth
                <div class="ms-auto mt-3"> <!-- Ovo gura dugmad na desno -->
                    @if(Auth::user()->role == 'buyer')
                        @switch($project->status)
                            @case('waiting_confirmation')
                                <div class="text-center">
                                    <i class="fas fa-hand-point-down text-primary"></i> Kliknite da preduzmete akciju <i class="fas fa-hand-point-down text-primary"></i>
                                </div>
                                <div class="d-flex gap-2 justify-content-center">
                                    <form action="{{ route('projects.confirmationdone', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Završen <i class="fas fa-check-circle"></i></button>
                                    </form>

                                    <form action="{{ route('projects.confirmationuncompletebuyer', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Nije završen <i class="fas fa-exclamation-triangle"></i></button>
                                    </form>

                                    <form action="{{ route('projects.confirmationdone', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Korekcije <i class="fas fa-undo-alt"></i></button>
                                    </form>
                                </div>
                            @break
                        @endswitch
                    @endif
                </div>
                @endauth
            </div>

            <div>
                <!-- <i class="fas fas fa-project-diagram"></i> Trenutni status projekta<br> -->
                <ul class="list-unstyled">
                @switch($project->status)
                    @case('inactive')
                        <li class="mb-2">
                            <i class="fas fa-hourglass-start text-secondary" style="font-size: 1.1em;"></i>
                            <strong>Čeka se prihvat:</strong><br> Čeka se izvršilac da prihvati projekat.
                        </li>
                    @break

                    @case('in_progress')
                        <li class="mb-2">
                            <i class="fas fa-tasks text-primary"></i>
                            <strong>U toku</strong><br> Izvršilac je prihvatio projekat i radi na njemu.
                        </li>
                    @break

                    @case('waiting_confirmation')
                        <li class="mb-2">
                            <i class="fas fa-user-check text-primary"></i>
                            <strong>Čeka se odobrenje</strong><br>
                            <strong>Vi možete da preduzmete akciju:</strong><br>
                            <i class="fas fa-check-circle text-success"></i> Izvršilac je završio projekat prema vašim očekivanjima.<br>
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Projekat nije završen, rezervisana sredstva biće zamrznuta.<br>
                            <span style="margin-left: 3%;"><i class="fas fa-exclamation-circle text-warning"></i> Izvršilac može uložiti prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                            <i class="fas fa-undo-alt  text-danger"></i>
                            Zahtevate dodatne korekcije pre finalnog kompletiranja.<br>
                            <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_kupca">Dodajte korekcije u odeljku vaših datoteka</a></span>
                        </li>
                    @break

                    @case('rejected')
                        <li class="mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                            <strong>Odbijeno:</strong><br> Izvršilac je odbio projekat, sredstva su vama refundirana.
                        </li>
                    @break

                    @case('requires_corrections')
                        <li class="mb-2">
                            <i class="fas fa-undo-alt  text-danger"></i>
                            <strong> Potrebne su korekcije:</strong><br>
                            <span style="margin-left: 3%;">Zahtevate dodatne korekcije pre finalnog kompletiranja</span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_kupca">Pogledajte u odeljku vaših datoteka</a></span>
                        </li>
                    @break

                    @case('completed')
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Završeno:</strong><br> Čestitamo projekat je uspešno završen, sredstva su prebačena izvršiocu.
                        </li>
                    @break

                    @case('uncompleted')
                         <li class="mb-2">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <strong>Nije završeno</strong> <br>
                            <span style="margin-left: 3%;">Označili ste da projekat nije završen, rezervisana sredstva su zamrznuta.</span><br>
                            <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-warning"></i> Izvršilac može uložiti prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                        </li>
                    @break
                @endswitch
                </ul>
            </div>
        </div>

        <!-- O prodavcu -->
        <div class="col-md-4 mb-1 g-0">
            <div class="card">
                <div class="card-body">
                <h4 class="card-title text-center text-success">O prodavcu</h4>

                    <!-- Osnovne informacije -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('user/' . $service->user->avatar) }}"
                                 class="rounded-circle"
                                 alt="Avatar prodavca"
                                 width="50"
                                 height="50">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>{{ $service->user->firstname .' '.$service->user->lastname }}</h6>
                            @php
                                $sellerLevels = [
                                    0 => 'Kupac',
                                    1 => 'Novi prodavac',
                                    2 => 'Level 1 prodavac',
                                    3 => 'Level 2 prodavac',
                                    4 => 'Top Rated prodavac',
                                ];

                                $sellerLevelName = $sellerLevels[$service->user->seller_level] ?? 'Nepoznat nivo';
                            @endphp
                            <small class="text-center">{{ $sellerLevelName }}</small>
                        </div>
                    </div>

                     <div class="text-warning ms-auto mb-4">
                            <p class="text-secondary">Ukupno ponuda: {{ $userServiceCount }}</p>
                            <p class="text-secondary">Ukupna ocena:
                                @if ($service->user->stars > 0)
                                    @for ($j = 1; $j <= $service->user->stars; $j++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                @elseif ($service->user->stars == 0)
                                    <p>No stars available</p>
                                @endif

                                <small class="ms-2">({{ $service->user->stars }})</small>
                            </p>
                    </div>

                    @auth
                        <!-- Dugme za kontakt -->
                        <a href="#" class="btn btn-success w-100">
                            <i class="fas fa-envelope me-2"></i>Kontaktirajte prodavca
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Detalji projekta -->
        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-hashtag text-primary"></i> <strong>ID projekta:</strong> {{ $project->project_number }}</li>
                        <li><i class="fas fa-box text-success"></i> <strong>Paket:</strong> {{ $project->package }}</li>
                        <li><i class="fas fa-layer-group text-warning"></i> <strong>Količina:</strong> {{ $project->quantity }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-calendar-alt text-info"></i> <strong>Datum početka:</strong> {{ $project->start_date ?? 'N/A' }}</li>
                        <li><i class="fas fa-calendar-check text-success"></i> <strong>Datum završetka:</strong> {{ $project->end_date ?? 'N/A' }}</li>
                        <li><i class="fas fa-credit-card text-danger"></i> <strong>Rezervisana sredstva:</strong> {{ number_format($project->reserved_funds, 2) }} RSD</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Dodatna naplata</h6>
                    <p>Trenutno nema zahteva od izvršioca</p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h5 class="card-title mb-4 text-success">
                            <i class="fas fa-tasks text-dark"></i> Detalji projekta
                        </h5>

                        @if($project->status === 'inactive')
                            <!-- Forma za uređivanje opisa -->
                            <form action="{{ route('projects.updateDescription', $project) }}" method="POST">
                                @csrf
                                @method('PUT') <!-- Koristimo PUT metod za ažuriranje -->

                                <div class="mb-3">
                                    <textarea name="description" class="form-control" rows="3">{{ $project->description }}</textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Snimi
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="mb-3">
                                <textarea name="description" class="form-control" rows="3" disabled="">{{ $project->description }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije</h6>
                    <p>Možete popunjavati detalje projekta sve dok izvršilac ne prihvati projekat. Nakon prihvatanja, izmene više neće biti moguće</p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <h5><i class="fas fa-folder-open"></i></i> <span class="card-title mb-4 text-success">Datoteke izvršioca</span></h5>
                     @php
                        // Filtriranje fajlova koje je upload-ovao prodavac
                        $sellerFiles = $project->files->filter(function ($file) {
                            return $file->user_id === $file->project->seller_id;
                        });
                    @endphp

                    @if($sellerFiles->count() > 0)
                        <ul class="list-unstyled mt-3">
                            @foreach($sellerFiles as $key => $file)
                                <li>
                                    <a href="{{ route('project.file.download', $file) }}" target="_blank">{{$key+1}}. {{ $file->original_name }}</a>
                                    <p class="text-muted small mb-0">{{ $file->description }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Trenutno nema datoteka od izvršioca</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0" id="datoteke_kupca">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-0">
                        <i class="fas fa-folder-open"></i>
                        <span class="text-success">Vaše datoteke</span>
                    </h5>

                    <form action="{{ route('projects.upload', $project) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-baseline gap-2 ms-auto mt-1">
                            @csrf
                        <input type="file" name="files[]" multiple class="form-control form-control-sm">
                        <input type="text" name="description" placeholder="Opis fajla" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-success btn-sm">Upload</button>
                    </form>

                    @php
                        // Filtriranje fajlova koje je upload-ovao kupac
                        $buyerFiles = $project->files->filter(function ($file) {
                            return $file->user_id === $file->project->buyer_id;
                        });
                    @endphp

                    @if($buyerFiles->count() > 0)
                        <ul class="list-unstyled mt-3">
                            @foreach($buyerFiles as $key => $file)
                                <li>
                                    <a href="{{ route('project.file.download', $file) }}" target="_blank">{{$key+1}}. {{ $file->original_name }}</a>
                                    <p class="text-muted small mb-0">{{ $file->description }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Trenutno nema vaših datoteka</p>
                    @endif
                </div>
            </div>
        </div>


        <div class="col-md-8 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <h5><i class="fas fa-bullhorn"></i></i> <span class="card-title mb-4 text-success">Recinzija</span></h5>
                    <p>Recenziju možete ostaviti tek nakon što se kompletira projekat ili nakon arbitraže</p>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
