@extends('layouts.app')
<title>Poslovi Online | Poslovi</title>
@section('content')
@php
    use Illuminate\Support\Facades\Crypt;
@endphp
<div class="container">
    <!-- Prikaz poruka -->
    @if(session('success'))
        <div id="project-message" class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="project-message-danger" class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-handshake"></i> Vaši poslovi</h4>
        <h6 class="text-secondary">
            <i class="fas fa-credit-card"></i> Ukupna mesečna zarada: <strong class="text-success">{{ number_format($reserved_amount, 2) }} RSD</strong>
        </h6>
    </div>

    @if($projects->isEmpty())
        <p>Nemate aktivnih poslova.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usluga</th>
                    <th>ID projekta</th>
                    <th>Količina</th>
                    <th>Paket</th>
                    <th>Početak</th>
                    <th>Završetak</th>
                    <th>Rez. sredstva</th>
                    <th class="text-center">Status/Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $key => $project)
                @php
                    $encryptedUserId = Crypt::encryptString($project->buyer->id);
                @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><a class="text-dark" href="{{ route('services.show', $project->service->id) }}">{{ $project->service->title }}</a></td>
                        <td>
                            {{$project->project_number}}
                            <button type="button" class="btn btn-link openUserModal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#userInfoModal"
                                    data-firstname="{{ $project->buyer->firstname }}"
                                    data-lastname="{{ $project->buyer->lastname }}"
                                    data-image="{{ $project->buyer->avatar ?? 'https://via.placeholder.com/120' }}"
                                    data-userid="{{ $encryptedUserId }}"
                                    data-projectid="{{$project->project_number}}">
                                <i class="fas fa-user-circle"></i>
                            </button>
                        </td>
                        <td class="text-center">{{$project->quantity}}</td>
                        <td class="text-center">{{$project->package}}</td>
                        <td>{{ $project->start_date ? $project->start_date : 'N/A' }}</td>
                        <td>{{ $project->end_date ? $project->end_date : 'N/A' }}</td>
                        <td>{{ number_format($project->reserved_funds, 2) }}</td>
                        <td>
                            @if(Auth::user()->role == 'seller' && $project->status == 'inactive')
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
                            @elseif(Auth::user()->role == 'seller' && $project->status == 'in_progress')
                                <div class="d-flex gap-2 text-center">
                                    <i class="fas fa-tasks text-primary mt-1" style="font-size: 1.5em;"></i>

                                    <form action="{{ route('projects.waitingconfirmation', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Zahtevaj <i class="fas fa-user-check"></i></button>
                                    </form>
                                </div>
                            @elseif(Auth::user()->role == 'seller' && $project->status == 'waiting_confirmation')
                                <div class="d-flex gap-2 text-center justify-content-center align-items-center" style="font-size: 1.5em;">
                                    <i class="fas fa-user-check text-primary mt-2"></i>
                                </div>
                            @elseif(Auth::user()->role == 'seller' && $project->status == 'completed')
                                <div class="d-flex gap-2 text-center justify-content-center align-items-center" style="font-size: 1.5em;">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            @elseif(Auth::user()->role == 'seller' && $project->status == 'uncompleted')
                                <div class="d-flex gap-2 text-center justify-content-center align-items-center">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5em;"></i>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 p-3 border rounded bg-light">
            <h5><i class="fas fa-info-circle"></i> Status projekta</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-handshake text-success"></i>
                    <strong>Čeka se prihvat:</strong> Kupac je poslao zahtev za prihvat projekta.
                </li>
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger"></i>
                    <strong>Odbijeno:</strong> Možete odbiti projekat, rezervisana sredstva se refundiranju kupcu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-tasks text-primary"></i>
                    <strong>U toku:</strong> Prihvatili ste projekat i radite na njemu. <br>
                    <span style="margin-left: 3%;">Nakon što uradite posao možete poslati zahtev za odobrenje završetka posla od strane kupca <button type="submit" class="btn btn-sm btn-success" disabled="">Zahtevaj <i class="fas fa-user-check"></i></button>
                    </span>
                </li>
                <li class="mb-2">
                    <i class="fas fa-user-check text-primary"></i>
                    <strong>Zahtev za odobrenje:</strong> Završili ste projekat, čeka se potvrda kupca.
                </li>
                <li class="mb-2">
                    <i class="fas fa-undo-alt  text-danger"></i>
                    <strong> Potrebne su korekcije:</strong> Projekat je označen kao završen, ali kupac zahteva dodatne izmene ili korekcije pre finalnog kompletiranja.<br>
                    <span style="margin-left: 3%;"><i class="fas fa-file-upload text-primary"></i> Proverite uputstva i uputite izmene prema zahtevima kupca.</span><br>
                    <span style="margin-left: 4.8%;"> Ako su potrebni dodatni dokumenti za korekciju, možete ih postaviti koristeći obrazac.</span>
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success"></i>
                    <strong>Završeno:</strong> Projekat je uspešno završen, sredstva su prebačena vama.
                </li>
                <li class="mb-2">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <strong>Nije završeno:</strong> Projekat nije završen, rezervisana sredstva su zamrznuta.<br>
                    <span style="margin-left: 3%;"><i class="fas fa-exclamation-circle text-warning"></i> Ako smatrate da je status projekta sporan, možete uložiti prigovor. Naša podrška će doneti konačnu odluku.</span><br>
                    <span style="margin-left: 3%;"><i class="fas fa-ban text-danger"></i> Obe strane su saglasne da projekat nije završen prema očekivanjima. </span><br>
                    <span style="margin-left: 5%">Kupac će dobiti povrat sredstava, a projekat će biti zatvoren u statusu nekompletiran.</span>

                </li>
            </ul>
        </div>

        <!-- User Info Modal -->
        <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userInfoModalLabel">
                            <i class="fas fa-user"></i> Informacije o kupcu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <!-- Profilna slika (menja se dinamički) -->
                        <img id="modalUserImage" src="" alt="Profilna slika" class="rounded-circle img-thumbnail mb-3" width="120">

                        <!-- Ime i prezime (menja se dinamički) -->
                        <h4 id="modalUserName" class="mb-3"></h4>

                        <!-- Dugme za kontakt (menja se dinamički) -->
                        <a id="modalContactButton" href="#" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> Kontaktiraj
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#project-message') {
        const element = document.getElementById('project-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Automatsko sakrivanje poruka
    const messageElement = document.getElementById('project-message');
    if (messageElement) {
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }

    const messageElementDanger = document.getElementById('project-message-danger');
    if (messageElementDanger) {
        setTimeout(() => {
            messageElementDanger.remove();
        }, 5000);
    }

    document.querySelectorAll(".openUserModal").forEach(button => {
        button.addEventListener("click", function () {
            let firstname = this.getAttribute("data-firstname");
            let lastname = this.getAttribute("data-lastname");
            let image = this.getAttribute("data-image");
            let encryptedUserId = this.getAttribute("data-userid");
            let projectId = this.getAttribute("data-projectid");

            document.getElementById("modalUserName").textContent = firstname + " " + lastname;
            document.getElementById("modalUserImage").src = '/user/'+image;

            // Postavljamo šifrovani ID u kontakt dugme (umesto pravog ID-a)
            document.getElementById("modalContactButton").setAttribute("href", projectId+'/'+encryptedUserId);
        });
    });
});
</script>
