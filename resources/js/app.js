import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Globalni objekat za praćenje nepročitanih poruka
window.unreadMessages = {};
let sentMessages = new Set(); // Set za praćenje već poslanih ID-jeva
let switchContact = 0;

import axios from 'axios';

axios.defaults.headers.common['X-CSRF-TOKEN'] = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');

// Opcionalno, ako koristiš sesije, postavi i 'withCredentials'
axios.defaults.withCredentials = true;

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Omogući globalnu upotrebu Pusher-a
window.Pusher = Pusher;

// Inicijalizuj Echo sa Pusher konfiguracijom
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authorizer: (channel) => ({ // Додајте ауторизацију за приватне канале
        authorize: (socketId, callback) => {
                // Dodaj CSRF token u svaki axios zahtev
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.post('/broadcasting/auth', {
                socket_id: socketId,
                channel_name: channel.name
            })
            .then(response => callback(false, response.data))
            .catch(error => callback(true, error));
        }
    })
});

// window.Echo.connector.pusher.connection.bind('connected', () => {
//     console.log('✅ Повезан на Pusher! Socket ID:', window.Echo.socketId());
// });
window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('❌ Грешка у конекцији:', err);
});

// Функција за слање поруке
function handleSendMessage(event) {
    event.preventDefault(); // Спречи подразумевано понашање формe

    const form = event.target;
    const formData = new FormData(form);

    // Dodaj CSRF token u svaki axios zahtev
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Dodaj unreadMessages u formData ako postoje
    if (Object.keys(unreadMessages).length > 0) {
        // Filtriraj neposlate poruke (one koje nisu u sentMessages)
        const unreadMessagesWithTime = Object.entries(unreadMessages)
            .filter(([id, time]) => !sentMessages.has(id))  // Filtriraj samo nove poruke
            .map(([id, time]) => ({
                message_id: id,
                read_at: time
            }));

        // Ako postoji nova poruka koja nije još poslata, dodaj je u formData
        if (unreadMessagesWithTime.length > 0) {
            formData.append('unreadMessages', JSON.stringify(unreadMessagesWithTime));

            // Dodaj ove poruke u sentMessages kako bi se označile kao poslate
            unreadMessagesWithTime.forEach(message => {
                sentMessages.add(message.message_id);
            });
        }
    }

    axios.post('/send-message', formData, {
        headers: {
            'Content-Type': 'multipart/form-data' // Обавезно за фајлове
        }
    })
    .then(response => {
        // Додај поруку у DOM
        //console.log(response.data);
        // Ресетуј форму (очисти поља)
        form.reset();
    })
    .catch(error => {
        if(error.response.status === 403){
            showBlockMessage(error.response.data.message, 'none');
        }
        console.error('Грешка при слању:', error.response);
    });
}

// Selektuj element sa ID-jem 'content'
const contentElement = document.getElementById('content');

// Dodaj event listener na input za slanje poruke kada pritisneš Enter
if (contentElement) {
    contentElement.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && !event.shiftKey) {  // Ako je pritisnut Enter bez Shift-a
            event.preventDefault();  // Spreči da Enter dodaje novu liniju u textarea

            // Pre nego što pošaljemo poruku, označi sve nepročitane poruke kao pročitane
            const chatHistoryElement = document.getElementById('chatHistory');
                  chatHistoryElement.scrollTop = chatHistoryElement.scrollHeight;

            // Simuliraj submit događaj forme
            const submitEvent = new Event('submit', {
                bubbles: true,  // Omogućava da se događaj širi prema roditeljima
                cancelable: true  // Omogućava da sprečiš podrazumevano ponašanje
            });

            document.querySelector('#messageForm').dispatchEvent(submitEvent);  // Pošaljite poruku
        }
    });
}

// Selektuj element sa ID-jem 'messageForm'
const messageForm = document.querySelector('#messageForm');

// Proveri da li element postoji pre nego što dodaš event listener
if (messageForm) {
    messageForm.addEventListener('submit', function(event) {
        event.preventDefault();
        handleSendMessage(event); // Pozivamo funkciju koja šalje poruku
    });
}

