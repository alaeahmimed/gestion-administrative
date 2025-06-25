@extends('layouts.app')



@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i>Bulletin Scolaire
        </h1>
    </div>

    <!-- Filtre classes -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('bulletins.index') }}" class="form-inline">
                <div class="input-group">
                    <select name="classe" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe }}" {{ $selectedClass == $classe ? 'selected' : '' }}>
                                {{ $classe }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedClass)
                        <a href="{{ route('bulletins.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($eleves->count() == 0)
        <div class="alert alert-info shadow-sm">
            <i class="fas fa-info-circle me-2"></i>
            Aucun élève trouvé{{ $selectedClass ? " pour la classe $selectedClass" : '' }}.
        </div>
    @else
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-primary">
                            <tr>
                                <th class="ps-4">Nom et Prénom</th>
                                <th>Classe</th>
                                <th>Bulletin</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eleves as $eleve)
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
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary text-primary">
                                        {{ $eleve->classe }}
                                    </span>
                                </td>
                                <td>
                                    @if($eleve->bulletin)
                                        <a href="{{ route('bulletins.download', $eleve->bulletin->id) }}" target="_blank" 
                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                    @else
                                        <span class="text-muted">Aucun bulletin</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <!-- Bouton importer -->
                                        <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#importModal" 
                                            onclick="document.getElementById('eleve_id').value='{{ $eleve->id }}'"
                                            data-bs-toggle="tooltip" title="Importer">
                                            <i class="fas fa-upload"></i>
                                        </button>

                                        <!-- Bouton supprimer -->
                                        @if($eleve->bulletin)
                                            <form action="{{ route('bulletins.destroy', $eleve->bulletin->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bulletin?')"
                                                        data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                 @if($eleves->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $eleves->withQueryString()->links() }}
                </div>
            @endif
            </div>
        </div>
    @endif

    <!-- Modal import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('bulletins.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="importModalLabel">
                            <i class="fas fa-file-import me-2"></i>Importer un Bulletin
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="eleve_id" id="eleve_id" value="">

                        <div class="mb-3">
                            <label for="fichierPdf" class="form-label">Fichier PDF (max: 2MB)</label>
                            <input type="file" name="fichierPdf" id="fichierPdf" accept=".pdf" required class="form-control">
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
</script>
@endsection