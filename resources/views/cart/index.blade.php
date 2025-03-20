@extends('layouts.app')
<title>Poslovi Online | Vaša korpa</title>
@section('content')
<div class="container">
    <!-- Prikaz poruke sa anchor ID -->
    @if(session('success'))
        <div id="cart-message" class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="cart-message-danger" class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <!-- Naslov korpe levo -->
        <h4><i class="fas fa-shopping-cart"></i> Vaša korpa</h4>

        <!-- Balans korisnika desno -->
        <h6 class="text-secondary">
            <i class="fas fa-credit-card"></i> Trenutni depozit: <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} RSD</strong>
        </h6>
         <!-- pretraga omiljenih ponuda desno -->
        <input type="text" id="searchInput" placeholder="Pretraži omiljene ponude..." class="form-control w-25">
    </div>


    @if($cartItems->isEmpty())
        <p>Vaša korpa je prazna.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Usluga</th>
                    <th>Paket</th>
                    <th style="width: 18% !important;">Količina</th>
                    <th>Cena RSD</th>
                    <th>Ukupno</th>
                    <th>Ažuriraj</th>
                    <th class="text-center">Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $key => $cartItem)
                    @php
                        // Izračunavanje cene na osnovu paketa i količine
                        $price = 0;
                        if ($cartItem->package == 'Basic') {
                            $price = $cartItem->service->basic_price;
                        } elseif ($cartItem->package == 'Standard') {
                            $price = $cartItem->service->standard_price;
                        } elseif ($cartItem->package == 'Premium') {
                            $price = $cartItem->service->premium_price;
                        }
                        // Ukupna cena = cena * količina
                        $totalPrice = $price * $cartItem->quantity;

                        // Korisnički balans
                        $userBalance = Auth::user()->deposits;
                    @endphp
                    <tr>
                        <td>{{ $key +1 }}</td>
                        <td>
                            <a class="text-dark" href="{{ route('services.show', $cartItem->service->id) }}">{{ $cartItem->service->title }}</a>
                        </td>

                        <form action="{{ route('cart.update', $cartItem) }}" method="POST">
                            @csrf
                            @method('PUT')
                        <td>
                                <select name="package" class="form-select">
                                    <option value="Basic" {{ $cartItem->package == 'Basic' ? 'selected' : '' }}>Basic</option>
                                    <option value="Standard" {{ $cartItem->package == 'Standard' ? 'selected' : '' }}>Standard</option>
                                    <option value="Premium" {{ $cartItem->package == 'Premium' ? 'selected' : '' }}>Premium</option>
                                </select>
                        </td>
                        <td>
                                    <input type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1" style="width: 40% !important;">
                        </td>
                        <td>
                                @if($cartItem->package == 'Basic')
                                    <span>{{ number_format($cartItem->service->basic_price, 2) }}</span>
                                @elseif($cartItem->package == 'Standard')
                                    <span>{{ number_format($cartItem->service->standard_price, 2) }}</span>
                                @elseif($cartItem->package == 'Premium')
                                    <span>{{ number_format($cartItem->service->premium_price, 2) }}</span>
                                @else
                                    <span>N/A RSD</span> <!-- Ako paket nije definisan ili je neka druga vrednost -->
                                @endif
                        </td>
                        <td>
                            <span>{{ number_format($totalPrice, 2) }}</span>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-sync"></i></button>
                        </td>
                        </form>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                @if($userBalance >= $totalPrice)
                                    <form action="{{ route('projects.store', $cartItem) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button class="btn btn-outline-success ms-auto w-100" data-bs-toggle="tooltip" title="Pokreni projekat"><i class="fas fa-rocket"></i></button>
                                    </form>
                                @else
                                    <!-- Ako korisnik nema dovoljno novca, prikazujemo dugme za deponovanje novca -->
                                    <a href="{{ route('deposit.create') }}" data-bs-toggle="tooltip" title="Deponuj novac"> <button class="btn btn-outline-warning ms-auto w-100" data-bs-toggle="tooltip" title="Deponuj novac"><i class="fas fa-credit-card"></i></button>
                                    </a>
                                @endif
                                <form action="{{ route('cart.destroy', $cartItem) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger ms-auto w-100" data-bs-toggle="tooltip" title="Ukloni iz korpe"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 p-3 border rounded bg-light">
            <h5><i class="fas fa-info-circle"></i> Opis akcija</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-sync text-primary"></i>
                    <strong>Ažuriranje:</strong> Pritiskom na dugme možete promeniti paket ili količinu usluga u korpi.
                </li>
                <li class="mb-2">
                    <i class="fas fa-rocket text-success"></i>
                    <strong>Pokretanje projekta:</strong> Klikom na dugme aktivira se usluga koju ste odabrali.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success"></i>
                    <strong>Projekat je aktivan:</strong> Vaš projekat je već pokrenut i aktivan.
                </li>
                <li class="mb-2">
                    <i class="fas fa-credit-card text-warning"></i>
                    <strong>Deponovanje novca:</strong> Da biste mogli koristiti usluge, potrebno je da imate dovoljno sredstava na računu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-trash text-danger"></i>
                    <strong>Uklanjanje iz korpe:</strong> Klikom na dugme brišete uslugu iz vaše korpe (trajno).
                </li>
            </ul>
        </div>
    @endif
</div>
@endsection

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    // Provera hash-a i skrol
    if (window.location.hash === '#cart-message') {
        const element = document.getElementById('cart-message');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Automatsko sakrivanje poruke
    const messageElement = document.getElementById('cart-message');
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

    const messageElementDanger = document.getElementById('cart-message-danger');
    if (messageElementDanger) {
        // Dodajemo klasu za tranziciju
        messageElementDanger.classList.add('fade-out');

        // Sakrijemo poruku nakon 5 sekundi
        setTimeout(() => {
            messageElementDanger.classList.add('hide');

            // Uklonimo element iz DOM-a nakon što animacija završi
            setTimeout(() => {
                messageElementDanger.remove();
            }, 1000); // Vreme trajanja animacije (1s)
        }, 5000); // Poruka će početi da nestaje nakon 5s
    }


    // Pretraga tabele
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('.table tbody');
    const rows = tableBody.querySelectorAll('tr');

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;

            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchTerm)) {
                    match = true;
                }
            });

            if (match) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

});
</script>
