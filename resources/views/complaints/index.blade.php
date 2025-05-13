@extends('layouts.app')

@section('content')
<style type="text/css">
/* Poruke sa leve strane */
.conversation-list-left {
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    margin-top: 3px;
    margin-bottom: 3px;
}

/* Poruke sa desne strane */
.conversation-list-right {
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
    margin-top: 3px;
    margin-bottom: 3px;
}

/* Stilizacija za avatar i sadržaj */
.chat-avatar {
    margin-right: 1px;
}

.user-chat-content {
    max-width: 70%; /* Podesi širinu sadržaja poruke */
    background-color: #f1f1f1; /* Boja pozadine za poruke */
    padding: 10px;
    border-radius: 10px;
}

/* Različite boje pozadine za leve i desne poruke */
.conversation-list-left .user-chat-content {
    background-color: #e0e0e0;  /* Siva pozadina za levo */
}

.conversation-list-right .user-chat-content {
    background-color: #ffe5b5;  /* Žuta pozadina za desno */
}

</style>
<div class="container py-5">
    <div class="row">

        <!-- Prikaz poruke sa anchor ID -->
        @if(session('success'))
            <div id="complaint-message" class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="complaint-message" class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        @if($project->complaints->count() > 0 and Auth::user()->role === 'support' and $project->admin_decision === null)

            <div class="col-md-7 d-flex align-items-center gap-2 mb-3 text-end">
                <p class="mb-0"><strong>Odaberite ishod prigovora:</strong></p>

                <!-- Prihvaćen -->
                <form action="{{ route('projects.confirmationcompletesupport', $project) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check-circle"></i> Prihvaćen
                    </button>
                </form>

                <!-- Odbijen -->
                <form action="{{ route('projects.confirmationuncompletesupport', $project) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times-circle"></i> Odbijen
                    </button>
                </form>

                <!-- Fer Pay -->
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#fairPlayModal">
                    <i class="fas fa-balance-scale"></i> Fer Pay
                </button>
            </div>

            <!-- Opis ispod dugmića -->
            <div class="col-md-5 mb-5">
                <span class="text-success">
                    <i class="fas fa-check-circle"></i> <strong>Prihvaćen</strong></span> – Sredstva se prebacuju prodavcu.<br>

                <span class="text-danger">
                    <i class="fas fa-times-circle"></i> <strong>Odbijen</strong></span> – Sredstva se vraćaju kupcu.<br>

                <span class="text-warning">
                    <i class="fas fa-balance-scale"></i> <strong>Fer Pay</strong></span> – Unesite procenjeni iznos za delimičnu isplatu.
            </div>


            <!-- Modal za Fer Pay -->
            <div class="modal fade" id="fairPlayModal" tabindex="-1" aria-labelledby="fairPlayLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('projects.partiallycompletedsupport', $project) }}" method="POST" class="mb-0">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="fairPlayLabel">
                                    <i class="fas fa-balance-scale"></i> Unesite procenjeni iznos
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <span>Rezervisana sredstva za ovaj projekat: {{$project->reserved_funds}} <i class="fas fa-euro-sign"></i></span><br>
                                <label for="fairPlayAmount" class="form-label">Iznos:</label>
                                <input type="number" class="form-control" id="fairPlayAmount" name="fairPlayAmount" placeholder="Unesite iznos za prodavca" step="0.01" required="">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-check"></i> Potvrdi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endif

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

        @if($project->complaints->count() == 0 and Auth::user()->role !== 'support')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><i class="fas fa-exclamation-circle text-warning"></i>
                        <a class="text-dark" href="{{ route('projects.view', $project->id) }}"> Podnesi prigovor za projekat: {{ $project->service->title }}</a>
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
                    <small>Prilikom podnošenja prigovora, molimo da dostaviš što više relevantnih informacija kako bi što bolje objasnio situaciju. Detaljan opis problema i, ukoliko je moguće, priloženi dokumenti ili dokazi mogu pomoći podršci da donese što pravedniju i objektivniju odluku.</small>
                    </div>
                </div>
            </div>
        @elseif($project->complaints->count() == 0 and Auth::user()->role === 'support')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h6><i class="fas fa-exclamation-circle text-danger"></i> Za ovaj projekat nema dodatih prigovora</h6>
                    </div>
                </div>
            </div>
        @endif



        @if($project->complaints->count() > 0)
            @if(Auth::user()->role === 'support')
                <div class="text-end mb-1">
                    <a href="" id="openMessageHistoryModal" data-bs-toggle="tooltip" title="Pogledaj istoriju poruka izmedju strana">
                        <button class="btn btn-warning ms-auto btn-sm">
                            Istorija poruka <i class="fas fa-envelope"></i>
                        </button>
                    </a>
                </div>
            @endif

        <div class="col-md-8 mb-1 g-0">
            <div class="card">
                <div class="card-body">
                        <h5><i class="fas fa-exclamation-circle text-warning"></i> Lista prigovora</h5>
                        @foreach($project->complaints as $complaint)
                           <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="{{ Storage::url('user/' . $complaint->participant->avatar) }}"
                                         class="rounded-circle"
                                         alt="Avatar"
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
                        @if(Auth::user()->role !== 'support')
                            <small>Podrška će pregledati tvoj prigovor i doneti odluku. Može ga prihvatiti i prebaciti rezervisana sredstva na tvoj račun, odbiti i izvršiti povraćaj kupcu, proceniti da je projekat delimično završen i na osnovu toga prebaci procentualni deo rezervisanih sredstava na oba računa, ili zatražiti dodatne informacije pre konačne odluke.</small>
                        @else
                            <small>Pristigli prigovori zahtevaju vašu odluku, možete:<br><br> 1) Prihvatiti prigovor i time prebaciti rezervisana sredstva prodavcu,<br> 2) Odbiti ga i time izvršiti povraćaj kupcu,<br> 3) Zatražiti dodatne informacije od prodavca pre donošenja konačne odluke.</small>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($project->admin_decision_reply === 'enabled' and Auth::user()->role !== 'support' and $project->complaints->count() > 0)
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
        @elseif($project->complaints->count() > 0 and Auth::user()->role === 'support' and $project->admin_decision === null)
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('complaints.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="message" class="form-label">Pitanje</label>
                                <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Prilog (opciono)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="enablereply" id="enablereply" class="form-check-input">
                                <label for="enablereply" class="form-check-label">Omogući odgovor</label>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Pošalji pitanje</button>
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
                    <small>Pregledali ste detalje prigovora i zaključili ste da su potrebne dodatne informacije pre donošenja konačne odluke. Molimo vas da pažljivo i jasno formulišete pitanje kako bi strana koja je podnela prigovor mogla da dostavi tražene podatke kako bi se prigovor rešio na najpravičniji način.</small>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="messageHistoryModal" tabindex="-1" aria-labelledby="messageHistoryModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="messageHistoryModalLabel">Istorija poruka</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">×</span>
                        </button>
                  </div>
                   <div class="modal-body">
                    <div id="chatHistory" class="chat-history" style="max-height: 60vh; overflow-y: auto;">
                      <!-- Poruke će biti dinamčki ubačene ovde -->
                    </div>
                  </div>
                  <div class="modal-footer">
                   <small class="text-secondary">Skroluj nadole da učitaš još poruka</small>
                  </div>
                </div>
              </div>
            </div>

        @endif
    </div>
</div>
<script type="text/javascript">
     // Automatsko sakrivanje poruke
    const messageElement = document.getElementById('complaint-message');
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
</script>

<script type="text/javascript">
let currentPage = 1;  // Početna stranica
const serviceId = "{{$project->service_id}}";  // Primer ID-a usluge, trebaš ga postaviti prema stvarnim podacima
let isLoading = false;  // Flag koji sprečava višestruko učitavanje u isto vreme
// Prikazivanje datuma samo ako se menja u odnosu na poslednji datum
let dateDisplay = '';
let displayedDates = [];  // Lista koja prati prikazane datume

// Povezivanje dugmeta sa modalom putem JS-a
document.getElementById('openMessageHistoryModal').addEventListener('click', async function(event) {
    event.preventDefault(); // Sprečava standardnu akciju linka
    const apiToken = "{{ $token }}";  // Token koji se koristi za autentifikaciju

    try {
        // Resetuj prikazane datume svaki put kada se otvori modal
        displayedDates = [];

        // API poziv za preuzimanje poruka
        const response = await fetch(`/api/get-messages-complaints?service_id=${serviceId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Greška prilikom preuzimanja poruka');
        }

        const data = await response.json();

        // Prvo proveravamo da li postoji "data" unutar "messages"
        if (data.messages && data.messages.data && data.messages.data.length > 0) {
            const chatHistoryContainer = document.getElementById('chatHistory');
            chatHistoryContainer.innerHTML = '';  // Očisti prethodne poruke

            // Iteracija kroz sve poruke u "data" nizu
            data.messages.data.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(message);  // Pretpostavljamo da postoji funkcija koja generiše HTML za poruku
                //chatHistoryContainer.insertBefore(messageDiv, chatHistoryContainer.firstChild);
                chatHistoryContainer.appendChild(messageDiv)
            });
        } else {
            // Ako nema poruka, prikaži odgovarajuću poruku u modalu
            document.getElementById('chatHistory').innerHTML = '<p class="text-center">Nema poruka za ovu uslugu.</p>';
        }

        // Otvori modal
        var messageHistoryModal = new bootstrap.Modal(document.getElementById('messageHistoryModal'));
        messageHistoryModal.show();

    } catch (error) {
        console.error('Greška prilikom preuzimanja poruka:', error);
        //alert('Došlo je do greške prilikom učitavanja poruka.');
    }
});


// Funkcija za generisanje HTML-a poruke
function getMessageHtml(msg) {
    // Formatiraj datum i vreme
    const formattedDate = formatDate(msg.created_at);
    const [date, time] = formattedDate.split(' ');  // Razdvaja datum (YYYY-MM-DD) i vreme (HH:MM)

    // Proveravamo da li je datum već prikazan
    if (!displayedDates.includes(date)) {
        // Ako datum nije prikazan, dodajemo ga u listu prikazanih datuma
        displayedDates.push(date);

        // Ako je datum nov, prikazujemo ga
        dateDisplay = `
            <div class="mb-1 justify-content-center">
                <div class="date-separator w-100 text-center">
                    <span class="date-text text-secondary">${date}</span>
                </div>
            </div>
        `;
    } else {
        // Ako je datum već prikazan, ne prikazujemo ga ponovo
        dateDisplay = '';  // Prazan string znači da datum neće biti prikazan ponovo
    }

    let attach = `${msg.sender.firstname.charAt(0).toLowerCase() + msg.sender.firstname.slice(1)}_${time.replace(/:/g, '')}`;

    // Zavisno o tome da li je poruka poslana ili primljena, odaberi odgovarajući raspored
    if (msg.sender.role === 'seller') {
        // Dodaj HTML za poruku
        return `
            ${dateDisplay} <!-- Prikazivanje datuma samo ako je promenjen -->
            <div class="conversation-list-right">
                <div class="chat-avatar">
                    <img src="{{ asset('storage/user/') }}/${msg.sender.avatar}" alt="You" class="rounded-circle ms-2" style="width: 50px; height: 50px; margin-right:15px;">
                </div>

                <div class="user-chat-content">
                    <div class="conversation-name">
                        <span class="me-1 text-success">
                            <i class="bx bx-check-double bx-check"></i>
                        </span>
                        <strong>${msg.sender.firstname} ${msg.sender.lastname}</strong>
                        <small class="text-muted mb-0 me-2">${time}</small> <small class="read-status"></small><!-- Samo vreme -->
                    </div>
                    <div class="ctext-wrap">
                        <div class="ctext-wrap-content">
                            <p class="mb-0 rightChat">${msg.content}</p>
                        </div>
                        <!-- Prilog (ako postoji) -->
                        ${msg.attachment_path ? `
                            <div class="d-flex justify-content-end mt-1">
                                <small><a href="/${msg.attachment_path}" target="_blank" class="text-decoration-none">
                                            <i class="fa fa-download"></i> ${attach}
                                </a></small>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    } else {
        // Dodaj HTML za poruku
        return `
            ${dateDisplay} <!-- Prikazivanje datuma samo ako je promenjen -->
            <div class="conversation-list-left">
                <div class="chat-avatar">
                    <img src="{{ asset('storage/user/') }}/${msg.sender.avatar}" alt="You" class="rounded-circle ms-2" style="width: 50px; height: 50px; margin-right:15px;">
                </div>

                <div class="user-chat-content">
                    <div class="conversation-name">
                        <span class="me-1 text-success">
                            <i class="bx bx-check-double bx-check"></i>
                        </span>
                        <strong>${msg.sender.firstname} ${msg.sender.lastname}</strong>
                        <small class="text-muted mb-0 me-2">${time}</small> <small class="read-status"></small><!-- Samo vreme -->
                    </div>
                    <div class="ctext-wrap">
                        <div class="ctext-wrap-content">
                            <p class="mb-0 leftChat">${msg.content}</p>
                        </div>
                        <!-- Prilog (ako postoji) -->
                        ${msg.attachment_path ? `
                            <div class="d-flex justify-content-end mt-1">
                                <small><a href="/${msg.attachment_path}" target="_blank" class="text-decoration-none">
                                            <i class="fa fa-download"></i> ${attach}
                                </a></small>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }
}


function formatDate(dateString) {
    if (!dateString) return 'Invalid date';
    const date = new Date(dateString);

    // Opcije za formatiranje datuma i vremena
    const options = {
        timeZone: 'Europe/Belgrade', // Postavljanje vremenske zone na Beograd
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false // Koristi 24-časovni format
    };

    // Formater koji koristi vremensku zonu i daje željeni format
    const formattedDate = new Intl.DateTimeFormat('sr-RS', options).format(date);

    return formattedDate;
}


// Funkcija koja se poziva kada se korisnik skroluje na dno
const chatHistoryElement = document.getElementById('chatHistory');

chatHistoryElement.addEventListener('scroll', async () => {
    // Ako je skrolovanje došlo do dna i još nismo učitali poruke
    const nearBottom = chatHistoryElement.scrollTop + chatHistoryElement.clientHeight >= chatHistoryElement.scrollHeight - 10;

    if (nearBottom && !isLoading) {
        isLoading = true;  // Sprečava višestruko učitavanje

         const apiToken = "{{ $token }}";  // Token za autentifikaciju

        try {
            // API poziv za novije poruke (pretpostavljamo paginaciju)
            const response = await fetch(`/api/get-messages-complaints?service_id=${serviceId}&page=${currentPage}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${apiToken}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            // Sortiramo poruke od starijih ka novijim
            const chatMessages = data.messages.data;

            currentPage += 1;  // Uvećavamo broj stranice za sledeći API poziv

            // Dodajemo novije poruke na kraj
            chatMessages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(msg);  // Pretpostavka: postoji funkcija koja generiše HTML
                chatHistoryElement.appendChild(messageDiv);  // Dodajemo na kraj
            });

            // Pomeramo skrol na dno da ostane pri novim porukama
            //chatHistoryElement.scrollTop = chatHistoryElement.scrollHeight;

        } catch (error) {
            console.error('Error fetching messages:', error);
        }

        isLoading = false;  // Omogućava novo učitavanje
    }
});

</script>
@endsection
