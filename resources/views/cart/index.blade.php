@extends('layouts.app')
<title>Poslovi Online | Tvoja korpa</title>
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
        <h4><i class="fas fa-shopping-cart"></i> Tvoja korpa</h4>

        <!-- Balans korisnika desno -->
        <h6 class="text-secondary">
            <i class="fas fa-credit-card"></i> Trenutni depozit: <strong class="text-success">{{ number_format(Auth::user()->deposits, 2) }} <i class="fas fa-euro-sign"></i></strong>
        </h6>
         <!-- pretraga omiljenih ponuda desno -->
        <input type="text" id="searchInput" placeholder="Pretraži omiljene ponude..." class="form-control w-25">
    </div>


    @if($cartItems->isEmpty())
        <p>Tvoja korpa je prazna.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th style="width: 15% !important;">Usluga</th>
                    <th style="width: 11% !important;">Paket</th>
                    <th style="width: 14% !important;" class="text-center">Količina</th>
                    <th style="width: 8% !important;">Cena</th>
                    <th style="width: 8% !important;">Ukupno</th>
                    <th style="width: 8% !important;">Provizija</th>
                    <th style="width: 8% !important;">Svega <i class="fas fa-euro-sign"></i></th>
                    <th style="width: 10% !important;">Ažuriraj</th>
                    <th class="text-center" style="width: 18% !important;">Akcije</th>
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

                        // Izračunavanje 3% od ukupnog iznosa ( provizija )
                        $packageAmount = $totalPrice;
                        $commissionAmount = $packageAmount * 0.03; // 3% za komisiju
                        $totalWithCommisionPrice = $totalPrice + $commissionAmount;
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
                        <td class="text-center">
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
                                    <span>N/A <i class="fas fa-euro-sign"></i></span> <!-- Ako paket nije definisan ili je neka druga vrednost -->
                                @endif
                        </td>
                        <td>
                            <span>{{ number_format($totalPrice, 2) }}</span>
                        </td>
                        <td>{{number_format( $commissionAmount, 2)}}</td>
                        <td>{{number_format($totalWithCommisionPrice, 2)}}</td>
                        <td>
                            <button type="submit" class="btn btn-primary btn-sm">Izmeni <i class="fas fa-sync"></i></button>
                        </td>
                        </form>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @if($userBalance >= $totalWithCommisionPrice)
                                    <form action="{{ route('projects.store', $cartItem) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button class="btn btn-success ms-auto w-100 btn-sm" data-bs-toggle="tooltip" title="Kupi i pokreni projekat">Kupi <i class="fas fa-shopping-cart"></i></button>
                                    </form>
                                @else
                                    <!-- Ako korisnik nema dovoljno novca, prikazujemo dugme za deponovanje novca -->
                                    <a href="{{ route('deposit.form') }}" data-bs-toggle="tooltip" title="Deponuj novac"> <button class="btn btn-warning ms-auto w-100 btn-sm" data-bs-toggle="tooltip" title="Deponuj novac">Dopuni <i class="fas fa-credit-card"></i></button>
                                    </a>
                                @endif
                                <form action="{{ route('cart.destroy', $cartItem) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger ms-auto w-100 btn-sm" data-bs-toggle="tooltip" title="Ukloni iz korpe">Obriši <i class="fas fa-trash"></i></button>
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
                    <strong>Ažuriranje:</strong> Pritiskom na dugme možeš promeniti paket ili količinu usluga u korpi.
                </li>
                <li class="mb-2">
                    <i class="fas fas fa-shopping-cart text-success"></i>
                    <strong>Kupovina usluge:</strong> Klikom na dugme aktivira se usluga koju si odabrao i sredstva se rezervišu sa tvog računa.
                </li>
                <li class="mb-2">
                    <i class="fas fa-credit-card text-warning"></i>
                    <strong>Deponovanje novca:</strong> Da bi mogao koristiti usluge, potrebno je da imaš dovoljno sredstava na računu.
                </li>
                <li class="mb-2">
                    <i class="fas fa-trash text-danger"></i>
                    <strong>Uklanjanje iz korpe:</strong> Klikom na dugme brišeš uslugu iz tvoje korpe (trajno).
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
