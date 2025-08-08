<style type="text/css">

</style>
<div class="tab-pane fade {{ $activeTab === 'email_notifications' ? 'show active' : '' }}" id="email_notifications">
    <h2 class="mb-4">Email Notifikacije</h2>

    <!-- Filteri i dugmići -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-primary" data-filter="messages">Poruke</button>
                <button class="btn btn-outline-warning" data-filter="tickets">Tiketi</button>
                <button class="btn btn-outline-info" data-filter="subscriptions">Pretplate</button>
                <button class="btn btn-outline-secondary" data-filter="inactive">Neaktivni korisnici</button>
                <button class="btn btn-outline-dark" data-filter="custom">Prilagođeni email</button>
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
                                        <input class="form-check-input" type="checkbox"
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
                                        <input class="form-check-input" type="checkbox"
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
                    <span class="badge bg-white text-info"></span>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Korisnici:</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-users">
                                    <i class="fas fa-check-circle me-1"></i> Selelektuj sve
                                </button>
                            </div>
                            <div class="border p-2" style="max-height: 200px; overflow-y: auto;">

                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subscriptionTemplate" class="form-label">Email šablon:</label>
                            <select class="form-select" id="subscriptionTemplate" name="template" required>
                                <option value="">-- Odaberi šablon --</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subscriptionSubject" class="form-label">Naslov:</label>
                            <input type="text" class="form-control" id="subscriptionSubject"
                                   name="subject" value="Koristite vašu pretplatu" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-info text-white"
                                   >
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
                    <span class="badge bg-white text-secondary"></span>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Korisnici:</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-users">
                                    <i class="fas fa-check-circle me-1"></i> Selelektuj sve
                                </button>
                            </div>
                            <div class="border p-2" style="max-height: 200px; overflow-y: auto;">

                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="inactiveTemplate" class="form-label">Email šablon:</label>
                            <select class="form-select" id="inactiveTemplate" name="template" required>
                                <option value="">-- Odaberi šablon --</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="inactiveSubject" class="form-label">Naslov:</label>
                            <input type="text" class="form-control" id="inactiveSubject"
                                   name="subject" value="Vas nalog nas očekuje" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-secondary"
                                    >
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
                    <form method="POST" action="" id="customEmailForm">
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
                                      placeholder="<p>Poštovani korisniče,</p><p>Vaš prilagođeni sadržaj ovde...</p>" required></textarea>
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
        const filterButtons = document.querySelectorAll('[data-filter]');
        const cards = document.querySelectorAll('[data-category]');

        // Početno selektovanje "Poruke"
        const defaultFilter = 'messages';
        let selectedFilter = defaultFilter;

        // Filtriraj dugmadi
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
                        card.querySelector('.card-body').classList.add('full-screen');
                    } else {
                        card.style.display = 'none';
                        card.querySelector('.card-body').classList.remove('full-screen');
                    }
                });
            });
        });

        // Aktiviraj "Poruke" kao početno dugme
        filterButtons.forEach(button => {
            if (button.getAttribute('data-filter') === defaultFilter) {
                button.classList.add('active');
            }
        });

        // Početno filtriranje i primena stila za poruke
        cards.forEach(card => {
            if (card.getAttribute('data-category') === defaultFilter) {
                card.style.display = 'block';
                card.querySelector('.card-body').classList.add('full-screen');
            } else {
                card.style.display = 'none';
                card.querySelector('.card-body').classList.remove('full-screen');
            }
        });
    });
</script>
