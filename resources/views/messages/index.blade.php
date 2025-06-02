@extends('layouts.app')
<title>Poslovi Online | Poruke</title>
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Ostali meta tagovi i linkovi -->
<!-- Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Toastify JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

@section('content')
  <style>
    .chat-container {
      display: flex;
      height: 100vh;
    }
    .contacts {
      width: 250px;
      background-color: #f1f1f1;
      overflow-y: auto;
      padding: 10px;
    }
    .chat-box {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .chat-history {
      flex: 1;
      background-color: #ffffff;
      padding: 20px;
      overflow-y: auto;
    }
    .chat-input {
      padding: 10px;
      background-color: #f1f1f1;
    }
    .chat-message {
      padding: 10px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .leftChat {
      background-color: #7269ef;/*#e1ffe1;*/
      background-color: #e1ffe1;
      flex-direction: row-reverse;
      /*color: white;*/
      border-radius: 5px;
      padding: 5px;
    }
    .rightChat {
      background-color: #e1e1e1;
      flex-direction: row-reverse;
      border-radius: 5px;
      background-color: #add8e6; /* Svetlo plava (LightBlue) */
      padding: 5px;
    }
    .contact-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      cursor: pointer;
    }
    .contact-avatar {
      width: 40px;
      height: 40px;
    /*  object-fit: cover;*/
      border-radius: 50%;
      flex-shrink: 0;
    }
    .status-online {
      color: green;
      font-size: 14px;
    }
    .status-offline {
      color: gray;
      font-size: 14px;
    }
    .message-time {
      font-size: 12px;
      color: gray;
      text-align: right;
      margin-left: 10px;
    }
    .message-header {
      display: flex;
      align-items: center;
    }
    .message-header img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }
    .message-header .status {
      font-size: 12px;
      color: gray;
    }
    .message-body {
      margin-top: 5px;
    }
    .message-content {
      display: flex;
      justify-content: space-between;
      width: 100%;
    }

    .read-status {
        display: inline-block;
        font-size: 0.8em;
    }

    .read-status i {
        cursor: help;
    }

    .conversation-list {
      align-items: flex-end;
      display: inline-flex;
      margin-bottom: 24px;
      max-width: 80%;
      position: relative;
    }

    .conversation-list-right {
        display: flex; /* umesto inline-flex */
        flex-direction: row-reverse;
        align-items: flex-end;
        margin-bottom: 24px;
        max-width: 80%;
        margin-left: auto; /* gura ceo blok desno */
    }

    .date-separator {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 100%;
    }

    .date-separator .date-text {
        padding: 0 10px;
        background-color: white; /* ili boja modala */
        position: relative;
        z-index: 1;
    }

    .date-separator::before,
    .date-separator::after {
        content: "";
        flex: 1;
        border-bottom: 1px solid #dee2e6; /* standardna Bootstrap linija */
        margin: 0 10px;
    }

    .unread-badge {
        font-size: 0.7rem;
        min-width: 1.25rem;
        height: 1.25rem;
        line-height: 1.25rem;
    }

    .contact-item {
        cursor: pointer;
        padding: 10px;
        border-radius: 5px;
        transition: background-color 0.2s;
    }

    .contact-item:hover {
        background-color: #f8f9fa;
    }

    .contact-item.has-unread {
        font-weight: bold;
    }

    .selected-contact {
        border: 1px solid rgba(255, 0, 0, 0.5) !important;
    }

    .switchBlock {
        position: relative;
        display: inline-block;
        width: 175px;
        height: 20px;
        top: -8px !important;
        cursor: pointer;
    }

    .switchBlock input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .sliderBlock {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /* Osnovna boja pozadine - siva kad je odblokiran */
        transition: 0.4s;
        border-radius: 24px;
        background: linear-gradient(to right, #ccc 50%, #4CAF50 50%); /* Leva siva, desna zelena */
    }

    /* Stil za "lopticu" koja se pomera unutar slider-a */
    .sliderBlock:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        border-radius: 50%;
        left: 2px;
        bottom: 1px;
        background-color: white;
        transition: 0.4s;
    }

    /* Stilizacija za label-text */
    .label-textBlock {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        font-weight: bold;
        color: #fff;
        margin-left: 12px;
    }

    .label-textBlock.leftBlock {
        left: 12px;
    }

    .label-textBlock.rightBlock {
        right: 22px;
    }

    /* Kad je checkbox označen (blokiran) */
    .switchBlock input:checked + .sliderBlock {
        background: linear-gradient(to right, #9c1c2c 50%, #ccc 50%); /* Pozadina crvena (blokiran) */
    }

    /* Kad je checkbox označen, loptica je na levoj strani (blokiran) */
    .switchBlock input:checked + .sliderBlock:before {
        transform: translateX(0); /* Loptica pomerena na levo */
    }

    /* Kad nije označen (odblokiran), pozadina je zelena */
    .switchBlock input:not(:checked) + .sliderBlock {
        background: linear-gradient(to right, #ccc 50%, #4CAF50 50%); /* Pozadina zelena (odblokiran) */
    }

    /* Kad nije označen, loptica pomera desno (odblokiran) */
    .switchBlock input:not(:checked) + .sliderBlock:before {
        transform: translateX(153px); /* Loptica pomerena na desno */
    }

</style>

<div class="container py-5">
    <div class="row">
        <div class="container-fluid">
            <div class="row chat-container">
                <!-- Contacts List -->
                <div class="col-12 col-md-3 contacts text-secondary">
                    <h6><i class="fa fa-address-book"></i> Tvoji kontakti</h6>
                    <ul class="list-group">
                        @foreach ($contacts as $contact)
                            <li class="list-group-item contact-item" data-user-id="{{ $contact->id }}" onclick="showServices('{{ $contact->id }}', {{$contact->service_titles}})">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="{{ $contact->is_online ? 'status-online' : 'status-offline' }}">
                                            {{ $contact->is_online ? 'Online' : 'Offline' }}
                                        </div>
                                        <div class="text-warning">
                                            @for ($j = 1; $j <= 5; $j++)
                                                @if ($j <= $contact->stars)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <img src="{{ Storage::url('user/'.$contact->avatar) }}" alt="{{ $contact->firstname }} {{ $contact->lastname }}" class="contact-avatar me-2">
                                        <div>
                                            <strong>{{ $contact->firstname }} {{ $contact->lastname }}</strong><br>
                                            <small class="text-muted">
                                                @if($contact->last_seen_at)
                                                    Poslednja aktivnost: {{ \Carbon\Carbon::parse($contact->last_seen_at)->format('d.m.Y H:i:s') }}
                                                @endif
                                            </small>
                                        </div>

                                        @php
                                            $unreadCount = $contact->service_titles[0]['unreadCount'] ?? 0;
                                        @endphp

                                        <span data-user-unread-messages-id="{{ $contact->id }}"
                                              class="unread-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                                              style="display: {{ $unreadCount > 0 ? 'inline-block' : 'none' }}">
                                            {{ $unreadCount }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="switchBlock" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="blockSwitch_{{ $contact->id }}"
                                               {{ $contact->blocked ? 'checked' : '' }}
                                               data-contact-id="{{ $contact->id }}"
                                               onchange="toggleBlockStatus(this)">
                                        <span class="sliderBlock"></span>
                                        <span class="label-textBlock leftBlock">Blokiran</span>
                                        <span class="label-textBlock rightBlock">Odblokiran</span>
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Chat Box -->
                <div class="col-12 col-md-9 chat-box chat-input">
                    <div class="d-flex text-secondary ms-auto">
                        <i class="fas fa-comment-dots mt-1 text-info"></i> &nbsp;
                        <h6 id="topic"></h6>
                    </div>
                    <div class="row list-group-item contact-item">
                        <div id="chatHistory" class="chat-history" data-contact-id="0" style="max-height: 75vh;">
                            <h6 class="text-secondary">Izaberi kontakt sa kojim želiš da započneš razgovor.</h6>
                        </div>

                        <form id="messageForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="service_id" id="service_id" value="">
                            <input type="hidden" name="user_id"  id="user_id" value="">
                            <div class="border rounded p-2 bg-light" id="chatArea" style="display: none;">
                                <div class="mb-2">
                                    <textarea name="content" id="content" class="form-control" rows="3" placeholder="Unesi poruku..." required></textarea>
                                </div>

                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <input type="file" name="attachment" class="form-control form-control-sm w-50">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Pošalji
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Modal -->
        <div class="modal fade" id="servicesModal" tabindex="-1" aria-labelledby="servicesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="servicesModalLabel">Izaberi uslugu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="servicesList">
                        <!-- Services will be listed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize modal variable
    let currentPage = 2;  // Početna stranica
    let servicesModal = null;
    const currentUser = @json(auth()->user());
    let isLoading = false;  // Flag koji sprečava višestruko učitavanje u isto vreme
    let directChatService = @json($directChatService);
    const chatHistoryContainer = document.getElementById('chatHistory');

    document.addEventListener('DOMContentLoaded', function () {
        if (directChatService !== null) {
            document.getElementById('service_id').value = directChatService.id;
            document.getElementById('user_id').value = directChatService.user_id;
            openChat(directChatService.user_id, directChatService.id);
            document.getElementById('topic').innerHTML = directChatService.title;
        }
    });
</script>


<script type="text/javascript">
    // Funkcija koja uzima poslednju poruku
    async function getLastMessageContact(contactId, serviceId) {
        const apiToken = "{{ $token }}";
        const response = await fetch(`/api/get-last-message-service?contact_id=${contactId}&service_id=${serviceId}`, {
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
        return data.last_message_time;  // Vraća vreme poslednje poruke
    }

    // Funkcija koja prikazuje usluge za kontakt
    async function showServices(contactId, services) {
        const servicesList = document.getElementById('servicesList');
        servicesList.innerHTML = '';

        if (services.length === 0) {
            servicesList.innerHTML = '<p>Nema dostupnih usluga za ovog korisnika</p>';
        } else {
            // Za svaku uslugu pozivamo asinhronu funkciju da dobijemo poslednju poruku
            for (let service of services) {
                try {
                    // Čekamo da dobijemo poslednju poruku
                    let contactLastMessage = await getLastMessageContact(contactId, service.service_id);

                    const serviceItem = document.createElement('div');
                    serviceItem.className = 'service-item p-3 border-bottom';
                    serviceItem.style.cursor = 'pointer';
                    serviceItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center" data-service-unread-messages-id="${service.service_id}">
                            <strong>${service.service_title}</strong>
                        </div>
                        <small class="text-muted">
                            Poslednja poruka: ${contactLastMessage != null ? formatDate(contactLastMessage) : 'Nema poruka'}
                        </small>
                    `;

                    serviceItem.addEventListener('click', () => {
                        // Prikazivanje indikatora učitavanja
                        let loadingMsg = '<i class="fa fa-spinner fa-spin"></i> Učitavanje poruka...';
                        document.getElementById('service_id').value = service.service_id;
                        document.getElementById('user_id').value = contactId;
                        document.getElementById('chatHistory').innerHTML = '<div class="text-center p-3">'+loadingMsg+'</div>';
                        openChat(contactId, service.service_id);
                        if (servicesModal) {
                            servicesModal.hide();
                            document.getElementById('topic').innerHTML = '<a href="/ponuda/'+service.service_id+'" class="text-decoration-none text-secondary">'+service.service_title+'</a>';
                        }
                    });

                    servicesList.appendChild(serviceItem);
                } catch (error) {
                    console.error('Greška pri dobijanju poslednje poruke za uslugu:', error);
                    // Ako ne uspemo da dobijemo poslednju poruku, možemo prikazati poruku
                    const serviceItem = document.createElement('div');
                    serviceItem.className = 'service-item p-3 border-bottom';
                    serviceItem.style.cursor = 'pointer';
                    serviceItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center" data-service-unread-messages-id="${service.service_id}">
                            <strong>${service.service_title}</strong>
                        </div>
                        <small class="text-muted">
                            Poslednja poruka: Nema poruka
                        </small>
                    `;
                    servicesList.appendChild(serviceItem);
                }
            }
        }

        // Inicijalizacija ili prikazivanje modalnog prozora
        if (!servicesModal) {
            servicesModal = new bootstrap.Modal(document.getElementById('servicesModal'));
        }
        servicesModal.show();
    }
</script>

<script type="text/javascript">
// Function to open the chat and display history for the selected contact
async function openChat(contactId, serviceId) {
         event.preventDefault(); // Sprečava standardnu akciju linka
    const apiToken = "{{ $token }}";  // Token koji se koristi za autentifikaciju

    try {
        // Resetuj prikazane datume svaki put kada se otvori modal
        displayedDates = [];

        // API poziv za preuzimanje poruka
        const response = await fetch(`/api/get-messages?contact_id=${contactId}&service_id=${serviceId}`, {
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
        if (data.messages && data.messages.length > 0) {
            chatHistoryContainer.innerHTML = '';  // Očisti prethodne poruke

            const chatMessages = data.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            // Iteracija kroz sve poruke u "data" nizu
            chatMessages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(message);  // Pretpostavljamo da postoji funkcija koja generiše HTML za poruku
                chatHistoryContainer.appendChild(messageDiv)

                // Kreiraj observer koji će pratiti vidljivost div-ova
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        // Ako je element postao vidljiv i tab je vidljiv
                        if (entry.isIntersecting && document.visibilityState === 'visible') {


                            // Uzimanje childNodes
                            const childNodes = entry.target.childNodes;

                            // Pronađi child element koji sadrži 'data-message-id'
                            childNodes.forEach(node => {
                                if (node.nodeType === Node.ELEMENT_NODE && node.hasAttribute('data-message-id')) {
                                    const messageId = node.getAttribute('data-message-id');
                                    // Onda možeš koristiti messageId za dalju obradu
                                    const message = data.messages.find(msg => msg.id === parseInt(messageId));

                                    if (message) {
                                        if(!message.read_at){
                                            sendWhisper(message);
                                            sendWhisper(messageId);
                                            selectUnreadMessages(currentUser.id);
                                        }
                                    }
                                }
                            });
                        }

                    });
                }, { threshold: 0.5 });  // Element mora biti 50% vidljiv da bi bio aktiviran

                if (messageDiv) {
                    observer.observe(messageDiv);  // Posmatraj div koji odgovara ovoj poruci
                }


            });
            chatHistoryContainer.setAttribute('data-contact-id', contactId);
        } else {
            // Ako nema poruka, prikaži odgovarajuću poruku u modalu
            document.getElementById('chatHistory').innerHTML = '<p class="text-center">Nema poruka za ovu uslugu.</p>';
        }

        // Proveravamo blokadu s obe strane
        if (data.blockedByHim) {
            // Ako je korisnik blokirao vas
            const chatArea = document.getElementById('chatArea');
            const messageForm = document.getElementById('messageForm');

            // Sakrij formular za slanje poruka
            chatArea.style.display = 'none';

            // Prikazivanje obavestenja da vas je korisnik blokirao
            const blockMessage = document.createElement('div');
                  blockMessage.classList.add('alert', 'alert-warning', 'text-center', 'mt-3');
                  blockMessage.textContent = 'Ovaj korisnik te blokirao i ne možeš slati poruke.';

                // Dodajte obaveštenje ispod forme (ako je forma još uvek u DOM-u)
                messageForm.parentNode.insertBefore(blockMessage, messageForm.nextSibling);
        } else if (data.blockedByYou) {
                // Ako ste vi blokirali korisnika
                const chatArea = document.getElementById('chatArea');
                const messageForm = document.getElementById('messageForm');

                // Sakrij formular za slanje poruka
                chatArea.style.display = 'none';

                // Prikazivanje obavestenja da ste vi blokirali korisnika
                const blockMessage = document.createElement('div');
                      blockMessage.classList.add('alert', 'alert-warning', 'text-center', 'mt-3');
                      blockMessage.textContent = 'Ti si blokirao ovog korisnika i ne možeš slati poruke.';

                // Dodajte obaveštenje ispod forme (ako je forma još uvek u DOM-u)
                messageForm.parentNode.insertBefore(blockMessage, messageForm.nextSibling);
        } else {
                // Ako nijedna blokada nije postavljena, prikazujemo formular za slanje poruka
                const chatArea = document.getElementById('chatArea');
                chatArea.style.display = 'block';

                // Uklonite obaveštenje o blokadi (ako postoji)
                const existingBlockMessage = document.querySelector('.alert-warning');
                if (existingBlockMessage) {
                    existingBlockMessage.remove();
                }
        }

        // Selektuj sve poruke koje imaju klasu "unreadMessagesDiv"
        const unreadMessagesDiv = chatHistoryContainer.querySelectorAll('.unreadMessagesDiv');

        // Ako postoje nepročitane poruke, skroluj do poslednje
        if (unreadMessagesDiv.length > 0) {
            const lastUnreadMessage = unreadMessagesDiv[unreadMessagesDiv.length - 1];  // Poslednja nepročitana poruka

            // Skroluj do te poruke
            lastUnreadMessage.scrollIntoView({
                behavior: 'smooth',   // Glatko skrolovanje
                block: 'nearest'      // Podesi poziciju poruke na vidljivo područje (ne mora biti na samom vrhu)
            });
        } else {
            // Ako nema nepročitanih poruka, skroluj na dno chat-a
            chatHistoryContainer.scrollTop = chatHistoryContainer.scrollHeight;
        }
        adjustDateSeparators(); // Dodajte ovaj poziv
    } catch (error) {
        console.error('Greška prilikom preuzimanja poruka:', error);
        //alert('Došlo je do greške prilikom učitavanja poruka.');
    }
}
</script>

<script type="text/javascript">
// Funkcija za generisanje HTML-a poruke
function getMessageHtml(msg) {
    // Formatiraj datum i vreme
    const formattedDate = formatDate(msg.created_at);
    const [date, time] = formattedDate.split(' ');  // Razdvaja datum (YYYY-MM-DD) i vreme (HH:MM)

    let attach = `${msg.attachment_name}`;

    // Zavisno o tome da li je poruka poslana ili primljena, odaberi odgovarajući raspored
    if (msg.sender_id === currentUser.id) {
        // Dodaj HTML za poruku
        let messageHtml = `
                        <div class="conversation-list-right" data-message-id="${msg.id}" data-date="${date}">
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
        // Ako je poruka pročitana, dodajemo status pročitano
        if (msg.read_at) {
            messageHtml = messageHtml.replace('<small class="read-status"></small>', `
                <i class="fas fa-check-double text-primary" title="Pročitano ${formatDate(msg.read_at)}"></i>
            `);
            // Dodajemo klasu "read" za označavanje da je poruka pročitana
            messageHtml = messageHtml.replace('<div class="conversation-list-right"', '<div class="conversation-list-right read"');
        }

        return messageHtml;
    } else {
        // Dodaj HTML za poruku
        let messageHtml = `
                        <div class="conversation-list" data-message-id="${msg.id}" data-date="${date}">
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
        if (!msg.read_at) {
            messageHtml = messageHtml.replace('<small class="read-status"></small>', `<small class="read-status unreadMessagesDiv"></small>`);
            // Dodajemo klasu "read" za označavanje da je poruka pročitana
            messageHtml = messageHtml.replace('<div class="conversation-list-right"', '<div class="conversation-list-left"');
        }

        return messageHtml;
    }
}
</script>

<script type="text/javascript">
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
</script>

<script type="text/javascript">
async function toggleBlockStatus(checkbox) {
    const contactId = checkbox.getAttribute('data-contact-id');
    const isBlocked = checkbox.checked;  // true ako je označen, false ako nije

    // Dobijanje CSRF tokena iz meta taga
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // URL na osnovu da li blokiramo ili odblokiramo korisnika
    const apiUrl = isBlocked
        ? `/api/messages/block/${contactId}`  // Za blokiranje
        : `/api/messages/unblock/${contactId}`;  // Za odblokiranje

     const apiToken = "{{ $token }}";

    // Pozovite backend API da blokirate ili odblokirate korisnika
    const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${apiToken}`,  // Dodajte Bearer token
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken  // Dodajte CSRF token
        },
        body: JSON.stringify({ user_id: contactId }),
    });

    // Provera odgovora
    if (!response.ok) {
        throw new Error('Greška prilikom promene statusa blokiranja');
    }

    const data = await response.json();

    if (data.success) {
        Toastify({
                text: isBlocked ? "Korisnik je blokiran." : "Korisnik je odblokiran.",
                duration: 3000,  // Dužina prikaza toasta u milisekundama
                gravity: "top",  // Prikaz na vrhu stranice
                position: "right",  // Pozicija desno
                backgroundColor: isBlocked ? "linear-gradient(to right, #ff5f6d, #ffc3a0)" : "linear-gradient(to right, #4CAF50, #8BC34A)",
                stopOnFocus: true,  // Pauza na toastu kad korisnik pređe mišem
            }).showToast();

        // Ako je ID kontakta isti kao data-receiver-id, menjaćemo status
        const chatHistoryDiv = document.getElementById('chatHistory');
        const receiverId = chatHistoryDiv.dataset.contactId; // ili .getAttribute('data-contact-id')

        // Ako odgovara, pozivamo funkciju za prikazivanje obaveštenja
        if (receiverId == contactId) {
            if (data.blockedByHim) {
                // Ako vas je korisnik blokirao
                showBlockMessage('Ovaj korisnik te blokirao i ne možeš slati poruke.', 'none');
            } else if (data.blockedByYou) {
                // Ako ste vi blokirali korisnika
                showBlockMessage('Ti si blokirao ovog korisnika i ne možeš slati poruke.', 'none');
            } else {
                // Ako nijedna blokada nije postavljena, omogućavamo slanje poruka
                showBlockMessage('', 'block');
            }
        }
    }
}


// Funkcija koja prikazuje ili sakriva formular i obaveštenje
function showBlockMessage(message, displayType) {
    const chatArea = document.getElementById('chatArea');
    const messageForm = document.getElementById('messageForm');

    // Sakrij formular za slanje poruka
    chatArea.style.display = displayType;

    // Ako postoji obaveštenje, uklonite ga
    const existingBlockMessage = document.querySelector('.alert-warning');
    if (existingBlockMessage) {
        existingBlockMessage.remove();
    }

    if (message) {
        // Kreiramo novo obaveštenje o blokadi
        const blockMessage = document.createElement('div');
        blockMessage.classList.add('alert', 'alert-warning', 'text-center', 'mt-3');
        blockMessage.textContent = message;

        // Dodajemo obaveštenje ispod forme
        messageForm.parentNode.insertBefore(blockMessage, messageForm.nextSibling);
    }
}

</script>

<script type="text/javascript">
// Funkcija koja se poziva kada se korisnik skroluje na dno
chatHistoryContainer.addEventListener('scroll', async () => {
    const contactId = chatHistoryContainer.getAttribute('data-contact-id');
    const serviceId = document.getElementById('service_id').value;
    // Ako je skrolovanje došlo do dna i još nismo učitali poruke
    const nearTop = chatHistoryContainer.scrollTop <= 10;  // Blizu vrha (neka mala tolerancija)

    if (nearTop && !isLoading) {
        isLoading = true;  // Sprečava višestruko učitavanje

         const apiToken = "{{ $token }}";  // Token za autentifikaciju

        try {
            // API poziv za novije poruke (pretpostavljamo paginaciju)
            const response = await fetch(`/api/get-messages?contact_id=${contactId}&service_id=${serviceId}&page=${currentPage}`, {
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
            chatMessages = data.messages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            currentPage += 1;  // Uvećavamo broj stranice za sledeći API poziv

            // Dodajemo novije poruke na kraj
            chatMessages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(msg);  // Pretpostavka: postoji funkcija koja generiše HTML
                chatHistoryContainer.insertBefore(messageDiv, chatHistoryContainer.firstChild);
            });

            adjustDateSeparators(); // Dodajte ovaj poziv

        } catch (error) {
            console.error('Error fetching messages:', error);
        }

        isLoading = false;  // Omogućava novo učitavanje
    }
});


function adjustDateSeparators() {
    // Ukloni postojeće separatore
    const existingSeparators = chatHistoryContainer.querySelectorAll('.date-separator-container');
    existingSeparators.forEach(separator => separator.remove());

    const displayedDates = [];
    const messages = Array.from(chatHistoryContainer.querySelectorAll('[data-date]'));

    messages.forEach(message => {
        const date = message.getAttribute('data-date');
        if (!displayedDates.includes(date)) {
            displayedDates.push(date);
            const separatorHtml = `
                <div class="date-separator-container mb-1 justify-content-center">
                    <div class="date-separator w-100 text-center">
                        <span class="date-text text-secondary">${date}</span>
                    </div>
                </div>
            `;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = separatorHtml.trim();
            const separatorElement = tempDiv.firstChild;
            message.parentNode.insertBefore(separatorElement, message);
        }
    });
}
</script>
@endsection
