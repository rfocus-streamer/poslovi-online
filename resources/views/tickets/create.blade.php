@extends('layouts.app')

@section('content')
<div class="container">
    <h4><i class="fas fa-ticket"></i> Kreiraj novi tiket</h4>
    <div class="row d-flex">
        <div class="col-8">
            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Naslov</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Opis</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="attachment" class="form-label">Prilog</label>
                    <input type="file" class="form-control" id="attachment" name="attachment">
                </div>
                <button type="submit" class="btn btn-primary w-100" style="background-color: #198754">Pošalji</button>
            </form>
        </div>

        <div class="col-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h5 class="text-center">Otvori Ticket za Podršku</h5>
                        <span>Ako imaš bilo kakvih problema ili pitanja u vezi s korišćenjem platforme Poslovi Online, slobodno možeš otvoriti tiket za podršku kako bi dobio pomoć. <br>Naša korisnička podrška je tu da pomogne u najkraćem mogućem roku i da reši sve nedoumice ili tehničke izazove s kojima se možeš susresti.</span>

                        <span class="mt-3">Status tvoj tiketa možeš pratiti u sekciji "Tiketi". Na tom mestu možeš videti da li je tiket otvoren, u procesu rešavanja ili zatvoren, kao i sve odgovore od našeg tima.</span>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
