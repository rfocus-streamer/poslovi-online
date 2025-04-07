@extends('layouts.app')
<title>Poslovi Online | Projekti</title>
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
        <h4><i class="fas fa-project-diagram"></i> Tvoji projekti</h4>
        <h6 class="text-secondary">
            <i class="fas fa-credit-card"></i> Ukupna rezervisana sredstva: <strong class="text-success">{{ number_format($reserved_amount, 2) }} <i class="fas fa-euro-sign"></i></strong>
        </h6>
    </div>

    @if($projects->isEmpty())
        <p>Nemaš aktivnih projekata.</p>
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
                    <th>Rezervisano <i class="fas fa-euro-sign"></i></th>
                    <th class="text-center">Status/Akcija</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $key => $project)
                    @php
                        $encryptedUserId = Crypt::encryptString($project->seller->id);
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><a class="text-dark" href="{{ route('services.show', $project->service->id) }}">{{ $project->service->title }}</a></td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                {{$project->project_number}}
                                <button type="button" class="btn btn-link openUserModal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#userInfoModal"
                                        data-firstname="{{ $project->seller->firstname }}"
                                        data-lastname="{{ $project->seller->lastname }}"
                                        data-image="{{ $project->seller->avatar ?? 'https://via.placeholder.com/120' }}"
                                        data-userid="{{ $encryptedUserId }}"
                                        data-projectid="{{$project->project_number}}">
                                    <i class="fas fa-user-circle"></i>
                                </button>
                            </div>
                        </td>
                        <td class="text-center">{{$project->quantity}}</td>
                        <td class="text-center">{{$project->package}}</td>
                        <td>{{ $project->start_date ? $project->start_date : 'N/A' }}</td>
                        <td>{{ $project->end_date ? $project->end_date : 'N/A' }}</td>
                        <td>{{ number_format($project->reserved_funds, 2) }}</td>
                        <td>
                            @if(Auth::user()->role == 'buyer')
                                <div class="d-flex gap-2 justify-content-center">
                                    @switch($project->status)
                                        @case('inactive')
                                            <i class="fas fa-hourglass-start text-secondary mt-2" title="Čeka se odobrenje izvršioca" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('in_progress')
                                            <i class="fas fa-tasks text-primary mt-2" title="Radovi su u toku" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('waiting_confirmation')
                                            <i class="fas fa-user-check text-primary mt-2" title="Čeka se vaše odobrenje" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('rejected')
                                            <i class="fas fa-times-circle text-danger mt-2" title="Izvršilac je odbio projekat" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('completed')
                                            <i class="fas fa-check-circle text-success mt-2" title="Projekat je kompletiran" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('uncompleted')
                                            @if($project->admin_decision === 'rejected')
                                                <i class="fas fa-balance-scale text-danger mt-2"></i>
                                                <i class="fas fa-times-circle text-danger mt-2"></i>
                                            @elseif($project->admin_decision === 'accepted')
                                                <i class="fas fa-balance-scale text-danger mt-2"></i>
                                                <i class="fas fa-check-circle text-success mt-2"></i>
                                            @elseif($project->admin_decision === 'partially')
                                                <i class="fas fa-balance-scale text-danger mt-2"></i>
                                                <i class="fas fa-adjust text-warning mt-2"></i>
                                            @elseif($project->admin_decision === null and $project->seller_uncomplete_decision === 'accepted')
                                                <i class="fas fa-exclamation-triangle text-warning mt-2"></i>
                                                <i class="fas fa-check-circle text-success mt-2"></i>
                                            @elseif($project->admin_decision === null and $project->seller_uncomplete_decision === 'arbitration')
                                                <i class="fas fa-balance-scale text-danger mt-2"></i>
                                            @else
                                                <i class="fas fa-exclamation-triangle text-warning mt-2"></i>
                                            @endif
                                        @break
                                    @endswitch

                                    <form action="{{ route('projects.view', $project) }}" method="GET">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Pogledaj <i class="fas fas fa-eye"></i></button>
                                    </form>
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
                    <i class="fas fa-hourglass-start text-secondary"></i>
                    <strong>Čeka se prihvat:</strong> Čeka se izvršilac da prihvati projekat.
                </li>
                <li class="mb-2">
                    <i class="fas fa-tasks text-primary"></i>
                    <strong>U toku:</strong> Izvršilac je prihvatio projekat i radi na njemu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-user-check text-primary"></i>
                    <strong>Čeka se odobrenje:</strong> Izvršilac završio, čeka se vaša potvrda da je projekat završen prema očekivanjima.
                </li>
                <li class="mb-2">
                    <i class="fas fa-undo-alt  text-danger"></i>
                    <strong> Potrebne su korekcije:</strong> Projekat je označen kao završen, ali vi zahtevate dodatne izmene ili korekcije pre finalnog kompletiranja.
                </li>
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger"></i>
                    <strong>Odbijeno:</strong> Izvršilac je odbio projekat, sredstva su vama refundirana.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success"></i>
                    <strong>Završeno:</strong> Projekat je uspešno završen, sredstva su prebačena izvršiocu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <strong>Nije završeno:</strong> Projekat nije završen, rezervisana sredstva su zamrznuta.<br>
                    <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Izvršilac je saglasan da je projekat "nekompletiran", sredstva su refundirana na tvoj račun</span>
                </li>
                <li class="mb-2">
                    <i class="fas fa-balance-scale text-danger"></i>
                    <strong>Arbitražni postupak:</strong> Podrška odlučuje o pokrenutom sporu<br>
                    <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> <strong>Potpuno završen:</strong> <i>Rezervisana sredstva su prebačena na prodavčev račun<i></span><br>
                    <span style="margin-left: 3%;">
                        <i class="fas fa-adjust text-warning"></i> <strong>Delimično završen:</strong> <i>Rezervisana sredstva su podeljena između tvog i prodavčevog računa a po visini procene podrške</i>
                    </span><br>
                    <span style="margin-left: 3%;"><i class="fas fa-times-circle text-danger"></i> <strong>Nekompletiran projekat:</strong> <i>Rezervisana sredstva su refundirana na tvoj račun</i></span>
                </li>
            </ul>
        </div>

        <!-- User Info Modal -->
        <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userInfoModalLabel">
                            <i class="fas fa-user"></i> Informacije o izvršiocu
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
