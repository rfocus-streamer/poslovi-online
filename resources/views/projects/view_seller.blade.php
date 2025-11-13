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

        @if(!Auth::user()->package)
            <div class="d-flex col-md-8">
                <div class="alert alert-danger ms-auto">
                    <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Da bi nastavio sa poslom, moraš imati aktivan plan !
                </div>

                <div class="text-warning mb-2 ms-auto">
                    <a href="{{ route('packages.index') }}" class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> <i class="fas fa-calendar-alt"></i>
                    </a>
                    <p class="text-center text-secondary">Odaberi paket</p>
                </div>
            </div>
        @endif

        <!-- Glavni sadržaj -->
        <div class="col-md-8 g-0">
            <!-- Naslov i osnovne informacije -->
             <h4><i class="fas fa-project-diagram"></i> <a class="text-dark" href="{{ route('services.show', ['id' => $project->service->id, 'slug' => Str::slug($project->service->title)]) }}">{{ $project->service->title }}</a></h4>
            <div class="d-flex align-items-center mb-1">

                @auth
                <div class="ms-auto mt-3"> <!-- Ovo gura dugmad na desno -->
                    @if(Auth::user()->role == 'seller')
                        @if(Auth::user()->package)
                            @switch($project->status)
                                @case('inactive')
                                    <div class="d-flex gap-2 text-center">
                                        <form action="{{ route('projects.acceptoffer', $project) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Prihvati <i class="fas fa-handshake"></i></button>
                                        </form>


                                        <form action="{{ route('projects.rejectoffer', $project) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Odbij <i class="fas fa-times-circle"></i></button>
                                        </form>
                                    </div>
                                @break

                                @case('in_progress')
                                    <div class="d-flex gap-2 text-center">
                                        <form action="{{ route('projects.waitingconfirmation', $project) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Zahtevaj <i class="fas fa-user-check"></i></button>
                                        </form>
                                    </div>
                                @break

                                @case('requires_corrections')
                                    <div class="d-flex gap-2 text-center">
                                        <form action="{{ route('projects.waitingconfirmation', $project) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Zahtevaj <i class="fas fa-user-check"></i></button>
                                        </form>
                                    </div>
                                @break

                                @case('uncompleted')
                                    <div class="d-flex gap-2 text-center">
                                        @if($project->seller_uncomplete_decision === 'arbitration' && $countReply > 0)
                                            <a href="{{ route('complaints.show', $project) }}">
                                                <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-circle"></i> Pogledaj arbitražu</button>
                                            </a>
                                        @elseif($project->seller_uncomplete_decision === null and $countReply == 0)
                                            <a href="{{ route('complaints.show', $project) }}">
                                                <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-circle"></i> Podnesi prigovor</button>
                                            </a>
                                        @endif

                                        @if($project->admin_decision === null and $project->seller_uncomplete_decision !== 'accepted')
                                            <form action="{{ route('projects.confirmationuncompleteseller', $project) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-ban"></i> Slažem se</button>
                                            </form>
                                        @endif
                                    </div>
                                @break

                            @endswitch
                        @endif
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
                            <strong>Čeka se prihvat posla</strong><br> Čeka se tvoja odluka da li želiš da prihvatiš ili odbiješ posao?
                        </li>
                    @break

                    @case('in_progress')
                        <li class="mb-2">
                            <i class="fas fa-tasks text-primary"></i>
                            <strong>U toku</strong><br> Prihvatio si posao i radiš na njemu.<br>
                             <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_prodavca">Po završetku posla, dodaj sve relevantne datoteke u svoj odeljak za predaju kako bi kupac mogao da ih pregleda.</a></span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-user-check"></i> Nakon što uradiš posao možeš poslati zahtev za odobrenje završetka posla od strane kupca
                            </span>
                        </li>
                    @break

                    @case('waiting_confirmation')
                        <li class="mb-2">
                            <i class="fas fa-user-check text-primary"></i>
                            <strong>Čeka se odobrenje kupca</strong><br>
                            <strong>Kupac može:</strong><br>
                            <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Potvrditi završetak posla, nakon čega se rezervisana sredstva prebacuju na tvoj račun.</span><br>
                             <span style="margin-left: 3%;"><i class="fas fa-exclamation-triangle text-warning"></i>
                            Posao nije završen, rezervisana sredstva biće zamrznuta.</span><br>
                            <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-warning"></i> Možeš uložiti prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-undo-alt  text-danger"></i>
                            Kupac zahteva dodatne korekcije pre finalnog kompletiranja.</span><br>
                        </li>
                    @break

                    @case('rejected')
                        <li class="mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                            <strong>Odbijeno:</strong><br> Odbio si ovaj posao, rezervisana sredstva su kupcu refundirana.
                        </li>
                    @break

                    @case('requires_corrections')
                        <li class="mb-2">
                            <i class="fas fa-undo-alt  text-danger"></i>
                            <strong> Potrebne su korekcije</strong><br>
                            <span style="margin-left: 3%;">Kupac zahteva dodatne korekcije pre finalnog kompletiranja</span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_kupca">Pogledaj u odeljku datoteke kupca</a></span>
                            <br>
                            <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Nakon što završiš sa korekcijama, možeš ponovo poslati posao na odobrenje</span>
                        </li>
                    @break

                    @case('completed')
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Završeno:</strong><br> Čestitamo posao je uspešno završen, rezervisana sredstva su prebačena na tvoj račun.
                        </li>
                    @break

                    @case('uncompleted')
                         <li class="mb-2">
                           @if($project->admin_decision === 'rejected')
                                    <i class="fas fa-reply text-danger"></i>
                                    <strong>Nekompletiran</strong><br>
                                    @if($countReply > 0)
                                        <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-danger"></i> Uložio si prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                                    @endif
                                    <span style="margin-left: 6%;"><i class="fas fa-times-circle text-danger"></i> Podrška je odbila tvoj prigovor, te rezervisana sredstva su vraćena kupcu.</span>
                            @elseif($project->admin_decision === 'accepted')
                                    <i class="fas fa-reply text-danger"></i>
                                    <strong>Delimično ili potpuno završen</strong><br>
                                    @if($countReply > 0)
                                        <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-danger"></i> Uložio si prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                                    @endif
                                    <span style="margin-left: 6%;"><i class="fas fa-check-circle text-success"></i> Podrška je prihvatila tvoj prigovor</span><br>
                                    <span style="margin-left: 6%;"><i class="fas fa-check-circle text-success"></i> Rezervisana sredstva su prebačena na tvoj račun.</span>
                            @elseif($project->admin_decision === null and $project->seller_uncomplete_decision === 'accepted')
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                    <strong>Nije završeno</strong> <br>
                                    <span style="margin-left: 6%;"><i class="fas fa-check-circle text-success"></i> Prihvatio si da je posao "nekompletiran", sredstva su refundirana kupcu.</span>
                                </li>
                            @elseif($project->admin_decision === 'partially')
                                <li class="mb-2">
                                    <i class="fas fa-adjust text-warning"></i>
                                    <strong>Delimično završen</strong> <br>
                                    <span style="margin-left: 3%;"> Rezervisana sredstva su podeljena između tvog i kupčevog računa a po visini procene podrške.</span>
                                </li>
                            @else
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <strong>Nije završeno</strong> <br>
                                <span style="margin-left: 3%;">Posao nije završen, rezervisana sredstva su zamrznuta.</span>
                                <br>
                                <div style="margin-left: 6%;">
                                    <i class="fas fa-exclamation-circle text-warning"></i>
                                    Možeš uložiti prigovor. Naša podrška će doneti konačnu odluku.<br>
                                    <i class="fas fa-ban text-danger"></i> <span>Saglasan sam da posao nije završen prema očekivanjima.</span><br>
                                    <span style="margin-left: 3.2%;">Kupac će dobiti povrat sredstava, a posao će biti zatvoren u statusu nekompletiran.</span>
                                    @if($countReply > 0)
                                        <br>
                                        <span><i class="fas fa-exclamation-circle text-danger"></i> Uložio si prigovor. Naša podrška će doneti konačnu odluku.</span>
                                    @endif
                                </div>
                            @endif
                        </li>
                    @break
                @endswitch
                </ul>
            </div>
        </div>

        <!-- O kupcu -->
        <div class="col-md-4 mb-1 g-0">
            <div class="card">
                <div class="card-body">
                <h4 class="card-title text-center text-success">O kupcu</h4>

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
                            <h6>{{ $project->buyer->firstname .' '.$project->buyer->lastname }}</h6>
                        </div>
                    </div>

                     <div class="text-warning ms-auto mb-4">
                            <p class="text-secondary">Ukupno posla: {{ $userServiceCount }}</p>
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
                                $encryptedUserId = Crypt::encryptString($project->buyer->id);
                                $encryptedUserId = route('messages.index', ['service_id' => $encryptedServiceId, 'buyer_id' => $encryptedUserId]);
                            @endphp

                            <!-- Dugme za kontakt -->
                            <a href="{{$encryptedUserId}}" class="btn btn-success w-100">
                                <i class="fas fa-envelope me-2"></i>Kontaktiraj kupca
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>


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
                        <li><i class="fas fa-credit-card text-danger"></i> <strong>Rezervisana sredstva:</strong> {{ number_format(($project->reserved_funds * (1 - ($project->commission->seller_percentage / 100))), 2) }} €</li>
                    </ul>
                </div>
            </div>
        </div>

        @if($project->admin_decision === null and $project->status !== 'inactive' and $project->seller_uncomplete_decision !== 'accepted')
            @if($project->status !== 'completed' and $project->status !== 'uncompleted')
                <div class="col-md-4 mb-2 g-0">
                    <div class="card">
                        <div class="card-body mb-4">
                            @if(auth()->id() === $project->seller_id)
                                <div class="text-center">
                                    @if($hasPendingRequest)
                                        <button type="button" class="btn btn-secondary w-100" disabled>
                                            <i class="fas fa-wallet"></i> &nbsp;Zahtev već poslat
                                        </button>
                                        <small class="d-block mt-1 text-muted">Sačekaj odobrenje pre slanja novog zahteva</small>
                                    @else
                                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#additionalChargeModal">
                                                <i class="fas fa-wallet"></i> &nbsp;Zatraži dodatnu naplatu
                                        </button>
                                        <small class="d-block mt-1 text-muted">Dodatna naplata zavisi od odobrenja kupca</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if($project->additionalCharges->count() > 0)
            <div class="col-md-8 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Lista zahteva za dodatne naplate</h6>
                        <ul class="list-unstyled">
                            @foreach($project->additionalCharges as $charge)
                                <li class="mb-1 card">
                                    <div class="ml-2">
                                        <strong>Iznos:</strong> {{ number_format($charge->amount, 2) }} €<br>
                                        @if($charge->status == 'waiting_confirmation')
                                            <strong>Status: </strong> <i class="fas fa-hourglass-half text-warning"></i> Čeka se odobrenje kupca za naplatu<br>
                                        @elseif($charge->status == 'rejected')
                                            <strong>Status: </strong> <i class="fas fa-times-circle text-danger"></i> Kupac je odbio zahtev<br>
                                        @elseif($charge->status == 'completed')
                                            <strong>Status: </strong> <i class="fas fas fa-check-circle text-success"></i> Kupac je odobrio zahtev, zahtevan iznos je dodat u rezervisana sredstva <br>
                                        @endif
                                        <strong>Razlog:</strong> {{ $charge->reason }}
                                            <div class="text-muted small text-end">
                                                {{ $charge->created_at->format('d.m.Y H:i') }}
                                            </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2 g-0">
                <div class="card">
                    <div class="card-body mb-2">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o dodatnoj naplati</h6>
                        <small>Kupac može da odobri ili odbije zahtev za dodatnu naplatu. Ukoliko dobri zahtev, zahtevan iznos biće dodat u rezervisana sredstva </small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Detalji projekta -->
        @if(strlen($project->description) > 0)
            <div class="col-md-8 mb-2 g-0">
                <div class="card">
                    <div class="card-body mb-5">
                        <div class="row">
                            <h5 class="card-title mb-4 text-success">
                                <i class="fas fa-tasks text-dark"></i> Detalji posla
                            </h5>

                            <div class="mb-3">
                                {{ $project->description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-4 mb-2 g-0">
                <div class="card">
                    <div class="card-body mb-2">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o poslu</h6>
                        <small>Kupac može da menja detalje posla sve dok ne prihvatiš posao. Nakon prihvatanja, izmene više neće biti moguće i smatra se da su to finalni detalji</small>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-8 mb-2 g-0" id="datoteke_kupca">
            <div class="card">
                <div class="card-body mb-5">
                    <h5><i class="fas fa-folder-open"></i> <span class="card-title mb-4 text-success">Datoteke kupca</span></h5>
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
                        <p>Trenutno nema datoteka od kupca</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body mb-3">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o datoteci kupca</h6>
                    <small>Kupac može dostaviti datoteke neophodne za rad na poslu, koje će izvršilac koristiti za njegovo uspešno izvođenje.</small>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0" id="datoteke_prodavca">
            <div class="card">
                <div class="card-body mb-5">
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
                        // Filtriranje fajlova koje ste upload-ovali
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
                        <p>Nisi dodao ništa od datoteka</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body mb-2">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o tvojim datotekama</h6>
                    <small>Možeš dodati datoteke vezane za posao, koje će kupac moći da pregleda u toku rada. Takođe, po završetku posla, sve relevantne datoteke će biti dostupne kupcu.</small>
                </div>
            </div>
        </div>

        @if($project->status === 'uncompleted' and $project->admin_decision === null)
            <div class="col-md-8 mb-2 g-0" id="prigovor">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <h5 class="card-title mb-4 text-success">
                                <i class="fas fa-exclamation-circle"></i> Prigovor o kupcu
                            </h5>
                            <!-- Forma za dodavanja prigovora -->
                            <form action="{{ route('projects.updateDescription', $project) }}" method="POST">
                                    @csrf
                                    @method('POST')

                                    <div class="mb-3">
                                        <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Pošalji
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
         @endif

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

    <!-- Modal -->
    <div class="modal fade" id="additionalChargeModal" tabindex="-1" aria-labelledby="additionalChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="additionalChargeModalLabel"><i class="fas fa-credit-card text-dark"></i> Dodatna naplata</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('additional_charges.store', $project) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Iznos u <i class="fas fa-euro-sign"></i>:</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Razlog</label>
                            <textarea name="reason" id="reason" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Pošalji</button> <!-- Promenjeno u type="submit" -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
