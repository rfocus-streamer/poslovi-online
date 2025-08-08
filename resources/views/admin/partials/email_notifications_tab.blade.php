<div class="tab-pane fade {{ $activeTab === 'email_notifications' ? 'show active' : '' }}" id="email_notifications">
    <h2 class="mb-4">Email Notifikacije</h2>

    <div class="row">
        <!-- Nepročitane poruke -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nepročitane poruke</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-message-reminders') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Korisnici sa nepročitanim porukama:</label>
                            <div class="border p-2 mb-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($usersWithUnreadMessages as $user)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="users[]" value="{{ $user->id }}"
                                               id="user-{{ $user->id }}" checked>
                                        <label class="form-check-label" for="user-{{ $user->id }}">
                                            {{ $user->firstname }} {{ $user->lastname }} ({{ $user->email }})
                                            <span class="badge bg-danger ms-2">
                                                {{ $user->unread_messages_count }} nepročitanih
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">Nema korisnika sa nepročitanim porukama</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="messageTemplate" class="form-label">Email šablon:</label>
                            <select class="form-select" id="messageTemplate" name="template" required>
                                <option value="">-- Odaberi šablon --</option>
                                @foreach($messageTemplates as $template)
                                    <option value="{{ $template }}">{{ $template }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="messageSubject" class="form-label">Naslov:</label>
                            <input type="text" class="form-control" id="messageSubject"
                                   name="subject" value="Imate nepročitane poruke" required>
                        </div>

                        <div class="mb-3">
                            <label for="additionalMessage" class="form-label">Dodatna poruka:</label>
                            <textarea class="form-control" id="additionalMessage"
                                      name="additional_message" rows="3"
                                      placeholder="Ovo će biti dodato na kraj osnovne poruke..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary"
                                {{ count($usersWithUnreadMessages) ? '' : 'disabled' }}>
                            <i class="fas fa-paper-plane me-1"></i> Pošalji podsjetnik
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Nepročitani odgovori na tikete -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Nepročitani odgovori na tikete</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.send-ticket-reminders') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Korisnici sa nepročitanim odgovorima:</label>
                            <div class="border p-2 mb-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($usersWithUnreadTicketResponses as $user)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="users[]" value="{{ $user->id }}"
                                               id="ticket-user-{{ $user->id }}" checked>
                                        <label class="form-check-label" for="ticket-user-{{ $user->id }}">
                                            {{ $user->firstname }} {{ $user->lastname }} ({{ $user->email }})
                                            <span class="badge bg-danger ms-2">
                                                {{ $user->unread_responses_count }} nepročitanih
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">Nema korisnika sa nepročitanim odgovorima</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ticketTemplate" class="form-label">Email šablon:</label>
                            <select class="form-select" id="ticketTemplate" name="template" required>
                                <option value="">-- Odaberi šablon --</option>
                                @foreach($ticketTemplates as $template)
                                    <option value="{{ $template }}">{{ $template }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ticketSubject" class="form-label">Naslov:</label>
                            <input type="text" class="form-control" id="ticketSubject"
                                   name="subject" value="Imate nepročitane odgovore na tikete" required>
                        </div>

                        <div class="mb-3">
                            <label for="additionalTicketMessage" class="form-label">Dodatna poruka:</label>
                            <textarea class="form-control" id="additionalTicketMessage"
                                      name="additional_message" rows="3"
                                      placeholder="Ovo će biti dodato na kraj osnovne poruke..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning"
                                {{ count($usersWithUnreadTicketResponses) ? '' : 'disabled' }}>
                            <i class="fas fa-paper-plane me-1"></i> Pošalji podsjetnik
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
