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
        <h4><i class="fas fa-handshake"></i> Tvoji poslovi</h4>
        <h6 class="text-secondary">
            <i class="fas fa-credit-card"></i> Ukupna mesečna zarada: <strong class="text-success">{{ number_format($totalEarnings, 2) }} <i class="fas fa-euro-sign"></i></strong>
        </h6>
    </div>

    @if(empty($projects))
        <p>Nemaš aktivnih poslova.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usluga</th>
                    <th>ID posla</th>
                    <th>Količina</th>
                    <th>Paket</th>
                    <th>Početak</th>
                    <th>Završetak</th>
                    <th>Rezervisano €</i></th>
                    <th class="text-center">Status/Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $key => $project)
                    @php
                        $encryptedServiceId = Crypt::encrypt($project->service->id);
                        $encryptedUserId = Crypt::encryptString($project->buyer->id);
                        $encryptedUserId = route('messages.index', ['service_id' => $encryptedServiceId, 'buyer_id' => $encryptedUserId]);
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
                        <td class="text-center">{{ number_format($project->reserved_funds, 2) }}</td>
                        <td>
                            @if(Auth::user()->role == 'seller')
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
                                            <i class="fas fa-times-circle text-danger mt-2" title="Izvršilac je odbio posao" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('completed')
                                            <i class="fas fa-check-circle text-success mt-2" title="Posao je kompletiran" style="font-size: 1.1em;"></i>
                                        @break

                                        @case('requires_corrections')
                                            <i class="fas fa-undo-alt text-danger mt-2" title="Posao je kompletiran" style="font-size: 1.1em;"></i>
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
            <h5><i class="fas fa-info-circle"></i> Status posla</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-hourglass-start text-secondary"></i>
                    <strong>Čeka se prihvat:</strong> Kupac je poslao zahtev za prihvat posla.
                </li>
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger"></i>
                    <strong>Odbijeno:</strong> Odbio si posao, rezervisana sredstva se refundiranju kupcu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-tasks text-primary"></i>
                    <strong>U toku:</strong> Prihvatio si posao i radiš na njemu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-user-check text-primary"></i>
                    <strong>Zahtev za odobrenje:</strong> Završio si posao, čeka se potvrda kupca.
                </li>
                <li class="mb-2">
                    <i class="fas fa-undo-alt  text-danger"></i>
                    <strong> Potrebne su korekcije:</strong> Posao je označen kao završen, ali kupac zahteva dodatne izmene ili korekcije pre finalnog kompletiranja.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success"></i>
                    <strong>Završeno:</strong> Posao je uspešno završen, sredstva su prebačena na tvoj račun.
                </li>
                <li class="mb-2">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <strong>Nije završeno:</strong> Posao nije završen, rezervisana sredstva su zamrznuta.<br>
                    <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Saglasan si da je posao "nekompletiran", sredstva su refundirana kupcu</span>
                </li>
                <li class="mb-2">
                    <i class="fas fa-balance-scale text-danger"></i>
                    <strong>Arbitražni postupak:</strong> Podrška odlučuje o pokrenutom sporu<br>
                    <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> <strong>Potpuno završen:</strong> <i>Rezervisana sredstva su prebačena na tvoj račun<i></span><br>
                    <span style="margin-left: 3%;">
                        <i class="fas fa-adjust text-warning"></i> <strong>Delimično završen:</strong> <i>Rezervisana sredstva su podeljena između tvog i kupčevog računa a po visini procene podrške</i>
                    </span><br>
                    <span style="margin-left: 3%;"><i class="fas fa-times-circle text-danger"></i> <strong>Nekompletiran posao:</strong> <i>Rezervisana sredstva su refundirana na račun kupca</i></span>
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
            document.getElementById("modalContactButton").setAttribute("href", encryptedUserId);
        });
    });
});
</script>
