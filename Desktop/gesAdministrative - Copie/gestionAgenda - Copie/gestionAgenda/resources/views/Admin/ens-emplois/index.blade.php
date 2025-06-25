@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    {{-- Bouton Retour --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{route('Admin.ens-eleve')}}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i>Emplois du temps des enseignants
        </h1>
    </div>

    <!-- Barre de recherche -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('ens-emplois.index') }}" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Rechercher par nom ou prénom..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('ens-emplois.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($enseignants->isEmpty())
        <div class="alert alert-info shadow-sm">
            <i class="fas fa-info-circle me-2"></i>
            Aucun enseignant trouvé{{ request('search') ? " pour '".request('search')."'" : '' }}.
        </div>
    @else
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-primary">
                            <tr>
                                <th class="ps-4">Enseignant</th>
                                <th>Matière</th>
                                <th>Classes</th>
                                <th>Emploi du temps</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enseignants as $enseignant)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40 symbol-circle me-3">
                                                <span class="symbol-label bg-light-primary text-primary fs-5 fw-semibold">
                                                    {{ substr($enseignant->user->nom, 0, 1) }}{{ substr($enseignant->user->prenom, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $enseignant->user->nom }} {{ $enseignant->user->prenom }}</div>
                                                <small class="text-muted">{{ $enseignant->user->login }}</small>
                                               
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $enseignant->matiere }}</td>
                                   <td>
    @if($enseignant->classe && is_array(json_decode($enseignant->classe, true)))
        @foreach(json_decode($enseignant->classe, true) as $classe)
            <span class="badge bg-light-info text-info mb-1">{{ $classe }}</span>
        @endforeach
    @elseif($enseignant->classe)
        <span class="badge bg-light-info text-info mb-1">{{ $enseignant->classe }}</span>
    @else
        <span class="text-muted">Aucune classe</span>
    @endif
</td>
<td>
    @if($enseignant->emploiTemps)
        <div class="btn-group" role="group">
            <a href="{{ route('emplois.voir', $enseignant->emploiTemps->id) }}" 
               class="btn btn-sm btn-outline-primary rounded-pill me-1"
               target="_blank">
               <i class="fas fa-eye me-1"></i> Voir
            </a>
        </div>
    @else
        <span class="text-muted">Aucun disponible</span>
    @endif
</td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <!-- Bouton importer -->
                                            <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#importModal" 
                                                onclick="setImportModalData({{ $enseignant->id }}, '{{ $enseignant->user->nom }} {{ $enseignant->user->prenom }}')">
                                                <i class="fas fa-upload"></i>
                                            </button>

                                            <!-- Bouton supprimer -->
                                            @if($enseignant->emploiTemps)
                                                <form action="{{ route('ens-emplois.supprimer', $enseignant->emploiTemps->id) }}" method="POST">
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
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($enseignants->hasPages())
                    <div class="card-footer">
                        {{ $enseignants->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ens-emplois.ajouter') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="importModalLabel">
                            <i class="fas fa-file-import me-2"></i>Importer un emploi du temps
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="enseignant_id" id="modal_enseignant_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Enseignant</label>
                            <input type="text" id="modal_enseignant_name" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="file_path" class="form-label">Fichier PDF (max: 2MB)</label>
                            <input type="file" name="file_path" id="file_path" accept=".pdf" required class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Importer
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
    // Définir les données du modal d'import
    function setImportModalData(enseignantId, enseignantName) {
        document.getElementById('modal_enseignant_id').value = enseignantId;
        document.getElementById('modal_enseignant_name').value = enseignantName;
        document.getElementById('importModalLabel').innerText = `Importer emploi - ${enseignantName}`;
    }

    // Validation du fichier
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