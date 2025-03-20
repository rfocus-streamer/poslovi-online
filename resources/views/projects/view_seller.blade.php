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
<!-- <h1>{{$project->status}}</h1> -->
                @auth
                <div class="ms-auto mt-3"> <!-- Ovo gura dugmad na desno -->
                    @if(Auth::user()->role == 'seller')
                        @switch($project->status)
                            @case('inactive')
                                <div class="d-flex gap-2 text-center">
                                    <form action="{{ route('projects.acceptoffer', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Prihvati <i class="fas fa-handshake"></i></button>
                                    </form>


                                    <form action="{{ route('projects.acceptoffer', $project) }}" method="POST">
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
                                    @if($project->seller_uncomplete_decision === 'accepted' && $countReply > 0)
                                         <a href="{{ route('complaints.create', $project) }}">
                                            <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-circle"></i> Pogledaj arbitražu</button>
                                        </a>
                                    @elseif(is_null($project->seller_uncomplete_decision) && $countReply == 0)
                                        <a href="{{ route('complaints.create', $project) }}">
                                            <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-circle"></i> {{ ($countReply > 0) ? 'Pogledaj arbitražu' : 'Podnesi prigovor' }}</button>
                                        </a>
                                    @endif

                                    @if($project->seller_uncomplete_decision !== 'accepted')
                                        <form action="{{ route('projects.confirmationuncompleteseller', $project) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-ban"></i> Slažem se</button>
                                        </form>
                                    @endif
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
                            <strong>Čeka se prihvat projekta</strong><br> Čeka se vaša odluka da li želite da prihvate ili odbijete projekat?
                        </li>
                    @break

                    @case('in_progress')
                        <li class="mb-2">
                            <i class="fas fa-tasks text-primary"></i>
                            <strong>U toku</strong><br> Prihvatili ste projekat i radite na njemu.<br>
                             <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_prodavca">Po završetku posla, dodajte sve relevantne datoteke u svoj odeljak za predaju kako bi kupac mogao da ih pregleda.</a></span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-user-check"></i> Nakon što uradite posao možete poslati zahtev za odobrenje završetka posla od strane kupca
                            </span>
                        </li>
                    @break

                    @case('waiting_confirmation')
                        <li class="mb-2">
                            <i class="fas fa-user-check text-primary"></i>
                            <strong>Čeka se odobrenje kupca</strong><br>
                            <strong>Kupac može:</strong><br>
                            <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Potvrditi završetak projekta, nakon čega se rezervisana sredstva prebacuju na vaš balans.</span><br>
                             <span style="margin-left: 3%;"><i class="fas fa-exclamation-triangle text-warning"></i>
                            Projekat nije završen, rezervisana sredstva biće zamrznuta.</span><br>
                            <span style="margin-left: 6%;"><i class="fas fa-exclamation-circle text-warning"></i> Vi možete uložiti prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-undo-alt  text-danger"></i>
                            Kupac zahteva dodatne korekcije pre finalnog kompletiranja.</span><br>
                        </li>
                    @break

                    @case('rejected')
                        <li class="mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                            <strong>Odbijeno:</strong><br> Odbili ste ovaj projekat, rezervisana sredstva su kupcu refundirana.
                        </li>
                    @break

                    @case('requires_corrections')
                        <li class="mb-2">
                            <i class="fas fa-undo-alt  text-danger"></i>
                            <strong> Potrebne su korekcije</strong><br>
                            <span style="margin-left: 3%;">Kupac zahteva dodatne korekcije pre finalnog kompletiranja</span><br>
                            <span style="margin-left: 3%;"><i class="fas fa-folder-open"></i> <a class="text-dark" href="#datoteke_kupca">Pogledajte u odeljku datoteke kupca</a></span>
                            <br><br>
                            <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Nakon što završite sa korekcijama, možete ponovo poslati projekat na odobrenje</span>
                        </li>
                    @break

                    @case('completed')
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Završeno:</strong><br> Čestitamo projekat je uspešno završen, rezervisana sredstva su vam prebačena.
                        </li>
                    @break

                    @case('uncompleted')
                         <li class="mb-2">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <strong>Nije završeno</strong> <br>
                            <span style="margin-left: 3%;">Projekat nije završen, rezervisana sredstva su zamrznuta.</span>
                            <br>
                            <div style="margin-left: 6%;">
                                <i class="fas fa-exclamation-circle text-warning"></i>
                                Možete uložiti prigovor. Naša podrška će doneti konačnu odluku.<br>
                                <i class="fas fa-ban text-danger"></i> <span>Saglasan sam da projekat nije završen prema očekivanjima.</span><br>
                                <span style="margin-left: 3.2%;">Kupac će dobiti povrat sredstava, a projekat će biti zatvoren u statusu nekompletiran.</span>
                            </div>
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
                            <p class="text-secondary">Ukupno projekata: {{ $userServiceCount }}</p>
                            <p class="text-secondary">Ukupna ocena:
                                @if ($userStars > 0)
                                    @for ($j = 1; $j <= $userStars; $j++)
                                        <i class="fas fa-star text-warning"></i> {{-- Puna zvezdica --}}
                                    @endfor
                                    @for ($k = $userStars + 1; $k <= 5; $k++)
                                        <i class="far fa-star text-warning"></i> {{-- Prazna zvezdica --}}
                                    @endfor
                                @else
                                    <p>No stars available</p>
                                @endif

                                <small class="ms-2">({{ $userStars }}/5)</small>

                            </p>
                    </div>

                    @auth
                        <!-- Dugme za kontakt -->
                        <a href="#" class="btn btn-success w-100">
                            <i class="fas fa-envelope me-2"></i>Kontaktirajte kupca
                        </a>
                    @endauth
                </div>
            </div>
        </div>


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
                <div class="card-body mb-4">
                    @if(auth()->id() === $project->seller_id)
                        <div class="text-center">
                            @if($hasPendingRequest)
                                <button type="button" class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-wallet"></i> &nbsp;Zahtev već poslat
                                </button>
                                <small class="d-block mt-1 text-muted">Sačekajte odobrenje pre slanja novog zahteva</small>
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

        @if($project->additionalCharges->count() > 0)
            <div class="col-md-8 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-wallet text-dark"></i> Lista zahteva za dodatne naplate</h6>
                        <ul class="list-unstyled">
                            @foreach($project->additionalCharges as $charge)
                                <li class="mb-1 card">
                                    <div class="ml-2">
                                        <strong>Iznos:</strong> {{ number_format($charge->amount, 2) }} RSD<br>
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
                                <i class="fas fa-tasks text-dark"></i> Detalji projekta
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
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o projektu</h6>
                        <small>Kupac može da menja detalje projekta sve dok ne prihvatite projekat. Nakon prihvatanja, izmene više neće biti moguće i smatra se da su to finalni detalji</small>
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
                <div class="card-body mb-2">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o datoteci kupca</h6>
                    <small>Kupac može dostaviti datoteke neophodne za rad na projektu, koje će izvršilac koristiti za njegovo uspešno izvođenje.</small>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-2 g-0" id="datoteke_prodavca">
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
                        <p>Trenutno nema vaših datoteka</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2 g-0">
            <div class="card">
                <div class="card-body mb-2">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o vašim datotekama</h6>
                    <small>Možete dodati datoteke vezane za projekat, koje će kupac moći da pregleda u toku rada. Takođe, po završetku projekta, sve relevantne datoteke će biti dostupne kupcu.</small>
                </div>
            </div>
        </div>

        @if($project->status === 'uncompleted')
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

        <div class="col-md-8 mb-2 g-0">
            <div class="card">
                <div class="card-body">
                    <h5><i class="fas fa-bullhorn"></i></i> <span class="card-title mb-4 text-success">Recinzija</span></h5>
                    <p>Recenziju možete ostaviti tek nakon što se kompletira projekat ili nakon arbitraže</p>
                </div>
            </div>
        </div>
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
                            <label for="amount" class="form-label">Iznos u RSD:</label>
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
@endsection
