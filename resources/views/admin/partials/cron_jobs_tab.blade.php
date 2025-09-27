<div class="tab-pane fade {{ $activeTab === 'cron_jobs' ? 'show active' : '' }}" id="cron_jobs">
    <h2 class="mb-4">Upravljanje cron zadacima</h2>

    <!-- Status servera -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Status Cron Servisa</h5>
            <div class="d-flex gap-3">
                <button type="button" class="btn btn-outline-primary" id="checkCronStatus">
                    <i class="fas fa-sync-alt"></i> Proveri Status
                </button>
                <span id="cronServiceStatus" class="badge bg-secondary">Nepoznat status</span>
            </div>
        </div>
    </div>

    <!-- Trenutni cron zadaci -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Trenutni cron zadaci</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCronModal">
                <i class="fas fa-plus"></i> Dodaj Novi Cron
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Raspored</th>
                            <th>Komanda</th>
                            <th>Status</th>
                            <th>Poslednje izvršavanje</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody id="cronJobsTable">
                        <tr>
                            <td colspan="6" class="text-center">Učitavanje cron zadataka...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cron Logovi -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Cron Logovi</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-outline-secondary" id="refreshLogs">
                    <i class="fas fa-refresh"></i> Osveži Logove
                </button>
                <button type="button" class="btn btn-outline-danger" id="clearLogs">
                    <i class="fas fa-trash"></i> Obriši Logove
                </button>
            </div>
            <div class="log-container" style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                <pre id="cronLogs" class="mb-0 small">Učitavanje logova...</pre>
            </div>
        </div>
    </div>
</div>

<!-- Modal za dodavanje novog cron zadatka -->
<div class="modal fade" id="addCronModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dodaj novi cron zadatak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCronForm">
                    @csrf
                    <div class="mb-3">
                        <label for="cronSchedule" class="form-label">Raspored (Cron Expression)</label>
                        <input type="text" class="form-control" id="cronSchedule" name="schedule"
                               placeholder="npr. * * * * *" required>
                        <small class="form-text text-muted">
                            Format: minute sat dan mesec dan_u_nedelji<br>
                            Primeri: * * * * * (svaki minut), 0 * * * * (svaki sat), 0 0 * * * (svaki dan u ponoć)
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="cronCommand" class="form-label">Komanda</label>
                        <select class="form-select" id="cronCommand" name="command" required>
                            <option value="">Izaberi komandu...</option>
                            <option value="php artisan schedule:run">Laravel Scheduler</option>
                            <option value="php artisan queue:work">Queue Worker</option>
                            <option value="php artisan backup:run">Backup</option>
                            <option value="php artisan cache:clear">Clear Cache</option>
                            <option value="custom">Prilagođena komanda</option>
                        </select>
                    </div>
                    <div class="mb-3" id="customCommandContainer" style="display: none;">
                        <label for="customCommand" class="form-label">Prilagođena Komanda</label>
                        <input type="text" class="form-control" id="customCommand" name="custom_command"
                               placeholder="npr. php artisan your:command">
                    </div>
                    <div class="mb-3">
                        <label for="cronDescription" class="form-label">Opis</label>
                        <input type="text" class="form-control" id="cronDescription" name="description"
                               placeholder="Opis cron zadatka">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
                <button type="button" class="btn btn-primary" id="saveCronJob">Sačuvaj</button>
            </div>
        </div>
    </div>
</div>

