@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
     {{--Bouton Retourner--}}
     <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{route('Admin.ens-eleve')}}" class="btn btn-primary" > Retourne</a>
       
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i>Emplois du temps
        </h1>
    </div>


    <!-- Filtres cycle et classe -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('par-emplois.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select name="cycle" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les cycles</option>
                        @foreach($cycles as $cycle)
                            <option value="{{ $cycle }}" {{ $selectedCycle == $cycle ? 'selected' : '' }}>
                                Cycle {{ $cycle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="classe" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe }}" {{ $selectedClass == $classe ? 'selected' : '' }}>
                                {{ $classe }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedCycle || $selectedClass)
                    <div class="col-md-4">
                        <a href="{{ route('par-emplois.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Réinitialiser
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
@if(!$hasEmplois)
    <div class="alert alert-info shadow-sm">
        <i class="fas fa-info-circle me-2"></i>
        Aucun emploi du temps trouvé{{ $selectedCycle ? " pour le cycle $selectedCycle" : '' }}{{ $selectedClass ? " dans la classe $selectedClass" : '' }}.
    </div>
@else
    <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-primary">
                            <tr>
                                <th class="ps-4">Cycle</th>
                                <th>Classe</th>
                               
                                <th>Emploi du temps</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allClasses as $cycle => $classesInCycle)
    @foreach($classesInCycle as $classeData)
        @php
            $classe = $classeData->classe;
            $emploi = $groupedClasses[$cycle][$classe] ?? null;
        @endphp
        <tr>
            <td class="ps-4">
                <span class="badge bg-light-info text-info">
                    Cycle {{ $cycle }}
                </span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40 symbol-circle me-3">
                        <span class="symbol-label bg-light-primary text-primary fs-5 fw-semibold">
                            {{ substr($classe, 0, 2) }}
                        </span>
                    </div>
                    <div class="fw-semibold">{{ $classe }}</div>
                </div>
            </td>
            <td>
                @if($emploi && !empty($emploi['file_path']))
                    <a href="{{ route('emplois.download', $emploi['id']) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="fas fa-eye me-1"></i></i>Voir
                    </a>
                @else
                    <span class="text-muted">Aucun disponible</span>
                @endif
            </td>
            <td class="text-end pe-4">
                <div class="btn-group">
                    <!-- Bouton importer (toujours visible) -->
                    <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                        data-bs-toggle="modal" 
                        data-bs-target="#importModal" 
                        onclick="setImportModalData('{{ $cycle }}', '{{ $classe }}')">
                        <i class="fas fa-upload"></i>
                    </button>

                    <!-- Bouton supprimer (visible seulement si emploi existe) -->
                    @if($emploi && !empty($emploi['file_path']))
                        <form action="{{ route('par-emplois.destroy', $emploi['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet emploi du temps?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@endforeach
        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('par-emplois.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="importModalLabel">
                            <i class="fas fa-file-import me-2"></i>Importer un emploi du temps
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cycle" id="modal_cycle">
                        <input type="hidden" name="classe" id="modal_classe">

                        <div class="mb-3">
                            <label for="file_path" class="form-label">Fichier PDF (max: 2MB)</label>
                            <input type="file" name="file_path" id="file_path" accept=".pdf" required class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Importer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
    }
    .symbol-circle {
        border-radius: 50%;
    }
    .symbol-40 {
        width: 40px;
        height: 40px;
    }
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    .bg-light-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-light-info {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
</style>

<script>
    // Activer les tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Définir les données du modal d'import
    function setImportModalData(cycle, classe) {
        document.getElementById('modal_cycle').value = cycle;
        document.getElementById('modal_classe').value = classe;
        document.getElementById('importModalLabel').innerText = `Importer emploi - ${classe} (Cycle ${cycle})`;
    }

   // Dans votre script JS, ajoutez :
document.getElementById('file_path').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const submitBtn = document.querySelector('#importModal button[type="submit"]');
    
    if (!file) {
        submitBtn.disabled = true;
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Fichier trop volumineux (max 2MB)');
        this.value = '';
        submitBtn.disabled = true;
    } else if (file.type !== 'application/pdf') {
        alert('Seuls les PDF sont acceptés');
        this.value = '';
        submitBtn.disabled = true;
    } else {
        submitBtn.disabled = false;
    }
});
</script>
@endsection