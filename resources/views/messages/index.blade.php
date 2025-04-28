@extends('layouts.app')
<title>Poslovi Online | Poruke</title>
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<!-- Ostali meta tagovi i linkovi -->

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
    // Prvo, prenesi podatke o trenutno prijavljenom korisniku
    const currentUser = @json(auth()->user());

    // Chat messages array
    const messages = @json($messages); // Laravel Blade to pass messages data to JavaScript
    const contacts = @json($contacts); // Laravel Blade to pass contacts data to JavaScript
    let currentChat = null;
    let contactIdUser = null;
    let serviceId = null;

    let currentDate = ''; // Čuvanje trenutnog datuma
    let lastDate = null; // Varijabla koja čuva poslednji prikazani datum

    // Initialize modal variable
    let servicesModal = null;
    let directChatService = @json($directChatService);
    if(directChatService !== null){
        openChat(directChatService.user_id, directChatService.id);
        document.getElementById('topic').innerHTML = directChatService.title;
    }

    // Function to show services for a contact
    function showServices(contactId, services) {
        contactIdUser = contactId;
        const servicesList = document.getElementById('servicesList');
        servicesList.innerHTML = '';

        if (services.length === 0) {
            servicesList.innerHTML = '<p>Nema dostupnih usluga za ovog korisnika</p>';
        } else {
            services.forEach(service => {
                const serviceItem = document.createElement('div');
                serviceItem.className = 'service-item p-3 border-bottom';
                serviceItem.style.cursor = 'pointer';
                serviceItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center" data-service-unread-messages-id="${service.service_id}">
                        <strong>${service.service_title}</strong>
                    </div>
                    <small class="text-muted">
                        Poslednja poruka: ${service.last_message_date != null ? formatDate(service.last_message_date) : 'Nema poruka'}
                    </small>
                `;

                serviceItem.addEventListener('click', () => {
                    // Show loading indicator
                    let loadingMsg = '<i class="fa fa-spinner fa-spin"></i> Učitavanje poruka...';
                    document.getElementById('chatHistory').innerHTML = '<div class="text-center p-3">'+loadingMsg+'</div>';
                    openChat(contactId, service.service_id);
                    if (servicesModal) {
                        servicesModal.hide();
                        document.getElementById('topic').innerHTML = '<a href="/ponuda/'+service.service_id+'" class="text-decoration-none text-secondary">'+service.service_title+'</a>';
                    }
                });

                servicesList.appendChild(serviceItem);
            });
        }

        // Initialize or show the modal
        if (!servicesModal) {
            servicesModal = new bootstrap.Modal(document.getElementById('servicesModal'));
        }
        servicesModal.show();
    }

    let currentPage = 1;  // Početna stranica
    let isLoading = false;  // Flag koji sprečava višestruko učitavanje u isto vreme
    // Prikazivanje datuma samo ako se menja u odnosu na poslednji datum
    let dateDisplay = '';
    let displayedDates = [];  // Lista koja prati prikazane datume

    // Function to open the chat and display history for the selected contact
    async function openChat(contactId, serviceId) {
        contactIdUser = contactId;
        currentChat = contactId;

        // Ukloni selektovanu klasu sa svih kontakata
        let allContacts = document.querySelectorAll('.contact-item');
        allContacts.forEach(contact => {
            contact.classList.remove('selected-contact');
        });

        let selectedContact = document.querySelector(`[data-user-id="${contactId}"]`);
        if (selectedContact) {
            selectedContact.classList.add('selected-contact');
        }

        const apiToken = "{{ $token }}";
        const response = await fetch(`/api/get-messages?contact_id=${contactId}&service_id=${serviceId}`, {
            method: 'GET',  // Ako je GET zahtev
            headers: {
                'Authorization': `Bearer ${apiToken}`,  // Dodajte token ovde
                'Content-Type': 'application/json'  // Dodajte Content-Type, ako je potrebno
            }
        });


        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        // Sortiranje poruka po 'created_at' u opadajućem redosledu
        data.messages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        const chatHistory = data.messages.filter(msg =>
            (msg.sender_id == contactId || msg.receiver_id == contactId)
        );

        document.getElementById('service_id').value = serviceId;
        document.getElementById('user_id').value = contactId;
        document.getElementById('chatArea').style.display = 'block';

        document.getElementById('chatHistory').innerHTML = ''; // Čisti prethodne poruke
        window.hasSelectedUnreadMessages = false; // Flag koji prati da li je funkcija već pozvana

        chatHistory.forEach(msg => {
            if(serviceId === msg.service_id){
            // Formatiraj datum i vreme
                const formattedDate = formatDate(msg.created_at);
                const [date, time] = formattedDate.split(' ');  // Razdvaja datum (YYYY-MM-DD) i vreme (HH:MM)

                // Proveravamo da li je datum već prikazan
                // if (!displayedDates.includes(date)) {
                //     // Ako datum nije prikazan, dodajemo ga u listu prikazanih datuma
                //     displayedDates.push(date);

                //     // Ako je datum nov, prikazujemo ga
                //     dateDisplay = `<div class="mb-1 justify-content-center">
                //                     <div class="date-separator w-100 text-center">
                //                             <span class="date-text text-secondary">${formatDate(date)}</span>
                //                         </div>
                //                     </div>
                //                 `;
                // } else {
                //     // Ako je datum već prikazan, ne prikazujemo ga ponovo
                //     dateDisplay = '';  // Prazan string znači da datum neće biti prikazan ponovo
                // }

                //if (date !== lastDate) {
                if (!displayedDates.includes(date)) {
                    displayedDates.push(date);
                    // Ako je datum nov, prikazujemo ga i ažuriramo poslednji prikazani datum
                    dateDisplay = `<div class="mb-1 justify-content-center">
                        <div class="date-separator w-100 text-center">
                                <span class="date-text text-secondary">${formatDate(msg.created_at).split(' ')[0]}</span>
                            </div>
                        </div>
                        `;
                   // lastDate = date;  // Ažuriraj poslednji prikazani datum
                }else{
                    dateDisplay = '';  // Prazan string znači da datum neće biti prikazan ponovo
                }

                const isSentByCurrentUser = msg.sender_id === currentUser.id;
                const sender = msg.sender_id === contactId ? msg.sender : msg.receiver;
                const msgBackground = msg.role === 'buyer' ? 'leftChat' : 'rightChat';
                const msgBackgroundSender = msg.role === 'buyer' ? 'rightChat' : 'leftChat';
                let attach = `${msg.sender.firstname.charAt(0).toLowerCase() + msg.sender.firstname.slice(1)}_${time.replace(/:/g, '')}`;


                const messageDiv = document.createElement('div');

                // Ovisno o tome da li je poruka poslana ili primljena, odaberi odgovarajući raspored
                if (isSentByCurrentUser) {
                    // Dodaj HTML za poruku
                    messageDiv.innerHTML += `
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
                    messageDiv.innerHTML += `
                        ${dateDisplay} <!-- Prikazivanje datuma samo ako je promenjen -->
                        <div class="conversation-list">
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

                // Dodaj poruku u chat history
                const chatHistoryElement = document.getElementById('chatHistory');
                      chatHistoryElement.setAttribute('data-contact-id', contactId); // Na primer, menjamo na 123
                messageDiv.setAttribute('data-message-id', msg.id);
                messageDiv.setAttribute('data-receiver-id', msg.receiver_id);


                chatHistoryElement.appendChild(messageDiv);

                // Selektuj sve poruke koje nisu označene kao "pročitane"
                const unreadMessagesDiv = chatHistoryElement.querySelectorAll('div[data-message-id]:not(.read)');

                // Ako postoje nepročitane poruke, skroluj do poslednje
                if (unreadMessagesDiv.length > 0) {
                    const lastUnreadMessage = unreadMessagesDiv[unreadMessagesDiv.length-1];  // Poslednja nepročitana poruka

                    // Skroluj do te poruke
                    lastUnreadMessage.scrollIntoView({
                        behavior: 'smooth',   // Glatko skrolovanje
                        block: 'nearest'      // Podesi poziciju poruke na vidljivo područje (ne mora biti na samom vrhu)
                    });
                }else{
                    // Skroluj na dno chat-a
                    chatHistoryElement.scrollTop = chatHistoryElement.scrollHeight;
                }

                // Clear input after sending
                document.getElementById('content').value = '';

                // Ako poruka NIJE poslata od strane trenutnog korisnika i NIJE pročitana
                if(!isSentByCurrentUser && !msg.read_at) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach((entry, index) => {
                            const isLastEntry = (index === entries.length - 1); // Da li je trenutni entry poslednji
                            if(entry.isIntersecting && document.visibilityState === 'visible') {
                                // Dodaj malu pauzu da potvrdimo da je korisnik stvarno video poruku
                                sendWhisper(msg);
                                if (isLastEntry) {
                                    // Selektujemo nepročitane poruke pri otvaranju
                                    if (!hasSelectedUnreadMessages) {
                                        selectUnreadMessages(msg.sender_id);
                                        hasSelectedUnreadMessages = true;
                                    }
                                }
                            }
                        });
                    }, { threshold: 0 }); // 0% vidljivost + provera taba

                    observer.observe(messageDiv);
                }

                // Dodajemo status pročitano
                if(msg.read_at && isSentByCurrentUser) {
                    const messageElement = document.querySelector(`[data-message-id="${msg.id}"]`);
                    if (messageElement) {
                        messageElement.classList.add('read');
                        messageElement.querySelector('.read-status').innerHTML = `
                            <i class="fas fa-check-double text-primary" title="Pročitano ${formatDate(msg.read_at)}"></i>
                        `;
                    }
                }
            }
        });
    }


