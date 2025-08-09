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
        <!-- Desktop naslov + info -->
        <div class="d-none d-md-flex align-items-center gap-3 w-100">
            <h4 class="mb-0"><i class="fas fa-handshake"></i> Tvoji poslovi</h4>
            <div class="ms-auto">
                <h6 class="text-secondary mb-0">
                    <i class="fas fa-credit-card"></i> Ukupna rezervisana sredstva:
                    <strong class="text-success">{{ number_format($reserved_amount, 2) }} €</strong>
                </h6>
            </div>
        </div>

        <!-- Mobile naslov + info -->
        <div class="d-flex d-md-none flex-column text-center w-100">
            <h6 class="mb-0"><i class="fas fa-handshake"></i> Tvoji poslovi</h6>
            <h6 class="text-secondary mt-1">
                <i class="fas fa-credit-card"></i> Ukupna rezervisana sredstva:
                <strong class="text-success">{{ number_format($reserved_amount, 2) }} €</strong>
            </h6>
        </div>
    </div>


    @if($projects->isEmpty())
        <!-- Desktop -->
        <div class="d-none d-md-flex">
            <p>Nemaš aktivnih poslova.</p>
        </div>

        <!-- Mobile  -->
        <div class="d-md-none text-center">
            <p>Nemaš aktivnih poslova.</p>
        </div>
    @else
        <table class="table d-none d-md-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usluga</th>
                    <th>ID posla</th>
                    <th>Količina</th>
                    <th>Početak</th>
                    <th>Završetak</th>
                    <th>Rezervisano €</th>
                    <th>Provizija €</th>
                    <th class="text-center">Status/Akcija</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $key => $project)
                    @php
                        $encryptedServiceId = Crypt::encrypt($project->service->id);
                        $encryptedUserId = Crypt::encryptString($project->seller->id);
                        $encryptedUserId = route('messages.index', ['service_id' => $encryptedServiceId, 'seller_id' => $encryptedUserId]);
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
                        <td>{{ $project->start_date ? $project->start_date : 'N/A' }}</td>
                        <td>{{ $project->end_date ? $project->end_date : 'N/A' }}</td>
                        <td>{{ number_format($project->reserved_funds, 2) }}</td>
                        <td class="text-center">{{ $project->commission->buyer_amount ?? '' }}</td>
                        <td style="float: right;">
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

        <!-- Kartice za mobilne uređaje -->
        <div class="d-md-none">
            @foreach($projects as $key => $project)
                @php
                    $price = $project->reserved_funds;
                    $commission = $project->commission->buyer_amount;
                    $encryptedServiceId = Crypt::encrypt($project->service->id);
                    $encryptedUserId = Crypt::encryptString($project->seller->id);
                    $chatUrl = route('messages.index', ['service_id' => $encryptedServiceId, 'seller_id' => $encryptedUserId]);

                    $statusIcons = [
                        'inactive' => ['icon' => 'hourglass-start', 'color' => 'secondary', 'title' => 'Čeka se odobrenje izvršioca'],
                        'in_progress' => ['icon' => 'tasks', 'color' => 'primary', 'title' => 'Radovi su u toku'],
                        'waiting_confirmation' => ['icon' => 'user-check', 'color' => 'primary', 'title' => 'Čeka se vaše odobrenje'],
                        'rejected' => ['icon' => 'times-circle', 'color' => 'danger', 'title' => 'Izvršilac je odbio posao'],
                        'completed' => ['icon' => 'check-circle', 'color' => 'success', 'title' => 'Posao je kompletiran'],
                        'requires_corrections' => ['icon' => 'undo-alt', 'color' => 'danger', 'title' => 'Zahteva ispravke']
                    ];
                @endphp

                <div class="card mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center" style="background-color: #198754 !important; color: white !important">
                        <span>#{{ $project->project_number }}</span>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-light btn-sm openUserModal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#userInfoModal"
                                    data-firstname="{{ $project->seller->firstname }}"
                                    data-lastname="{{ $project->seller->lastname }}"
                                    data-image="{{ $project->seller->avatar ?? 'https://via.placeholder.com/120' }}"
                                    data-userid="{{ $chatUrl }}"
                                    data-projectid="{{ $project->project_number }}">
                                Kontakt <i class="fas fa-user-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><a href="{{ route('services.show', $project->service->id) }}" class="text-dark text-decoration-none"><h6 class="card-title mb-1"><i class="fas fa-briefcase"></i> {{ $project->service->title }}</h6></a></p>
                        <p><strong>Količina:</strong> {{ $project->quantity }}</p>
                        <p><strong>Početak:</strong> {{ $project->start_date ?? 'N/A' }}</p>
                        <p><strong>Završetak:</strong> {{ $project->end_date ?? 'N/A' }}</p>
                        <p><strong>Rezervisano:</strong> {{ number_format($price, 2) }} €</p>
                        <p><strong>Provizija:</strong> {{ number_format($commission, 2) }} €</p>

                        <div class="d-flex align-items-center gap-2 mb-2">
                            {{-- Prikaz status ikone --}}
                            @if(array_key_exists($project->status, $statusIcons))
                                <i class="fas fa-{{ $statusIcons[$project->status]['icon'] }} text-{{ $statusIcons[$project->status]['color'] }}" title="{{ $statusIcons[$project->status]['title'] }}"></i>
                            @else
                                <i class="fas fa-info-circle text-warning" title="Nepoznat status"></i>
                            @endif

                            <form action="{{ route('projects.view', $project) }}" method="GET" class="ms-auto">
                                @csrf
                                <button class="btn btn-warning btn-sm">Pogledaj <i class="fas fa-eye"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        <div class="mt-4 p-3 border rounded bg-light">
            <h5><i class="fas fa-info-circle"></i> Status posla</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-hourglass-start text-secondary"></i>
                    <strong>Čeka se prihvat:</strong> Čeka se izvršilac da prihvati posao.
                </li>
                <li class="mb-2">
                    <i class="fas fa-tasks text-primary"></i>
                    <strong>U toku:</strong> Izvršilac je prihvatio posao i radi na njemu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-user-check text-primary"></i>
                    <strong>Čeka se odobrenje:</strong> Izvršilac završio, čeka se tvoja potvrda da je posao završen prema očekivanjima.
                </li>
                <li class="mb-2">
                    <i class="fas fa-undo-alt  text-danger"></i>
                    <strong> Potrebne su korekcije:</strong> Posao je označen kao završen, ali ti zahtevaš dodatne izmene ili korekcije pre finalnog kompletiranja.
                </li>
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger"></i>
                    <strong>Odbijeno:</strong> Izvršilac je odbio posao, sredstva su tebi refundirana.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success"></i>
                    <strong>Završeno:</strong> Posao je uspešno završen, sredstva su prebačena izvršiocu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <strong>Nije završeno:</strong> Posao nije završen, rezervisana sredstva su zamrznuta.<br>
                    <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> Izvršilac je saglasan da je posao "nekompletiran", sredstva su refundirana na tvoj račun</span>
                </li>
                <li class="mb-2">
                    <i class="fas fa-balance-scale text-danger"></i>
                    <strong>Arbitražni postupak:</strong> Podrška odlučuje o pokrenutom sporu<br>
                    <span style="margin-left: 3%;"><i class="fas fa-check-circle text-success"></i> <strong>Potpuno završen:</strong> <i>Rezervisana sredstva su prebačena na prodavčev račun<i></span><br>
                    <span style="margin-left: 3%;">
                        <i class="fas fa-adjust text-warning"></i> <strong>Delimično završen:</strong> <i>Rezervisana sredstva su podeljena između tvog i prodavčevog računa a po visini procene podrške</i>
                    </span><br>
                    <span style="margin-left: 3%;"><i class="fas fa-times-circle text-danger"></i> <strong>Nekompletiran posao:</strong> <i>Rezervisana sredstva su refundirana na tvoj račun</i></span>
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
            document.getElementById("modalContactButton").setAttribute("href", encryptedUserId);
        });
    });
});
</script>