// // Функција за додавање поруке у DOM
function appendNewMessage(msg) {
    if (window.location.pathname != '/messages') {
        return true;
    }

    const chatHistory = document.getElementById('chatHistory');

    // Pronađi sve <p> tagove unutar chatHistory
    const paragraphs = chatHistory.querySelectorAll('p');

    paragraphs.forEach(p => {
        if (p.textContent.trim() === 'Nema poruka za ovu uslugu.') {
            p.remove();
        }
    });


    const authUser = document.querySelector('meta[name="user_id"]').getAttribute('content')
    const isSentByCurrentUser = msg.sender_id === currentUser.id;
    const sender = msg.sender_id === authUser.id ? msg.sender : msg.receiver;
    const msgBackground = msg.role === 'buyer' ? 'leftChat' : 'rightChat';
    const msgBackgroundSender = msg.role === 'buyer' ? 'rightChat' : 'leftChat';

    // Formatiraj datum i vreme
    const formattedDate = formatDate(msg.created_at);
    const [date, time] = formattedDate.split(' ');  // Razdvaja datum (YYYY-MM-DD) i vreme (HH:MM)

    let attach = `${msg.attachment_name}`;

    const messageDiv = document.createElement('div');

    // Ovisno o tome da li je poruka poslana ili primljena, odaberi odgovarajući raspored
    if (isSentByCurrentUser) {
         // Dodaj HTML za poruku
        messageDiv.innerHTML += `
            <div class="conversation-list-right" data-date="${date}">
                <div class="chat-avatar">
                    <img src="storage/user/${msg.sender.avatar}" alt="You" class="rounded-circle ms-2" style="width: 50px; height: 50px; margin-right:15px;">
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
                        ${msg.attachment ? `
                        <div class="d-flex justify-content-end mt-1">
                            <small>
                                <a href="/${msg.attachment}" target="_blank" class="text-decoration-none">
                                    <i class="fa fa-download"></i> ${attach}
                                </a>
                            </small>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    } else {
        // Dodaj HTML za poruku
        messageDiv.innerHTML += `
            <div class="conversation-list" data-date="${date}">
                <div class="chat-avatar">
                    <img src="storage/user/${msg.sender.avatar}" alt="You" class="rounded-circle ms-2" style="width: 50px; height: 50px; margin-right:15px;">
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
                        ${msg.attachment ? `
                        <div class="d-flex justify-content-end mt-1">
                            <small>
                                <a href="/${msg.attachment}" target="_blank" class="text-decoration-none">
                                    <i class="fa fa-download"></i> ${attach}
                                </a>
                            </small>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    // Dodaj poruku u chat history
    const chatHistoryElement = document.getElementById('chatHistory');
    messageDiv.setAttribute('data-message-id', msg.id);
    const serviceElement = document.getElementById('service_id').value;
    if(msg.service_id == serviceElement){
        chatHistoryElement.appendChild(messageDiv);

        // Ako poruka NIJE poslata od strane trenutnog korisnika i NIJE pročitana
        if (!isSentByCurrentUser && !msg.read_at) {

            // Kreiramo novi Intersection Observer za detekciju kada je chat-container vidljiv
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    // Ako je element vidljiv i tab je aktivan, šaljemo whisper
                    if (entry.isIntersecting && isPageVisible()) {
                        //console.log('Chat container je vidljiv na ekranu!');
                        sendWhisper(msg); // Pretpostavljamo da imate `msg` objekat
                    }
                });
            }, { threshold: 0.5 }); // 50% elementa mora biti vidljivo

            // Selektujemo sve chat kontejner elemente
            const chatContainers = document.querySelectorAll('.chat-container');
            chatContainers.forEach(container => observer.observe(container));

            // Dodajemo event listener za visibilitychange, kako bismo reagovali kada tab postane aktivan
            document.addEventListener('visibilitychange', () => {
                if (isPageVisible() && !isSentByCurrentUser) {
                    //console.log('Tab je sada aktivan!');
                    // Kada tab postane aktivan, proveravamo da li je chat container vidljiv
                    chatContainers.forEach(container => {
                        if (container.getBoundingClientRect().top < window.innerHeight * 0.5) {
                            // Ako je chat container već vidljiv, šaljemo whisper
                            sendWhisper(msg);
                        }
                    });
                }
            });
        }
    }

    // Skroluj na dno chat-a
    //chatHistoryElement.scrollTop = chatHistoryElement.scrollHeight;
    // Skroluj do te poruke
    chatHistoryElement.scrollTo({
        top: chatHistoryElement.scrollHeight,
        behavior: 'smooth'
    });
    adjustDateSeparators(); // Dodajte ovaj poziv
}


// Funkcija koja proverava da li je tab aktivan
function isPageVisible() {
    return document.visibilityState === 'visible';
}

