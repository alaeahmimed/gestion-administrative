@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Enseignants</h1>
        <div>
            <a href="{{ route('addEnseignant.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-user-plus me-2"></i>Nouvel Enseignant
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel me-2"></i>Importer
            </button>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('listerEnseignant.index') }}">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control border-primary" placeholder="Rechercher par nom, prénom ou matière...">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('listerEnseignant.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('enseignants.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="importModalLabel">Importer des enseignants</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Sélectionnez un fichier Excel</label>
                            <input class="form-control form-control-lg" type="file" id="file" name="file" required accept=".xlsx,.xls,.csv">
                        </div>
                        <div class="alert alert-info bg-light-info border-info">
                            <h5 class="fw-semibold"><i class="fas fa-info-circle me-2"></i>Instructions</h5>
                            <p>Votre fichier doit contenir les colonnes suivantes :</p>
                            <ul class="mb-3">
                                <li><strong>nom</strong> (obligatoire)</li>
                                <li><strong>prenom</strong> (obligatoire)</li>
                                <li><strong>login</strong> (doit être unique)</li>
                                <li><strong>matiere</strong> (spécialité)</li>
                                <li><strong>classe</strong></li>
                                <li><strong>motDePasse</strong></li>
                            </ul>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Importer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   <!-- Messages d'alerte -->
    @if(session('import_errors'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs d'importation</h5>
            <div class="mt-3">
                @foreach(session('import_errors') as $error)
                    <div class="mb-2">
                        <strong>Ligne {{ $error['row'] }}:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach(Arr::wrap($error['errors']) as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-primary">
                        <tr>
                            <th class="ps-4">Nom et Prenom</th>
                            <th>Matière</th>
                            <th>Classe</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enseignants as $enseignant)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40 symbol-circle me-3">
                                            <span class="symbol-label bg-light-primary text-primary fs-5 fw-semibold">
                                                {{ substr(optional($enseignant->user)->nom, 0, 1) }}{{ substr(optional($enseignant->user)->prenom, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ optional($enseignant->user)->nom }} {{ optional($enseignant->user)->prenom }}</div>
                                            <div class="text-muted small">{{ optional($enseignant->user)->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary text-primary">
                                        {{ $enseignant->matiere }}
                                    </span>
                                </td>
                                <td class="classe-cell">
    @if(is_array(json_decode($enseignant->classe, true)))
        @foreach(json_decode($enseignant->classe, true) as $classe)
            <span class="badge bg-light-primary text-primary mb-1">{{ $classe }}</span>
        @endforeach
    @else
        <span class="badge bg-light-primary text-primary">{{ $enseignant->classe }}</span>
    @endif
</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('editEnseignant.edit', $enseignant->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                                           data-bs-toggle="tooltip" title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('deleteEnseignant.destroy', $enseignant) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant?')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-3"></i>
                                    <p class="h5">Aucun enseignant trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($enseignants->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $enseignants->withQueryString()->links() }}
                </div>
            @endif
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
    <style>
    /* Styles existants... */
    
    .classe-cell {
        min-width: 150px;
    }
    
    .classe-cell .badge {
        display: inline-block;
        margin-right: 4px;
        margin-bottom: 4px;
        padding: 4px 8px;
        font-size: 0.75rem;
        border-radius: 4px;
        white-space: nowrap;
    }
    
    /* Pour gérer l'affichage responsive */
    @media (max-width: 768px) {
        .classe-cell {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }
</style>
</style>

<script>
    // Activer les tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection