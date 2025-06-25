@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Élèves</h1>
        <div>
            <a href="{{ route('addEleve.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-user-plus me-2"></i>Nouvel Élève
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel me-2"></i>Importer
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('listerEleves.index') }}" class="d-flex gap-2 align-items-center">
                <div class="input-group">
                    <select name="classe" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe }}" {{ $selectedClass == $classe ? 'selected' : '' }}>
                                {{ $classe }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedClass)
                        <a href="{{ route('listerEleves.index') }}" class="btn btn-outline-secondary">
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
                <form action="{{ route('eleves.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="importModalLabel">Importer des élèves</h5>
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
        <li><strong>nom</strong> (obligatoire) - Nom de l'élève</li>
        <li><strong>prenom</strong> (obligatoire) - Prénom de l'élève</li>
        <li><strong>classe</strong> (obligatoire) - Classe de l'élève</li>
        <li><strong>parent_nom</strong> (optionnel) - Nom du parent</li>
        <li><strong>parent_prenom</strong> (optionnel) - Prénom du parent</li>
        <li><strong>login</strong> (optionnel) - Identifiant de l'élève</li>
        <li><strong>motdepasse</strong> (optionnel) - Mot de passe</li>
    </ul>
    <p class="text-danger">Important: Le parent doit déjà exister dans le système</p>
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
<div class="alert alert-danger alert-dismissible fade show">
    <h5><i class="fas fa-exclamation-triangle me-2"></i>Erreurs d'importation</h5>
    <p>{{ session('import_success', 0) }} élève(s) importé(s) avec succès</p>
    
    <ul class="mt-3">
        @foreach(session('import_errors') as $error)
            <li>
                <strong>Ligne {{ $error['row'] }}:</strong>
                {{ is_array($error['message']) ? implode(', ', $error['message']) : $error['message'] }}
            </li>
        @endforeach
    </ul>
    
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
    <!-- Table Card -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-primary">
                        <tr>
                            <th class="ps-4">Nom et Prénom</th>
                            <th>Classe</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eleves as $eleve)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40 symbol-circle me-3">
                                            <span class="symbol-label bg-light-primary text-primary fs-5 fw-semibold">
                                                {{ substr($eleve->user->nom, 0, 1) }}{{ substr($eleve->user->prenom, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $eleve->user->nom }} {{ $eleve->user->prenom }}</div>
                                           <small class="text-muted">{{ $eleve->user->login }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary text-primary">
                                        {{ $eleve->classe }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('editEleve.edit', $eleve) }}" 
                                           class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                                           data-bs-toggle="tooltip" title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('deleteEleve.destroy', $eleve) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="fas fa-user-graduate fa-2x mb-3"></i>
                                    <p class="h5">
                                        @if($selectedClass)
                                            Aucun élève en {{ $selectedClass }}
                                        @else
                                            Aucun élève enregistré
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($eleves->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $eleves->withQueryString()->links() }}
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