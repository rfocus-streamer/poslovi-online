@extends('layouts.app')
<title>Poslovi Online | Poslovi</title>
@section('content')
<style type="text/css">
    .btn-service-details {
      background: linear-gradient(45deg, #9c1c2c, #c82333);
        background-color: rgba(0, 0, 0, 0);
      color: white !important;
      border: none;
      padding: 0.5rem 1.5rem;
      border-radius: 25px;
      transition: all 0.3s ease;
    }
</style>

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


        <!-- Desktop naslov + info -->
        <div class="d-none d-md-flex justify-content-between gap-3 mb-2">
            <h4><i class="fas fa-file-signature"></i> Tvoje ponude</h4>

            @php
                $packageExpired = \Carbon\Carbon::parse(Auth::user()->package_expires_at)->isPast();
            @endphp

            @if(Auth::user()->package and !$packageExpired)
                @if($seller['countPublicService'] < Auth::user()->package->quantity)
                    <div class="text-secondary text-center">
                        <span class="blinking-alert"></span> <i class="fa fa-eye text-success" title="Javno vidljivo"></i> Iskoristio si {{$seller['countPublicService']}} od {{Auth::user()->package->quantity}} javnih ponuda u okviru tvog plana !
                    </div>

                    <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm" style="background-color: #198754; height: 33px !important">
                        <i class="fas fa-plus"></i> Dodaj
                    </a>
                @else
                    <div class="alert alert-danger text-center">
                        <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Dostignut je limit za tvoj paket!
                    </div>

                    <div class="text-warning mb-2">
                        <a href="{{ route('packages.index') }}" class="btn btn-outline-success ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> Izmeni paket <i class="fas fa-calendar-alt"></i>
                        </a>
                    </div>
                @endif
            @else
                <div class="alert alert-danger text-center">
                    <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemaš aktivan paket!
                </div>

                <div class="text-warning mb-2">
                    <a href="{{ route('packages.index') }}" class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> Odaberi paket <i class="fas fa-calendar-alt"></i>
                    </a>
                </div>
            @endif
        </div>

        <!-- Mobile naslov + info -->
        <div class="d-flex d-md-none flex-column text-center w-100">
            <h5 class="mb-0"><i class="fas fa-file-signature"></i> Tvoje ponude</h5>
            <h6 class="text-secondary mt-3">
                @if(Auth::user()->package)
                    @if($seller['countPublicService'] < Auth::user()->package->quantity)
                        <div class="text-secondary mb-2" style="font-size: 0.9rem !important">
                            <span class="blinking-alert"></span> <i class="fa fa-eye text-success" title="Javno vidljivo"></i> Iskoristio si {{$seller['countPublicService']}} od {{Auth::user()->package->quantity}} javnih ponuda u okviru tvog plana
                        </div>

                        <a href="{{ route('services.create') }}" class="btn btn-primary w-100" style="background-color: #9c1c2c">
                            <i class="fas fa-plus"></i> Dodaj ponudu
                        </a>
                    @else
                        <div class="alert alert-danger text-center">
                            <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Dostignut je limit za tvoj paket!
                        </div>

                        <div class="text-warning mb-2">
                            <a href="{{ route('packages.index') }}" class="btn btn-outline-success ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> Izmeni paket <i class="fas fa-calendar-alt"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="alert alert-danger text-center">
                        <span class="blinking-alert"><i class="fas fa-exclamation-circle"></i></span> Trenutno nemaš aktivan paket!
                    </div>

                    <div class="text-warning mb-2">
                        <a href="{{ route('packages.index') }}" class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Odaberite paket"> Odaberi paket <i class="fas fa-calendar-alt"></i>
                        </a>
                    </div>
                @endif
            </h6>
        </div>


    @if($services->isEmpty())
        <p>Nemate aktivnih ponuda.</p>
    @else
        <table class="table d-none d-md-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usluga</th>
                    <th>Kreirana</th>
                    <th>Ažurirana</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $key => $service)
                    @php
                        $expired = \Carbon\Carbon::parse($service->visible_expires_at)->isPast();
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><a class="text-dark" href="{{ route('services.show', $service->id) }}">{{ $service->title }}</a></td>
                        <td>
                            {{ \Carbon\Carbon::parse($service->created_at)->format('d.m.Y H:i:s') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($service->updated_at)->format('d.m.Y H:i:s') }}
                        </td>
                        <td class="text-center">
                            @if(is_null($service->visible))
                                <i class="fa fa-question-circle text-secondary" title="Nikada nije javno prikazana"></i>
                            @elseif($service->visible)
                                <i class="fa fa-eye text-success" title="Javno vidljivo do {{\Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y')}}"></i>
                            @else
                                <i class="fa fa-eye-slash text-danger" title="Nije javno vidljivo"></i>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <form action="{{ route('services.view', $service) }}" method="GET">
                                    @csrf
                                    <button type="submit" class="btn btn-sm text-white" style="background-color: #198754">Uredi <i class="fas fa-pen"></i></button>
                                </form>

                                @if($expired)
                                    <form action="{{ route('services.delete', $service) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-service-details">Obriši <i class="fas fa-trash-alt"></i></button>
                                    </form>
                                @else
                                    <div>
                                        <button type="submit" class="btn btn-sm btn-service-details text-secondary">Obriši <i class="fas fa-trash-alt" disabled></i></button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Mobile & Tablet cards -->
        <div class="d-md-none">
            @foreach($services as $key => $service)
            <div class="card mb-3 subscription-card" data-id="{{ $service->id }}">
                <div class="card-header btn-poslovi-green text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('services.show', $service->id) }}" class="text-light"><span>{{ $service->title }}</span></a>
                        <span class="badge bg-light text-dark">
                            @if(is_null($service->visible))
                                <i class="fa fa-question-circle text-secondary" title="Nikada nije javno prikazana"></i>
                            @elseif($service->visible)
                                <i class="fa fa-eye text-success" title="Javno vidljivo do {{\Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y')}}"></i>
                            @else
                                <i class="fa fa-eye-slash text-danger" title="Nije javno vidljivo"></i>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Kreirana</small>
                            <div>{{ \Carbon\Carbon::parse($service->created_at)->format('d.m.Y H:i:s') }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Ažurirana</small>
                            <div>{{ \Carbon\Carbon::parse($service->updated_at)->format('d.m.Y H:i:s') }}</div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <small class="text-muted">Status</small>
                        <div class="text-truncate">
                            @if(is_null($service->visible))
                                Nikada nije javno prikazana
                            @elseif($service->visible)
                                Javno vidljivo do {{\Carbon\Carbon::parse($service->visible_expires_at)->format('d.m.Y')}}
                            @else
                                Nije javno vidljivo
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex gap-2 justify-content-center">
                        <form action="{{ route('services.view', $service) }}" method="GET">
                            @csrf
                            <button type="submit" class="btn btn-sm text-white" style="background-color: #198754">Uredi <i class="fas fa-pen"></i></button>
                        </form>

                        @if($expired)
                            <form action="{{ route('services.delete', $service) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-service-details">Obriši <i class="fas fa-trash-alt"></i></button>
                            </form>
                        @else
                            <div>
                                <button type="submit" class="btn btn-sm btn-service-details text-secondary" disabled>Obriši <i class="fas fa-trash-alt"></i></button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>


        <div class="mt-4 p-3 border rounded bg-light">
            <h5><i class="fas fa-info-circle"></i> Status ponuda</h5>
            <ul class="list-unstyled">
                @if(Auth::user()->package)
                    <li class="mb-2">
                       <i class="fas fa-calendar-alt" title="Tvoj mesečni plan"></i>
                        <strong>{{Auth::user()->package->name}}:</strong>
                        @if(Auth::user()->package->quantity === 1)
                            Sadrži {{Auth::user()->package->quantity}} javno prikazivanje u okviru tvog plana !
                        @else
                            Sadrži {{Auth::user()->package->quantity}} javnih prikazivanja u okviru tvog plana !
                        @endif
                    </li>
                @endif

                <li class="mb-2">
                    <i class="fa fa-question-circle text-secondary" title="Nikada nije javno prikazana"></i>
                    <strong>Nikada nije javno prikazana</strong>
                </li>
                <li class="mb-2">
                    <i class="fa fa-eye text-success" title="Javno vidljivo"></i>
                    <strong>Javno vidljivo</strong>
                </li>
                <li class="mb-2">
                    <i class="fa fa-eye-slash text-danger" title="Nije javno vidljivo"></i>
                    <strong>Nije javno vidljivo</strong>
                </li>
            </ul>
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
