 <div class="tab-pane fade {{ $activeTab === 'projects' ? 'show active' : '' }}" id="projects">
    <h2 class="mb-4">Projekti</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Naziv</th>
                        <th>Klijent</th>
                        <th>Rok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-center">
                {{ $projects->onEachSide(1)->appends(['tab' => 'projects'])->links('pagination::bootstrap-5') }}
            </div>
        </div>
</div>
