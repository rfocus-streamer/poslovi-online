<div class="tab-pane fade {{ $activeTab === 'packages' ? 'show active' : '' }}" id="packages">
    <h2 class="mb-4">Plan paketa</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Naziv</th>
                    <th>Cena</th>
                    <th>Trajanje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packages as $package)
                    <tr>
                        <td>{{ $package->id }}</td>
                        <td>{{ $package->name }}</td>
                        <td>{{ $package->price }}</td>
                        <td>
                            @if($package->duration == 'yearly')
                                Godišnji plan
                            @elseif($package->duration == 'monthly')
                                Mesečni plan
                            @else
                                {{ $package->duration }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-center">
            {{ $packages->onEachSide(1)->appends(['tab' => 'packages'])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
