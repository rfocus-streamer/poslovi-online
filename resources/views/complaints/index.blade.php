@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">

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

        @if($project->complaints->count() == 0)
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><i class="fas fa-exclamation-circle text-warning"></i>
                        <a class="text-dark" href="{{ route('projects.view', $project->id) }}"> Podnesite prigovor za projekat: {{ $project->service->title }}</a>
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
                    <small>Prilikom podnošenja prigovora, molimo vas da dostavite što više relevantnih informacija kako biste što bolje objasnili situaciju. Detaljan opis problema i, ukoliko je moguće, priloženi dokumenti ili dokazi mogu pomoći podršci da donese što pravedniju i objektivniju odluku.</small>
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
                                    <img src="{{ asset('user/' . $project->seller->avatar) }}"
                                         class="rounded-circle"
                                         alt="Avatar prodavca"
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
                        <small>Podrška će pregledati vaš prigovor i doneti odluku. Može ga prihvatiti i prebaciti rezervisna sredstva vama, odbiti i izvršiti povraćaj kupcu, ili zatražiti dodatne informacije pre konačne odluke.</small>
                    </div>
                </div>
            </div>
        @endif

        @if($project->admin_decision_reply === 'enabled')
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
        @endif
    </div>
</div>
@endsection