// Funkcija koja se poziva kada se korisnik skroluje na vrh
const chatHistoryElement = document.getElementById('chatHistory');

chatHistoryElement.addEventListener('scroll', async () => {
    // Ako je skrolovanje došlo do vrha i još nismo učitali poruke
    if (chatHistoryElement.scrollTop === 0 && !isLoading) {
        isLoading = true;  // Sprečava višestruko učitavanje

        const contactId = chatHistoryElement.getAttribute('data-contact-id');
        const serviceId = document.getElementById('service_id').value;

        const apiToken = "{{ $token }}";  // Token za autentifikaciju

        try {
            // API poziv za starije poruke
            const response = await fetch(`/api/get-messages?contact_id=${contactId}&service_id=${serviceId}&page=${currentPage}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${apiToken}`,  // Dodajte token
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            // Sortiranje poruka po 'created_at' u rastućem redosledu (starije poruke prvo)
            const chatHistory = data.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));  // Poruke sortirane od starijih ka novijim

            currentPage += 1;  // Nema više stranica

            // Pomeranje skrola za 10% visine chat-a
            const scrollHeight = chatHistoryElement.scrollHeight;
            const scrollTarget = scrollHeight * 0.001;  // 1% od visine chat-a

            // Dodajemo starije poruke na početak (ne brišemo novije poruke)
            chatHistory.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(msg);  // Pretpostavljamo da postoji funkcija koja generiše HTML za poruku
                chatHistoryElement.insertBefore(messageDiv, chatHistoryElement.firstChild);  // Dodajemo na početak
            });

            // Pomeramo skrol za 10% od ukupne visine chat-a
            chatHistoryElement.scrollTop = scrollTarget;

        } catch (error) {
            console.error('Error fetching messages:', error);
        }

        isLoading = false;  // Omogućava novo učitavanje
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
        dateDisplay = `<div class="mb-1 justify-content-center">
                        <div class="date-separator w-100 text-center">
                                <span class="date-text text-secondary">${date}</span>
                            </div>
                        </div>
                    `;
    } else {
        // Ako je datum već prikazan, ne prikazujemo ga ponovo
        dateDisplay = '';  // Prazan string znači da datum neće biti prikazan ponovo
    }

    const isSentByCurrentUser = msg.sender_id === currentUser.id;

    // Zavisno o tome da li je poruka poslana ili primljena, odaberi odgovarajući raspored
    if (isSentByCurrentUser) {
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
                        <div class="conversation-list">
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
</script>

@endsection
