@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<title>Poslovi Online | {{ $title }}</title>
@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Prikaz poruke sa anchor ID -->
        @if(session('success'))
            <div id="service-message" class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="service-message-danger" class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- Glavni sadržaj -->
        <div class="col-md-8 g-0">
            <!-- Naslov i osnovne informacije -->
             <h4><i class="fas fa-project-diagram"></i> <a class="text-dark" href="{{ route('services.show', $project->service->id) }}">{{ $project->service->title }}</a></h4>
            <div class="d-flex align-items-center mb-1">

                @auth
                <div class="ms-auto mt-3"> <!-- Ovo gura dugmad na desno -->
                    @if(Auth::user()->role == 'buyer')
                        @switch($project->status)
                            @case('inactive')
                                <div class="d-flex gap-2 text-center">
                                    <form action="{{ route('projects.rejectoffer', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Otkaži posao <i class="fas fa-times-circle"></i></button>
                                    </form>
                                </div>
                            @break
                            @case('waiting_confirmation')
                                <div class="text-center">
                                    <i class="fas fa-hand-point-down text-primary"></i> Klikni da preduzmeš akciju <i class="fas fa-hand-point-down text-primary"></i>
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

                                    <form action="{{ route('projects.confirmationcorrectionbuyer', $project) }}" method="POST">
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
                            <strong>Čeka se prihvat:</strong><br> Čeka se izvršilac da prihvati posao.
                        </li>
                    @break

                    @case('in_progress')
                        <li class="mb-2">
                            <i class="fas fa-tasks text-primary"></i>
                            <strong>U toku</strong><br> Izvršilac je prihvatio posao i radi na njemu.
                        </li>
                    @break

                    @case('waiting_confirmation')
                        <li class="mb-2">
                            <i class="fas fa-user-check text-primary"></i>
                            <strong>Čeka se odobrenje</strong><br>
                            <strong>Možeš da preduzmeš sledeće akcije:</strong><br>
                            <i class="fas fa-check-circle text-success"></i> Izvršilac je završio posao prema tvojim očekivanjima.<br>
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Posao nije završen, rezervisana sredstva biće zamrznuta.<br>
                            <span style="margin-left: 3%;"><i class="fas fa-exclamation-circle text-warning"></i> Izvršilac može uložiti prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                            <i class="fas fa-undo-alt  text-danger"></i>
                            Zahtevaš dodatne korekcije pre finalnog kompletiranja.<br>
                            <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_kupca">Dodaj korekcije u odeljku tvojih datoteka</a></span>
                        </li>
                    @break

                    @case('rejected')
                        <li class="mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                            <strong>Odbijeno:</strong><br> Izvršilac je odbio posao, sredstva su tebi refundirana.
                        </li>
                    @break

                    @case('requires_corrections')
                        <li class="mb-2">
                            <i class="fas fa-undo-alt  text-danger"></i>
                            <strong> Potrebne su korekcije:</strong><br>
                            <span style="margin-left: 3%;">Zahtevaš dodatne korekcije pre finalnog kompletiranja</span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_kupca">Dodaj korekcije ( datoteku sa opisom ) u odeljku tvojih datoteka</a></span>
                        </li>
                    @break

                    @case('completed')
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Završeno:</strong><br> Čestitamo posao je uspešno završen, sredstva su prebačena izvršiocu.
                        </li>
                    @break

                    @case('uncompleted')

                        @if($project->admin_decision === 'rejected')
                                <i class="fas fa-reply text-danger"></i>
                                <strong>Nekompletiran</strong><br>
                                @if($countReply > 0)
                                    <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-danger"></i> Izvršilac je uložio prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                                @endif
                                <span style="margin-left: 6%;"><i class="fas fa-times-circle text-danger"></i> Podrška je odbila prigovor prodavca, te rezervisana sredstva su vraćena na tvoj račun.</span>
                        @elseif($project->admin_decision === 'accepted')
                                <i class="fas fa-reply text-danger"></i>
                                <strong>Delimično ili potpuno završen</strong><br>
                                @if($countReply > 0)
                                    <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-danger"></i> Izvršilac je uložio prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                                @endif
                                <span style="margin-left: 6%;"><i class="fas fa-check-circle text-success"></i> Podrška je prihvatila prigovor izvršioca</span><br>
                                <span style="margin-left: 6%;"><i class="fas fa-check-circle text-success"></i> Rezervisana sredstva su prebačena na račun prodavca.</span>
                        @elseif($project->admin_decision === null and $project->seller_uncomplete_decision === 'accepted')
                            <li class="mb-2">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <strong>Nije završeno</strong> <br>
                                <span style="margin-left: 6%;"><i class="fas fa-check-circle text-success"></i> Izvršilac se složio da je posao "nekompletiran", sredstva su tebi refundirana.</span>
                            </li>
                        @elseif($project->admin_decision === 'partially')
                            <li class="mb-2">
                                <i class="fas fa-adjust text-warning"></i>
                                <strong>Delimično završen</strong> <br>
                                <span style="margin-left: 3%;"> Rezervisana sredstva su podeljena između tvog i prodavčevog računa a po visini procene podrške.</span>
                            </li>
                        @else
                             <li class="mb-2">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <strong>Nije završeno</strong> <br>
                                <span style="margin-left: 3%;">Označio si da posao nije završen, rezervisana sredstva su zamrznuta.</span><br>
                                <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-warning"></i> Izvršilac može uložiti prigovor ili prihvatiti posao kao nekompletiran.</span><br>
                                <span style="margin-left: 9%;">Ako je status posla "nekompletiran" sredstva se refundiraju na tvoj račun.</span><br>

                                @if($countReply > 0)
                                    <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-danger"></i> Izvršilac je uložio prigovor. Naša podrška će doneti konačnu odluku.</span>
                                @endif
                            </li>
                        @endif
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
                                <div class="text-warning">
                                @if ($service->user->stars > 0)
                                    @for ($j = 1; $j <= 5; $j++)
                                        @if ($j <= $service->user->stars)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                @elseif ($service->user->stars == 0)
                                    <p>No stars available</p>
                                @endif
                                 <small class="ms-2">({{ $service->user->stars }})</small>
                                </div>
                            </p>
                    </div>

                    @auth
                        @if($project->admin_decision === null)
                            <!-- Dugme za kontakt -->
                             @php
                                $encryptedServiceId = Crypt::encrypt($project->service->id);
                                $encryptedUserId = Crypt::encryptString($project->seller->id);
                                $encryptedUserId = route('messages.index', ['service_id' => $encryptedServiceId, 'seller_id' => $encryptedUserId]);
                            @endphp
                            <a href="{{$encryptedUserId}}" class="btn btn-success w-100">
                                <i class="fas fa-envelope me-2"></i>Kontaktiraj prodavca
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Detalji projekta -->
        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-hashtag text-primary"></i> <strong>ID posla:</strong> {{ $project->project_number }}</li>
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
                        <li><i class="fas fa-credit-card text-danger"></i> <strong>Rezervisana sredstva:</strong> {{ number_format(($project->reserved_funds + $project->commission->amount * 0.03), 2) }} €</i></li>
                    </ul>
                </div>
            </div>
        </div>

        @if($project->additionalCharges->count() > 0)
            @php
                $hasWaitingConfirmation = false; // Proverava da li postoji bar jedan zahtev sa statusom 'waiting_confirmation'
            @endphp

            @foreach($project->additionalCharges as $charge)
                @if($charge->status == 'waiting_confirmation')
                    @php
                        $hasWaitingConfirmation = true; // Ako postoji zahtev sa statusom 'waiting_confirmation'
                    @endphp
                    <div class="col-md-4 mb-2 g-0">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Prodavac zahteva dodatnu naplatu</h6>
                                <div class="ml-2">
                                    <div class="text-muted small text-end">
                                        {{ $charge->created_at->format('d.m.Y H:i') }}
                                    </div>
                                    <strong>Iznos:</strong> {{ number_format($charge->amount, 2) }} €<br>
                                    <strong>Razlog:</strong> {{ $charge->reason }}

                                    <div class="mt-3">
                                        <div class="d-flex gap-2 justify-content-center w-100">
                                            @if(Auth::user()->deposits < $charge->amount)
                                                <!-- Ako korisnik nema dovoljno novca, prikazujemo dugme za deponovanje novca -->
                                                <a href="{{ route('deposit.form') }}" data-bs-toggle="tooltip" title="Deponuj novac"> <button class="btn btn-warning ms-auto w-100 btn-sm" data-bs-toggle="tooltip" title="Deponuj novac">Dopuni <i class="fas fa-credit-card"></i></button>
                                                </a>
                                            @else
                                                <form action="{{ route('additional_charges.accept', $charge) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Odobri <i class="fas fa-check-circle"></i></button>
                                                </form>
                                            @endif

                                            <form action="{{ route('additional_charges.reject', $charge) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Odbij <i class="fas fa-times-circle"></i></button>
                                            </form>
                                        </div>

                                        <!-- small tag is now placed below the buttons and centered -->
                                       <!--  <div class="text-center" style="margin-top: -15px !important">
                                            <small>Odobri ili odbij zahtev</small>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Ako nema zahteva sa statusom 'waiting_confirmation', prikazi poruku -->
            @if(!$hasWaitingConfirmation)
                <div class="col-md-4 mb-2 g-0">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Dodatna naplata</h6>
                            <p>Trenutno nema zahteva od izvršioca</p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="col-md-4 mb-2 g-0">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Dodatna naplata</h6>
                        <p>Trenutno nema zahteva od izvršioca</p>
                    </div>
                </div>
            </div>
        @endif

        @if($project->additionalCharges->count() > 0)
            <div class="col-md-8 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Lista odobrenih zahteva za dodatnu naplatu</h6>
                        <ul class="list-unstyled">
                            @foreach($project->additionalCharges as $charge)
                                @if($charge->status == 'completed')
                                    <li class="mb-1 card">
                                        <div class="ml-2">
                                            <strong>Iznos:</strong> {{ number_format($charge->amount, 2) }} €<br>
                                            @if($charge->status == 'completed')
                                                <strong>Status: </strong> <i class="fas fas fa-check-circle text-success"></i> Odobrio si zahtev, zahtevan iznos je dodat u rezervisana sredstva <br>
                                            @endif
                                            <strong>Razlog:</strong> {{ $charge->reason }}
                                                <div class="text-muted small text-end">
                                                    {{ $charge->created_at->format('d.m.Y H:i') }}
                                                </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-8 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h5 class="card-title mb-4 text-success">
                            <i class="fas fa-tasks text-dark"></i> Detalji posla
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
                    <p>Možeš popunjavati detalje posla sve dok izvršilac ne prihvati posao. Nakon prihvatanja, izmene više neće biti moguće</p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0">
            <div class="card">
                <div class="card-body mb-5">
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
                        <p>Nema datoteka od izvršioca</p>
                    @endif
                </div>
            </div>
        </div>

         <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body mb-2">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o datotekama</h6>
                    <small>Izvršilac će u ovom odeljku dodati datoteke vezane za posao, kojim ćeš moći da pregledaš u toku rada. Takođe, po završetku posla, sve relevantne datoteke će biti dostupne.</small>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0" id="datoteke_kupca">
            <div class="card">
                <div class="card-body mb-4">
                    <h5 class="mb-0">
                        <i class="fas fa-folder-open"></i>
                        <span class="text-success">Tvoje datoteke</span>
                    </h5>

                    @if($project->admin_decision === null)
                        <form action="{{ route('projects.upload', $project) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-baseline gap-2 ms-auto mt-1">
                                @csrf
                            <input type="file" name="files[]" multiple class="form-control form-control-sm">
                            <input type="text" name="description" placeholder="Opis fajla" class="form-control form-control-sm">
                            <button type="submit" class="btn btn-success btn-sm">Upload</button>
                        </form>
                    @endif

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
                        <br>
                        <p>Nisi dodao ništa od datoteka</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body mb-3">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o tvojoj datoteci</h6>
                    <small>Možeš dostaviti datoteke neophodne za rad na poslu, koje će izvršilac koristiti za njegovo uspešno izvođenje.</small>
                </div>
            </div>
        </div>

        @if($project->admin_decision !== null or $project->status === 'completed')
            @php
                $review = Auth::user()->reviewForAuthUser(Auth::user()->id, $project->service_id);
            @endphp
            @if($review)
                <div class="col-md-8 mb-2">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-end">
                                <small class="text-secondary">
                                    <i class="fas fa-clock"></i> {{$review->created_at->format('d.m.Y H:i')}}
                                </small>
                            </div>

                            <div class="d-flex">
                                <label for="rating" class="col-md-4">Tvoja ocena: </label>
                                <div class="text-warning ">
                                    @for ($j = 1; $j <= 5; $j++)
                                        @if ($j <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor

                                    <span class="text-secondary">( {{$review->rating}} )</span>
                                </div>
                            </div>

                            <div class="d-flex">
                                <label for="rating" class="col-md-4">Tvoj komentar: </label>
                                <span class="text-secondary">{{$review->comment}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-8 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-bullhorn"></i></i> <span class="card-title mb-4 text-success">Recinzija</span></h5>
                            <form method="POST" action="{{ route('reviews.store', $project) }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="rating" class="col-md-4 col-form-label text-md-right">Označi ocenu</label>
                                    <div class="col-md-6 mt-3">
                                        <div class="rating-input">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} />
                                                <label for="star{{ $i }}" title="{{ $i }} stars">
                                                    <i class="{{ old('rating') >= $i ? 'fas' : 'far' }} fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>

                                        @error('rating')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="comment" class="col-md-4 col-form-label text-md-right">Dodaj komentar</label>

                                    <div class="col-md-6">
                                        <textarea id="comment" class="form-control @error('comment') is-invalid @enderror" name="comment" required rows="4">{{ old('comment') }}</textarea>
                                    </div>
                                 </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn text-white w-100 mb-4" style="background-color: #198754">
                                    <i class="fa fa-floppy-disk me-1"></i> Sačuvaj
                                </button>
                                     </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="col-md-8 mb-2 g-0">
                <div class="card">
                    <div class="card-body">
                        <h5><i class="fas fa-bullhorn"></i></i> <span class="card-title mb-4 text-success">Recinzija</span></h5>
                        <p>Recenziju možeš ostaviti tek nakon što se kompletira posao ili nakon završetka arbitraže</p>
                    </div>
                </div>
            </div>
        @endif
    </div>


</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
     // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('service-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('service-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }

    const stars = document.querySelectorAll('.rating-input input');

        stars.forEach(star => {
            star.addEventListener('change', function() {
                const rating = this.value;
                const labels = document.querySelectorAll('.rating-input label i');

                labels.forEach((label, index) => {
                    if (index < 5 - rating) {
                        label.classList.remove('fas');
                        label.classList.add('far');
                    } else {
                        label.classList.remove('far');
                        label.classList.add('fas');
                    }
                });
            });
        });
});
</script>
@endsection