<style>
    .cron-status-active { color: #198754; }
    .cron-status-inactive { color: #dc3545; }
    .cron-status-unknown { color: #6c757d; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prikaz/pronalaženje prilagođene komande
    document.getElementById('cronCommand').addEventListener('change', function() {
        const customContainer = document.getElementById('customCommandContainer');
        customContainer.style.display = this.value === 'custom' ? 'block' : 'none';
    });

    // Učitaj cron zadatke prilikom otvaranja taba
    const cronTab = document.getElementById('cron_jobs');
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (cronTab.classList.contains('active')) {
                    loadCronJobs();
                    loadCronLogs();
                }
            }
        });
    });
    observer.observe(cronTab, { attributes: true });

    // Dugmad za akcije
    document.getElementById('checkCronStatus').addEventListener('click', checkCronStatus);
    document.getElementById('refreshLogs').addEventListener('click', loadCronLogs);
    document.getElementById('clearLogs').addEventListener('click', clearCronLogs);
    document.getElementById('saveCronJob').addEventListener('click', saveCronJob);

    function loadCronJobs() {
        fetch('/admin/cron/jobs')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('cronJobsTable');
                if (data.jobs && data.jobs.length > 0) {
                    tableBody.innerHTML = data.jobs.map((job, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td><code>${job.schedule}</code></td>
                            <td>${job.command}</td>
                            <td>
                                <span class="badge ${job.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                    ${job.status === 'active' ? 'Aktivan' : 'Neaktivan'}
                                </span>
                            </td>
                            <td>${job.last_run || 'Nikad'}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="toggleCronJob(${index})" title="${job.status === 'active' ? 'Zaustavi' : 'Pokreni'}">
                                        <i class="fas ${job.status === 'active' ? 'fa-pause' : 'fa-play'}"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="runCronJobNow(${index})" title="Pokreni sada">
                                        <i class="fas fa-bolt"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteCronJob(${index})" title="Obriši">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Nema cron zadataka</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading cron jobs:', error);
                document.getElementById('cronJobsTable').innerHTML =
                    '<tr><td colspan="6" class="text-center text-danger">Greška pri učitavanju</td></tr>';
            });
    }

    function loadCronLogs() {
        fetch('/admin/cron/logs')
            .then(response => response.text())
            .then(logs => {
                document.getElementById('cronLogs').textContent = logs || 'Nema logova';
            })
            .catch(error => {
                document.getElementById('cronLogs').textContent = 'Greška pri učitavanju logova';
            });
    }

    function checkCronStatus() {
        fetch('/admin/cron/status')
            .then(response => response.json())
            .then(data => {
                const statusElement = document.getElementById('cronServiceStatus');
                statusElement.textContent = data.status === 'active' ? 'Aktivan' :
                                          data.status === 'inactive' ? 'Neaktivan' : 'Nepoznat';
                statusElement.className = `badge bg-${data.status === 'active' ? 'success' :
                                         data.status === 'inactive' ? 'danger' : 'secondary'}`;
            });
    }

    function saveCronJob() {
        const formData = new FormData();
        formData.append('schedule', document.getElementById('cronSchedule').value);

        const commandSelect = document.getElementById('cronCommand');
        const command = commandSelect.value === 'custom' ?
            document.getElementById('customCommand').value : commandSelect.value;

        formData.append('command', command);
        formData.append('description', document.getElementById('cronDescription').value);

        fetch('/admin/cron/jobs', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addCronModal')).hide();
                loadCronJobs();
                showAlert('Cron zadatak je uspešno dodat!', 'success');
            } else {
                showAlert('Greška: ' + data.message, 'danger');
            }
        });
    }

    function clearCronLogs() {
        if (confirm('Da li ste sigurni da želite obrisati sve cron logove?')) {
            fetch('/admin/cron/logs', { method: 'DELETE' })
                .then(() => loadCronLogs());
        }
    }

    // Globalne funkcije za akcije
    window.toggleCronJob = function(index) {
        fetch('/admin/cron/jobs/' + index + '/toggle', { method: 'POST' })
            .then(() => loadCronJobs());
    };

    window.runCronJobNow = function(index) {
        fetch('/admin/cron/jobs/' + index + '/run', { method: 'POST' })
            .then(() => showAlert('Cron zadatak je pokrenut!', 'success'));
    };

    window.deleteCronJob = function(index) {
        if (confirm('Da li ste sigurni da želite obrisati ovaj cron zadatak?')) {
            fetch('/admin/cron/jobs/' + index, { method: 'DELETE' })
                .then(() => loadCronJobs());
        }
    };

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('#cron_jobs .card:first-child').before(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // Inicijalno učitavanje
    checkCronStatus();
});
</script>
