<style type="text/css">
    .card-body.full-screen {
        max-height: 80vh;
        overflow-y: auto;
    }
    .filter-btn.active {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        position: relative;
    }
    .filter-btn.active::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid currentColor;
    }
    .form-check-label {
        cursor: pointer;
    }
    .select-all-users {
        font-size: 0.8rem;
    }
</style>

<div class="tab-pane fade {{ $activeTab === 'email_notifications' ? 'show active' : '' }}" id="email_notifications">
    <h2 class="mb-4">Email Notifikacije</h2>

    <!-- Filteri i dugmići -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-primary filter-btn" data-filter="messages">Poruke</button>
                <button class="btn btn-outline-warning filter-btn" data-filter="tickets">Tiketi</button>
                <button class="btn btn-outline-info filter-btn" data-filter="subscriptions">Pretplate</button>
                <button class="btn btn-outline-secondary filter-btn" data-filter="inactive">Neaktivni korisnici</button>
                <button class="btn btn-outline-dark filter-btn" data-filter="custom">Prilagođeni email</button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Nepročitane poruke -->
        <div class="col-md-6 mb-4" data-category="messages">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nepročitane poruke</h5>
                    <span class="badge bg-white text-primary">{{ count($usersWithUnreadMessages) }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-message-reminders') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Korisnici:</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-users">
                                    <i class="fas fa-check-circle me-1"></i> Selelektuj sve
                                </button>
                            </div>
                            <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($usersWithUnreadMessages as $user)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input user-checkbox" type="checkbox"
                                               name="users[]" value="{{ $user->id }}"
                                               id="user-{{ $user->id }}" checked>
                                        <label class="form-check-label d-flex align-items-center" for="user-{{ $user->id }}">
                                            <img src="{{ $user->avatar ? Storage::url('user/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                                 class="rounded-circle me-2" width="30" height="30">
                                            <div>
                                                <div>{{ $user->firstname }} {{ $user->lastname }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge bg-danger ms-auto">
                                                {{ $user->unread_messages_count }}
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-3">Nema korisnika sa nepročitanim porukama</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="messageTemplate" class="form-label">Email šablon:</label>
                                <select class="form-select" id="messageTemplate" name="template" required>
                                    <option value="">-- Odaberi šablon --</option>
                                    @foreach($messageTemplates as $template)
                                        <option value="{{ $template }}">{{ $template }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="messageSubject" class="form-label">Naslov:</label>
                                <input type="text" class="form-control" id="messageSubject"
                                       name="subject" value="Imate nepročitane poruke" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="additionalMessage" class="form-label">Dodatna poruka:</label>
                            <textarea class="form-control" id="additionalMessage"
                                      name="additional_message" rows="2"
                                      placeholder="Ovo će biti dodato na kraj osnovne poruke..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"
                                    {{ count($usersWithUnreadMessages) ? '' : 'disabled' }}>
                                <i class="fas fa-paper-plane me-1"></i> Pošalji podsetnik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Nepročitani odgovori na tikete -->
        <div class="col-md-6 mb-4" data-category="tickets">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nepročitani odgovori na tikete</h5>
                    <span class="badge bg-dark text-warning">{{ count($usersWithUnreadTicketResponses) }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-ticket-reminders') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Korisnici:</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-users">
                                    <i class="fas fa-check-circle me-1"></i> Selelektuj sve
                                </button>
                            </div>
                            <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($usersWithUnreadTicketResponses as $user)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input user-checkbox" type="checkbox"
                                               name="users[]" value="{{ $user->id }}"
                                               id="ticket-user-{{ $user->id }}" checked>
                                        <label class="form-check-label d-flex align-items-center" for="ticket-user-{{ $user->id }}">
                                            <img src="{{ $user->avatar ? Storage::url('user/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                                 class="rounded-circle me-2" width="30" height="30">
                                            <div>
                                                <div>{{ $user->firstname }} {{ $user->lastname }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge bg-danger ms-auto">
                                                {{ $user->unread_responses_count }}
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-3">Nema korisnika sa nepročitanim odgovorima</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="ticketTemplate" class="form-label">Email šablon:</label>
                                <select class="form-select" id="ticketTemplate" name="template" required>
                                    <option value="">-- Odaberi šablon --</option>
                                    @foreach($ticketTemplates as $template)
                                        <option value="{{ $template }}">{{ $template }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="ticketSubject" class="form-label">Naslov:</label>
                                <input type="text" class="form-control" id="ticketSubject"
                                       name="subject" value="Imate nepročitane odgovore na tikete" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="additionalTicketMessage" class="form-label">Dodatna poruka:</label>
                            <textarea class="form-control" id="additionalTicketMessage"
                                      name="additional_message" rows="2"
                                      placeholder="Ovo će biti dodato na kraj osnovne poruke..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning"
                                    {{ count($usersWithUnreadTicketResponses) ? '' : 'disabled' }}>
                                <i class="fas fa-paper-plane me-1"></i> Pošalji podsetnik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pretplate bez ponuda -->
        <div class="col-md-6 mb-4" data-category="subscriptions">
            <div class="card h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pretplate bez ponuda</h5>
                    <span class="badge bg-white text-info">{{ count($usersWithSubscriptionsWithoutServices) }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-subscription-reminders') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Korisnici:</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-users">
                                    <i class="fas fa-check-circle me-1"></i> Selelektuj sve
                                </button>
                            </div>
                            <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($usersWithSubscriptionsWithoutServices as $user)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input user-checkbox" type="checkbox"
                                               name="users[]" value="{{ $user->id }}"
                                               id="subscription-user-{{ $user->id }}" checked>
                                        <label class="form-check-label d-flex align-items-center" for="subscription-user-{{ $user->id }}">
                                            <img src="{{ $user->avatar ? Storage::url('user/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                                 class="rounded-circle me-2" width="30" height="30">
                                            <div>
                                                <div>{{ $user->firstname }} {{ $user->lastname }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge bg-info ms-auto">
                                                <i class="fas fa-crown"></i>
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-3">Nema korisnika sa pretplatama bez ponuda</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="subscriptionTemplate" class="form-label">Email šablon:</label>
                                <select class="form-select" id="subscriptionTemplate" name="template" required>
                                    <option value="">-- Odaberi šablon --</option>
                                    @foreach($subscriptionTemplates as $template)
                                        <option value="{{ $template }}">{{ $template }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="subscriptionSubject" class="form-label">Naslov:</label>
                                <input type="text" class="form-control" id="subscriptionSubject"
                                       name="subject" value="Koristite vašu pretplatu" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="additionalSubscriptionMessage" class="form-label">Dodatna poruka:</label>
                            <textarea class="form-control" id="additionalSubscriptionMessage"
                                      name="additional_message" rows="2"
                                      placeholder="Ovo će biti dodato na kraj osnovne poruke..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-info text-white"
                                    {{ count($usersWithSubscriptionsWithoutServices) ? '' : 'disabled' }}>
                                <i class="fas fa-paper-plane me-1"></i> Pošalji podsetnik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Neaktivni korisnici -->
        <div class="col-md-6 mb-4" data-category="inactive">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Neaktivni korisnici</h5>
                    <span class="badge bg-white text-secondary">{{ count($inactiveUsers) }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-inactive-reminders') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Korisnici:</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-users">
                                    <i class="fas fa-check-circle me-1"></i> Selelektuj sve
                                </button>
                            </div>
                            <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($inactiveUsers as $user)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input user-checkbox" type="checkbox"
                                               name="users[]" value="{{ $user->id }}"
                                               id="inactive-user-{{ $user->id }}" checked>
                                        <label class="form-check-label d-flex align-items-center" for="inactive-user-{{ $user->id }}">
                                            <img src="{{ $user->avatar ? Storage::url('user/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                                 class="rounded-circle me-2" width="30" height="30">
                                            <div>
                                                <div>{{ $user->firstname }} {{ $user->lastname }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge bg-secondary ms-auto">
                                                {{ \Carbon\Carbon::parse($user->last_seen_at)->diffForHumans() ?: 'Nikad' }}
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-3">Nema neaktivnih korisnika</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="inactiveTemplate" class="form-label">Email šablon:</label>
                                <select class="form-select" id="inactiveTemplate" name="template" required>
                                    <option value="">-- Odaberi šablon --</option>
                                    @foreach($inactiveTemplates as $template)
                                        <option value="{{ $template }}">{{ $template }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="inactiveSubject" class="form-label">Naslov:</label>
                                <input type="text" class="form-control" id="inactiveSubject"
                                       name="subject" value="Vaš nalog vas čeka" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="additionalInactiveMessage" class="form-label">Dodatna poruka:</label>
                            <textarea class="form-control" id="additionalInactiveMessage"
                                      name="additional_message" rows="2"
                                      placeholder="Ovo će biti dodato na kraj osnovne poruke..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-secondary"
                                    {{ count($inactiveUsers) ? '' : 'disabled' }}>
                                <i class="fas fa-paper-plane me-1"></i> Pošalji podsetnik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Prilagođeni email -->
        <div class="col-md-6 mb-4" data-category="custom">
            <div class="card h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prilagođeni email</h5>
                    <i class="fas fa-cog"></i>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-custom-email') }}" id="customEmailForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Primaoci:</label>
                            <select class="form-select" id="customRecipients" name="recipients[]" multiple>
                                <option value="all">Svi korisnici</option>
                                <option value="active">Aktivni korisnici</option>
                                <option value="inactive">Neaktivni korisnici</option>
                                <option value="premium">Premium korisnici</option>
                                <option value="free">Besplatni korisnici</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ili unesite email adrese:</label>
                            <textarea class="form-control" id="customEmails" name="emails" rows="2"
                                      placeholder="email1@example.com, email2@example.com"></textarea>
                            <small class="text-muted">Razdvojite adrese zarezom</small>
                        </div>

                        <div class="mb-3">
                            <label for="customSubject" class="form-label">Naslov:</label>
                            <input type="text" class="form-control" id="customSubject"
                                   name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="customContent" class="form-label">Sadržaj:</label>
                            <textarea class="form-control" id="customContent" name="content" rows="5"
                                      placeholder="Poštovani korisniče,&#10;&#10;Vaš prilagođeni sadržaj ovde..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">
                                <i class="fas fa-paper-plane me-1"></i> Pošalji email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funkcija za filtriranje kartica
        const filterButtons = document.querySelectorAll('.filter-btn');
        const cards = document.querySelectorAll('[data-category]');

        // Funkcija za selektovanje svih korisnika
        const selectAllButtons = document.querySelectorAll('.select-all-users');
        selectAllButtons.forEach(button => {
            button.addEventListener('click', function() {
                const container = this.closest('.d-flex').nextElementSibling;
                const checkboxes = container.querySelectorAll('.user-checkbox');
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });

                this.querySelector('i').className = allChecked ?
                    'fas fa-check-circle me-1' : 'fas fa-times-circle me-1';
            });
        });

        // Postavi inicijalno stanje za dugme "Poruke"
        const initialFilter = 'messages';
        let selectedFilter = initialFilter;

        // Aktiviraj dugme "Poruke" na početku
        filterButtons.forEach(btn => {
            if (btn.getAttribute('data-filter') === initialFilter) {
                btn.classList.add('active');
            }
        });

        // Prikaži samo kartice za "Poruke" na početku
        cards.forEach(card => {
            if (card.getAttribute('data-category') === initialFilter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Dodaj event listenere za filter dugmad
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');

                // Ažuriraj selektovano dugme
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                selectedFilter = filter;

                // Filtriraj kartice
                cards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-category') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Validacija prilagođenog emaila
        document.getElementById('customEmailForm').addEventListener('submit', function(e) {
            const recipients = document.getElementById('customRecipients').selectedOptions;
            const emails = document.getElementById('customEmails').value;

            if (recipients.length === 0 && emails.trim() === '') {
                e.preventDefault();
                alert('Morate odabrati bar jednu grupu primaoca ili uneti email adrese!');
            }
        });
    });
</script>
