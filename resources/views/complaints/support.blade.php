@extends('layouts.app')
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
@section('content')
<div class="container py-5">
    <div class="row">
         <!-- Prikaz poruka -->
        @if(session('success'))
            <div id="support-message" class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="support-message-danger" class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

    @if($complaints->isEmpty())
        <p>Nemate aktivnih prigovora.</p>
    @else
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4><i class="fas fa-balance-scale"></i> Lista prigovora</h4>
             <!-- pretraga  desno -->
            <input type="text" id="searchInput" placeholder="Pretraži prigovore..." class="form-control w-25">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usluga</th>
                    <th>Pokrenut</th>
                    <th>Ažuriran</th>
                    <th class="text-center">Podneo</th>
                    <th class="text-center">Akcija</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $key => $complaint)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <a class="text-dark" href="{{ route('services.show', $complaint->service->id) }}">{{ $complaint->service->title }}
                            </a>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($complaint->created_at)->format('d.m.Y H:i:s') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($complaint->updated_at)->format('d.m.Y H:i:s') }}
                        </td>
                        <td>
                            {{ $complaint->seller->firstname .' '.$complaint->seller->lastname}}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('complaints.show', $complaint) }}">
                                <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-circle"></i> Pogledaj arbitražu</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginacija -->
        <div class="d-flex justify-content-center pagination-buttons">
            {{ $complaints->links() }}
        </div>

        <div class="mt-4 p-3 border rounded bg-light">
            <p><i class="fas fa-info-circle"></i> Trenutno su prikazani samo aktivni prigovori koji su u procesu arbitraže. Za pregled arhiviranih ili rešenih prigovora koristite pretragu.</p>

        </div>
    @endif

    </div>
</div>

<script>
 // Pretraga tabele
 function translatePagination() {
    const textElements = document.querySelectorAll("p.text-sm.text-gray-700");

    textElements.forEach(textElement => {
        let text = textElement.textContent.trim();

        // Prevedeni tekstovi za različite scenarije
        const translations = {
            'Showing': 'Prikazuje',
            'to': 'do',
            'of': 'od',
            'results': 'rezultata',
            'result': 'rezultat'
        };

        // Zamena engleskih reči sa srpskim prevodima
        Object.keys(translations).forEach(eng => {
            const regex = new RegExp(eng, 'gi');
            text = text.replace(regex, translations[eng]);
        });

        textElement.textContent = text;
    });
}


document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    let currentSearchTerm = '';
    let isServerSearch = false;

    // Klijentska pretraga
    function clientSideSearch(searchTerm) {
        const tableBody = document.querySelector('.table tbody');
        const rows = tableBody.querySelectorAll('tr');
        let found = false;

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;

            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchTerm)) {
                    match = true;
                    found = true;
                }
            });

            row.style.display = match ? '' : 'none';
        });

        return found;
    }

    // Serverska pretraga
    function serverSideSearch(searchTerm) {
        fetch(`?search=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Kreiraj novi DOM element iz dobijenog HTML-a
            const parser = new DOMParser();
            const doc = parser.parseFromString(data.html, 'text/html');

            // Zameni samo tbody i paginaciju
            const newTableBody = doc.querySelector('.table tbody');
            const newPagination = doc.querySelector('.pagination-buttons');

            if (newTableBody) {
                document.querySelector('.table tbody').innerHTML = newTableBody.innerHTML;
                translatePagination();
            }
            if (newPagination) {
                document.querySelector('.pagination-buttons').innerHTML = newPagination.innerHTML;
                translatePagination();
            }

            isServerSearch = true;
        });
    }

    // Event listener za pretragu
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.toLowerCase().trim();

        if (searchTerm === '') {
            // Reset pretrage
            if (currentSearchTerm !== '') {
                currentSearchTerm = '';
                serverSideSearch('');
            }
            return;
        }

        currentSearchTerm = searchTerm;

        // Resetuj prikaz svih redova pre pretrage
        const rows = document.querySelectorAll('.table tbody tr');
        rows.forEach(row => row.style.display = '');

        // Prvo probaj klijentsku pretragu
        const foundLocally = clientSideSearch(searchTerm);

        // Ako nema rezultata, pokreni serversku pretragu nakon 800ms
        if (!foundLocally) {
            searchTimeout = setTimeout(() => {
                serverSideSearch(searchTerm);
            }, 800);
        } else {
            isServerSearch = false;
        }
    });
});
</script>

<script type="text/javascript">
    const textElement = document.querySelector("p.text-sm.text-gray-700"); // Selektujemo element koji sadrži tekst
    if (textElement) {
        let text = textElement.textContent.trim();

        // Regex za hvatanje brojeva u stringu
        let matches = text.match(/\d+/g);

        if (matches && matches.length === 3) {
            let translatedText = `Prikazuje od ${matches[0]} do ${matches[1]} od ukupno ${matches[2]} rezultata`;
            textElement.textContent = translatedText;
        }
    }

    // Selektuj sve paginacione linkove
    document.querySelectorAll('nav[role="navigation"] a').forEach(function (link) {
        // Proveri da li href već ima hash deo
        if (!link.href.includes("#service")) {
            link.href += "#service";
        }
    });
</script>
@endsection
