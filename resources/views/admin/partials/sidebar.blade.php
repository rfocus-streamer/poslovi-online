<div class="col-md-3 col-lg-2 sidebar p-0" id="sidebar">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('favicon.ico') }}" class="me-2" style="width: 30px; height: 30px;">
                                <h4 class="text-white mb-0">Admin panel</h4>
                            </div>
                        </a>
                        <button class="btn btn-sm text-white d-lg-none" id="closeSidebar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link text-white {{ $activeTab === 'users' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#users">
                            <i class="fas fa-user "></i> Korisnici
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'privileged_commissions' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#privileged_commissions">
                            <i class="fas fa-percent"></i> Procenati
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'subscriptions' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#subscriptions">
                            <i class="fas fa-crown"></i> Pretplate
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'transactions' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#transactions">
                            <i class="fas fa-money-bill-wave"></i> Transakcije
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'stripe_transactions' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#stripe_transactions">
                            <i class="fas fa-credit-card"></i> Stripe Transakcije
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'services' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#services">
                            <i class="fas fa-file-signature "></i> Ponude
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'fiatpayouts' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#fiatpayouts">
                            <i class="fas fa-money-check"></i> Isplate
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'forcedservices' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#forcedservices">
                            <i class="fas fa-star"></i> Istaknute ponude
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'email_notifications' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           href="#email_notifications">
                           <i class="fas fa-envelope"></i> Email Notifikacije
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'projects' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#projects">
                            <i class="fas fa-handshake "></i> Projekti
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'packages' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#packages">
                            <i class="fas fa-calendar-alt "></i> Plan paketa
                        </a>
                        <a class="nav-link text-white {{ $activeTab === 'unusedfiles' ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            href="#unusedfiles">
                            <i class="fa fa-folder-open"></i> Nepotrebni file-ovi
                        </a>
                    </nav>
                </div>
            </div>