// Funkcija koja šalje whisper kada je poruka viđena
window.sendWhisper = function(msg) {
    if (isPageVisible()) {
        // Provera da li je već poslat whisper za ovu poruku (prema ID-u poruke)
        if (!window.unreadMessages[msg.id]) {
            // Dodajemo ID poruke u unreadMessages objekat
            window.unreadMessages[msg.id] = formatDate(new Date().toISOString());
        }
        //console.log('Tab je aktivan i chat container je vidljiv. Šaljem whisper...');
        window.Echo.private('messages')
            .whisper('messageSeen', {
                message_id: msg.id,
                receiver_id: msg.receiver_id,
                read_at: new Date().toISOString()
            });
        updateUnreadMessages(msg.sender_id);
        // Pronađite <ul> element (ako je u pitanju samo jedan, koristite njegov id ili klasu)
        var contactList = document.querySelector('.list-group');

        // Pronađite sve span elemente sa klasom 'unread-badge' unutar tog <ul>
        var unreadBadges = contactList.querySelectorAll('.unread-badge');

        // Inicijalizujte brojač za nepročitane poruke
        var totalUnread = 0;

        // Iterirajte kroz sve span elemente
        unreadBadges.forEach(function(badge) {
            // Pronađite tekst unutar span-a, koji je broj nepročitanih poruka
            var unreadCount = parseInt(badge.textContent.trim(), 10);

            // Ako broj nije NaN, dodajte ga ukupnom broju
            if (!isNaN(unreadCount) && unreadCount > 0) {
                totalUnread += unreadCount;
            }
        });
        updateTotalUnreadMessages(switchContact, totalUnread);
    } else {
        //console.log('Tab nije aktivan, whisper nije poslat.');
    }
}

// Funkcija koja menja broj unutar odgovarajućeg <span> elementa
window.updateUnreadMessages = function(contactId, newCount = null) {

    const span = document.querySelector(`[data-user-unread-messages-id="${contactId}"]`);

    if (span) {
        let currentCount = parseInt(span.textContent.trim()) || 0;

        if (newCount === null) {
            newCount = Math.max(0, currentCount - 1);
        }

        span.textContent = newCount;
        span.style.display = newCount > 0 ? 'block' : 'none';
    }
};

// Funkcija koja menja broj unutar odgovarajućeg menu elementa
window.updateTotalUnreadMessages = function(contactId, newCount = null) {

    // Proveri da li meta element sa name="user_id" postoji
    const userMeta = document.querySelector('meta[name="user_id"]');
    if(userMeta){
        let userId = userMeta.getAttribute('content');

        // Selektujemo span u meniju za broj novih poruka
        const menuBadge = document.querySelector(`#unread-count-id-${userId}`);  // Pravilo: koristi backticks za interpolaciju

        if (menuBadge && contactId != userId) {
            let currentCount = parseInt(menuBadge.textContent.trim()) || 0;

            if (newCount === null) {
                // Ako newCount nije prosleđen, smanji broj za 1
                newCount = Math.max(0, currentCount - 1);
            }

            // Ažuriraj broj poruka u meniju
            menuBadge.textContent = newCount;

            // Ako je newCount veći od 0, prikazujemo broj, inače ga sakrivamo
            menuBadge.style.display = newCount > 0 ? 'inline-block' : 'none';
        }
    }
}

// Funkcija koja dodaje badge unutar service-item-a na osnovu data-service-unread-messages-id
function addUnreadBadge(serviceId, unreadCount) {
    // Selektujemo div sa odgovarajućim data-service-unread-messages-id
    const serviceItem = document.querySelector(`[data-service-unread-messages-id="${serviceId}"]`);

    if (serviceItem) {
        // Kreiramo novi <span> element za badge
        const existingBadge = serviceItem.querySelector('.unread-badge'); // Proveravamo da li već postoji badge

        // Ako badge već postoji, samo ažuriramo njegov tekst
        if (existingBadge) {
            existingBadge.textContent = unreadCount;
            if (unreadCount > 0) {
                existingBadge.style.display = 'inline';  // Prikazujemo badge
            } else {
                existingBadge.style.display = 'none';  // Sakrivamo badge ako nema poruka
            }
        } else {
            // Ako badge ne postoji, kreiramo novi
            const badge = document.createElement('span');
            badge.classList.add('unread-badge', 'badge', 'bg-danger', 'rounded-pill', 'position-absolute', 'top-0', 'start-100', 'translate-middle');
            badge.textContent = unreadCount;  // Postavljamo broj nepročitanih poruka u badge
            serviceItem.appendChild(badge);  // Dodajemo badge unutar service-item-a

            // Prikazujemo ili sakrivamo badge u zavisnosti od broja poruka
            if (unreadCount > 0) {
                badge.style.display = 'inline';  // Prikazujemo badge
            } else {
                badge.style.display = 'none';  // Sakrivamo badge ako nema poruka
            }
        }
    }
}

