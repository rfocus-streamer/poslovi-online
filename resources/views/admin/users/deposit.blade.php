<div class="card">
    <div class="d-flex">
        <small>&nbsp;ID: {{$user->id}}</small>
        <small class="ms-auto">Trenutni depozit: {{$user->deposits}} € &nbsp;</small>
    </div>

    <div class="card-header text-center card-header text-white" style="background-color: #198754">
        <i class="fas fa-credit-card"></i> Depozit novca na {{$user->firstname.' '.$user->lastname}} balansu !
    </div>

    <div class="card-body">
        <form id="depositForm" method="POST" action="{{ url('api/admin/' . $user->id . '/deposit') }}">
            @csrf

            <div class="mb-3">
                <label for="amount" class="form-label">Iznos</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="1.00" oninput="formatAmount()" required>
            </div>

            <div class="mb-3">
                <label for="currency" class="form-label">Valuta</label>
                    <select class="form-select" id="currency" name="currency">
                        <option value="EUR">EUR</option>
                            <!--  <option value="USD">USD</option> -->
                    </select>
            </div>

            <div class="mt-4">
                <button type="submit" id="submit-button" class="btn w-100" style="background-color: #198754">
                        <span id="button-text" class="text-white">Dodaj</span>
                </button>
            </div>

            <div class="text-center">
                <small>Email: {{$user->email}}</small>
            </div>
        </form>
    </div>
</div>
