@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">

        <!-- Prikaz poruke sa anchor ID -->
        @if(session('success'))
            <div id="complaint-message" class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="complaint-message" class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        @if($project->complaints->count() > 0 and Auth::user()->role === 'support' and $project->admin_decision === null)

            <div class="col-md-7 d-flex align-items-center gap-2 mb-3 text-end">
                <p class="mb-0"><strong>Odaberite ishod prigovora:</strong></p>

                <!-- Prihvaćen -->
                <form action="{{ route('projects.confirmationcompletesupport', $project) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check-circle"></i> Prihvaćen
                    </button>
                </form>

                <!-- Odbijen -->
                <form action="{{ route('projects.confirmationuncompletesupport', $project) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times-circle"></i> Odbijen
                    </button>
                </form>

                <!-- Fer Pay -->
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#fairPlayModal">
                    <i class="fas fa-balance-scale"></i> Fer Pay
                </button>
            </div>

            <!-- Opis ispod dugmića -->
            <div class="col-md-5 mb-5">
                <span class="text-success">
                    <i class="fas fa-check-circle"></i> <strong>Prihvaćen</strong></span> – Sredstva se prebacuju prodavcu.<br>

                <span class="text-danger">
                    <i class="fas fa-times-circle"></i> <strong>Odbijen</strong></span> – Sredstva se vraćaju kupcu.<br>

                <span class="text-warning">
                    <i class="fas fa-balance-scale"></i> <strong>Fer Pay</strong></span> – Unesite procenjeni iznos za delimičnu isplatu.
            </div>


            <!-- Modal za Fer Pay -->
            <div class="modal fade" id="fairPlayModal" tabindex="-1" aria-labelledby="fairPlayLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('projects.partiallycompletedsupport', $project) }}" method="POST" class="mb-0">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="fairPlayLabel">
                                    <i class="fas fa-balance-scale"></i> Unesite procenjeni iznos
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <span>Rezervisana sredstva za ovaj projekat: {{$project->reserved_funds}} <i class="fas fa-euro-sign"></i></span><br>
                                <label for="fairPlayAmount" class="form-label">Iznos:</label>
                                <input type="number" class="form-control" id="fairPlayAmount" name="fairPlayAmount" placeholder="Unesite iznos za prodavca" step="0.01" required="">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-check"></i> Potvrdi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endif

        @if($project->admin_decision === 'accepted')
            <div class="col-md-8 mb-1 g-0">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                            <span class="ms-2" style="line-height: 1.5;">Podrška je prihvatila prigovor, te će rezervisana sredstva biti prebačena na račun prodavca.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($project->admin_decision === 'rejected')
           <div class="col-md-8 mb-1 g-0">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                            <span class="ms-2" style="line-height: 1.5;">Podrška je odbila prigovor, te će rezervisana sredstva biti vraćena kupcu.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($project->complaints->count() == 0 and Auth::user()->role !== 'support')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><i class="fas fa-exclamation-circle text-warning"></i>
                        <a class="text-dark" href="{{ route('projects.view', $project->id) }}"> Podnesi prigovor za projekat: {{ $project->service->title }}</a>
                        <!-- O kupcu -->
                        <div class="text-end">
                            <div class="d-inline-block text-center">
                                <img src="{{ asset('user/' . $project->buyer->avatar) }}"
                                     class="rounded-circle"
                                     alt="Avatar kupca"
                                     width="50"
                                     height="50">
                                <div class="mt-2">{{ $project->buyer->firstname .' '.$project->buyer->lastname }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('complaints.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="message" class="form-label">Prigovor</label>
                                <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Prilog (opciono)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Pošalji prigovor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- O arbitraži -->
            <div class="col-md-4 mb-1 g-0">
                <div class="card">
                    <div class="card-body">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o arbitraži</h6>
                    <small>Prilikom podnošenja prigovora, molimo da dostaviš što više relevantnih informacija kako bi što bolje objasnio situaciju. Detaljan opis problema i, ukoliko je moguće, priloženi dokumenti ili dokazi mogu pomoći podršci da donese što pravedniju i objektivniju odluku.</small>
                    </div>
                </div>
            </div>
        @elseif($project->complaints->count() == 0 and Auth::user()->role === 'support')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h6><i class="fas fa-exclamation-circle text-danger"></i> Za ovaj projekat nema dodatih prigovora</h6>
                    </div>
                </div>
            </div>
        @endif



        @if($project->complaints->count() > 0)
        <div class="col-md-8 mb-1 g-0">
            <div class="card">
                <div class="card-body">
                        <h5><i class="fas fa-exclamation-circle text-warning"></i> Lista prigovora</h5>
                        @foreach($project->complaints as $complaint)
                           <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="{{ Storage::url('user/' . $complaint->participant->avatar) }}"
                                         class="rounded-circle"
                                         alt="Avatar"
                                         width="50"
                                         height="50">
                                    <h6 class="ms-3 mb-0">{{ $complaint->participant->firstname .' '.$complaint->participant->lastname }}</h6>
                                </div>

                                <p>{{ $complaint->message }}</p>

                                @if($complaint->attachment)
                                    <div class="text-muted small text-end">
                                        <a href="{{ Storage::url($complaint->attachment) }}" target="_blank" class="btn btn-link p-0">
                                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#additionalChargeModal"> Preuzmi prilog <i class="fas fa-download"></i>
                                            </button>
                                         </a>
                                    </div>
                                @endif

                                @if($complaint->admin_decision)
                                    <p><strong>Odluka podrške:</strong> {{ $complaint->admin_decision }}</p>
                                @endif

                                <div class="text-muted small text-end">
                                    {{ $complaint->created_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2 g-0">
                <div class="card">
                    <div class="card-body mb-2">
                        <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Informacije o prigovoru</h6>
                        @if(Auth::user()->role !== 'support')
                            <small>Podrška će pregledati tvoj prigovor i doneti odluku. Može ga prihvatiti i prebaciti rezervisana sredstva na tvoj račun, odbiti i izvršiti povraćaj kupcu, proceniti da je projekat delimično završen i na osnovu toga prebaci procentualni deo rezervisanih sredstava na oba računa, ili zatražiti dodatne informacije pre konačne odluke.</small>
                        @else
                            <small>Pristigli prigovori zahtevaju vašu odluku, možete:<br><br> 1) Prihvatiti prigovor i time prebaciti rezervisana sredstva prodavcu,<br> 2) Odbiti ga i time izvršiti povraćaj kupcu,<br> 3) Zatražiti dodatne informacije od prodavca pre donošenja konačne odluke.</small>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($project->admin_decision_reply === 'enabled' and Auth::user()->role !== 'support' and $project->complaints->count() > 0)
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('complaints.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="message" class="form-label">Prigovor</label>
                                <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Prilog (opciono)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Pošalji prigovor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- O dodatnoj informaciji -->
            <div class="col-md-4 mb-1 g-0">
                <div class="card">
                    <div class="card-body">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Potrebne su dodatne informacije</h6>
                    <small>Podrška je pregledala prigovor i zaključila da su potrebne dodatne informacije pre donošenja konačne odluke. Molimo vas da pažljivo pregledate odgovor podrške i dostavite tražene podatke kako bi se prigovor rešio na najpravičniji način.</small>
                    </div>
                </div>
            </div>
        @elseif($project->complaints->count() > 0 and Auth::user()->role === 'support' and $project->admin_decision === null)
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('complaints.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="message" class="form-label">Pitanje</label>
                                <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Prilog (opciono)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="enablereply" id="enablereply" class="form-check-input">
                                <label for="enablereply" class="form-check-label">Omogući odgovor</label>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Pošalji pitanje</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- O dodatnoj informaciji -->
            <div class="col-md-4 mb-1 g-0">
                <div class="card">
                    <div class="card-body">
                    <h6 class="card-title mb-4 text-success"><i class="fas fa-info-circle text-dark"></i> Potrebne su dodatne informacije</h6>
                    <small>Pregledali ste detalje prigovora i zaključili ste da su potrebne dodatne informacije pre donošenja konačne odluke. Molimo vas da pažljivo i jasno formulišete pitanje kako bi strana koja je podnela prigovor mogla da dostavi tražene podatke kako bi se prigovor rešio na najpravičniji način.</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<script type="text/javascript">
     // Automatsko sakrivanje poruke
    const messageElement = document.getElementById('complaint-message');
    if (messageElement) {
        // Dodajemo klasu za tranziciju
        messageElement.classList.add('fade-out');

        // Sakrijemo poruku nakon 5 sekundi
        setTimeout(() => {
            messageElement.classList.add('hide');

            // Uklonimo element iz DOM-a nakon što animacija završi
            setTimeout(() => {
                messageElement.remove();
            }, 1000); // Vreme trajanja animacije (1s)
        }, 5000); // Poruka će početi da nestaje nakon 5s
    }
</script>
@endsection