// Korišćenje Presence kanala
const presenceChannel = window.Echo.join('presence-online-status')
    .here(users => {
        //users.forEach(user => updateOnlineStatus(user, true));
        //console.log('All users by contacts');
    })
    .joining(user => {
        updateContactStatus(user.id, true, formatDate(user.last_seen_at))
    })
    .leaving(user => {
        selectUnreadMessages(switchContact);
        updateContactStatus(user.id, false, formatDate(user.last_seen_at))
        // Pošaljemo nepročitane poruke pre nego što korisnik napusti chat
        if (Object.keys(unreadMessages).length > 0) {
            const unreadMessagesToSend = Object.entries(unreadMessages)
                    .filter(([id, time]) => !sentMessages.has(id))  // Filtriramo one koje nismo poslali
                    .map(([id, time]) => ({
                            message_id: id,
                            read_at: time
                    }));

            // Ako imamo poruka koje nisu poslati, šaljemo ih
            if (unreadMessagesToSend.length > 0) {
                 sendUnreadMessages(unreadMessagesToSend);

                // Označavamo poruke kao poslate
                unreadMessagesToSend.forEach(message => {
                    sentMessages.add(message.message_id);
                });
            }
        }
    });

    // Funkcija za ažuriranje statusa
    function updateContactStatus(userId, status, lastSeenAt) {
        const contactItem = document.querySelector(`.contact-item[data-user-id="${userId}"]`);

        if (!contactItem) {
            return;
        }

        // Ažuriraj status (online/offline)
        const statusElement = contactItem.querySelector('.status-online, .status-offline');
        statusElement.textContent = status ? 'Online' : 'Offline';
        statusElement.classList.toggle('status-online', status);
        statusElement.classList.toggle('status-offline', !status);

        // Ažuriraj poslednju aktivnost
        const lastSeenElement = contactItem.querySelector('.text-muted');
        if (lastSeenAt) {
            lastSeenElement.textContent = `Poslednja aktivnost: ${lastSeenAt}`;
        } else {
            lastSeenElement.textContent = 'Poslednja aktivnost: Nema podataka';
        }
    }

// Proveri da li meta element sa name="user_id" postoji
const userMeta = document.querySelector('meta[name="user_id"]');

if (userMeta) {
    // Ako postoji korisnički ID, započni slušanje događaja preko Laravel Echo
    window.Echo.private(`messages`)
        .listen('.MessageSent', (e) => {
            //console.log('ДОГАЂАЈ ПРИМЉЕН:', e);
            appendNewMessage(e.message);
        });
}

// Provera meta taga
if (userMeta && userMeta.getAttribute('content') !== '') { // Provera da li je user_id prisutan
    window.Echo.private(`messages`)
        .listen('MessageSent', (e) => {
            // Ažuriraj brojač za ovog korisnika
            const sender_id = e.message.sender_id;
            updateTotalUnreadMessages(sender_id, e.message.totalUnreadMessages);
            updateUnreadMessages(sender_id, e.message.totalSenderUnreadMessages);
            //console.log('Primljena poruka:', e.message);
            // Iteriramo kroz sve servise u unreadMessagesPerService
            Object.entries(e.message.unreadMessagesPerService).forEach(([serviceId, unreadCount]) => {
                // Pozivamo funkciju za dodavanje badge-a za svaki servis
                addUnreadBadge(serviceId, unreadCount);
            });
            let currentChat = document.querySelector(`[data-contact-id="${sender_id}"]`);
            if(currentChat || userMeta.getAttribute('content') == sender_id){
                appendNewMessage(e.message);
            }
        })

        .listenForWhisper('messageSeen', (data) => {
            const messageElement = document.querySelector(`[data-message-id="${data.message_id}"]`);
            if (messageElement) {
                // Držimo ID setTimeout-a kako bi mogli da ga zaustavimo ako je potrebno
                let timeoutId;
                messageElement.classList.add('read');
                timeoutId = setTimeout(() => {
                    const messageElement = document.querySelector(`[data-message-id="${data.message_id}"]`);
                    const readStatusElement = messageElement.querySelector('.read-status');
                    if (readStatusElement) {
                        readStatusElement.innerHTML = `<i class="fas fa-check-double text-success" title="Pročitano ${formatDate(data.read_at)}"></i>`;  // Promeni sadržaj
                        // Onda zaustavi bilo kakve dalje timeoute (ako postoji)
                        clearTimeout(timeoutId);
                    }
                }, 100);  // Čeka 100ms pre nego što pokuša da selektuje elemente
            }

            // Sad šalješ whisper nazad
            window.Echo.private(`messages`).whisper('updateUnreadMessages', {
                receiver_id: data.receiver_id
            });
        })

        .listenForWhisper('updateUnreadMessages', (data) => {
            updateUnreadMessages(data.sender_id);
        });

        window.addEventListener('beforeunload', function (event) {
            if (Object.keys(unreadMessages).length > 0) {
                // Proveri da li meta element sa name="user_id" postoji
                const userMeta = document.querySelector('meta[name="user_id"]');
                const unreadMessagesToSend = Object.entries(unreadMessages)
                        .filter(([id, time]) => !sentMessages.has(id))  // Filtriramo one koje nismo poslali
                        .map(([id, time]) => ({
                            message_id: id,
                            read_at: time
                        }));

                // Ako imamo poruka koje nisu poslati, šaljemo ih
                if (unreadMessagesToSend.length > 0) {
                    // Ovdje možemo da pokušamo da pošaljemo poruke asinhrono
                    sendUnreadMessages(unreadMessagesToSend).then(() => {
                        // Označavamo poruke kao poslate
                        unreadMessagesToSend.forEach(message => {
                            sentMessages.add(message.message_id);
                        });
                    }).catch(err => {
                        console.error('Došlo je do greške prilikom slanja poruka:', err);
                    });
                }
            }
        });

}else{
     window.Echo.private(`messages`)
        .listen('MessageSent', (e) => {
            // Ažuriraj brojač za ovog korisnika
            const sender_id = e.message.sender_id;
            updateTotalUnreadMessages(sender_id, e.message.totalUnreadMessages);
        })

    // Detekcija izlaska sa stranice
    // document.addEventListener('visibilitychange', function() {
    //     if (window.location.pathname != '/messages') {
    //         console.log('izasao:'+window.location.pathname);
    //     }
    // });

    // Pošaljemo nepročitane poruke pre nego što korisnik napusti chat
    if (Object.keys(unreadMessages).length > 0) {
        const unreadMessagesToSend = Object.entries(unreadMessages)
                .filter(([id, time]) => !sentMessages.has(id))  // Filtriramo one koje nismo poslali
                .map(([id, time]) => ({
                    message_id: id,
                    read_at: time
                }));

        // Ako imamo poruka koje nisu poslati, šaljemo ih
        if (unreadMessagesToSend.length > 0) {
            sendUnreadMessages(unreadMessagesToSend);
            // Označavamo poruke kao poslate
            unreadMessagesToSend.forEach(message => {
                sentMessages.add(message.message_id);
            });
        }
    }
}


window.selectUnreadMessages = function(sender_id, unreadCount = null)
{
    switchContact = sender_id;
    if (Object.keys(unreadMessages).length > 0) {
        const unreadMessagesToSend = Object.entries(unreadMessages)
                .filter(([id, time]) => !sentMessages.has(id))  // Filtriramo one koje nismo poslali
                .map(([id, time]) => ({
                    message_id: id,
                    read_at: time
                }));

        // Ako imamo poruka koje nisu poslati, šaljemo ih
        if (unreadMessagesToSend.length > 0) {
            sendUnreadMessages(unreadMessagesToSend);

            // Označavamo poruke kao poslate
            unreadMessagesToSend.forEach(message => {
                sentMessages.add(message.message_id);
            });
        }
        updateUnreadMessages(sender_id);
    }
}

// Funkcija za slanje nepročitanih poruka na server
function sendUnreadMessages(unreadMessages) {
    // Kreiramo objekat sa nepročitanim porukama
    const formData = new FormData();
    formData.append('unreadMessages', JSON.stringify(unreadMessages));

    // Dodaj CSRF token u svaki zahtev
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    formData.append('_token', csrfToken);

    // Koristimo sendBeacon za slanje podataka
    const url = '/mark-as-read'; // URL na koji šaljemo podatke

    // `sendBeacon()` omogućava slanje podataka serveru čak i kad se stranica napusti
    const result = navigator.sendBeacon(url, formData);

    if (result) {
        console.log('Nepročitane poruke uspešno poslate preko sendBeacon.');
    } else {
        console.error('Neuspešno slanje podataka koristeći sendBeacon.');
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
