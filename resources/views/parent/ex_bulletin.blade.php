@extends('layouts.parent')

@section('content')
<div class="container py-4">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($eleves->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Sélectionnez un élève</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('parent.ex_bulletin') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <select name="eleve_id" class="form-select" required>
                                <option value="" disabled selected>Choisir un élève</option>
                                @foreach($eleves as $eleveOption)
                                    <option value="{{ $eleveOption->user->id }}"
                                        {{ isset($selectedEleve) && $selectedEleve->user->id == $eleveOption->user->id ? 'selected' : '' }}>
                                        {{ $eleveOption->user->nom }} {{ $eleveOption->user->prenom }} ({{ $eleveOption->classe }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Voir les bulletins
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($selectedEleve))
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Bulletins de {{ $selectedEleve->user->nom }} {{ $selectedEleve->user->prenom }}
                        </h4>
                        <span class="badge bg-white text-dark">{{ $selectedEleve->classe }}</span>
                    </div>
                </div>

                <div class="card-body">
                    @if($bulletins->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bulletins as $bulletin)
                                    <tr>
                                        <td>Bulletin trimestriel</td>
                                        <td>{{ $bulletin->created_at->format('d/m/Y') }}</td>
                                        <td>
                                              @if($bulletin->fichierPdf)
        <a href="{{ route('parent.bulletins.voir', $bulletin->id) }}" 
           class="btn btn-sm btn-outline-primary rounded-pill">
           <i class="fas fa-download me-1"></i>Voir
        </a>
    @else
        <span class="text-muted">Aucun disponible</span>
    @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>Aucun bulletin disponible pour cet élève</h5>
                            <p class="mb-0">Les bulletins apparaîtront ici une fois disponibles</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="alert alert-warning text-center py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>Aucun enfant trouvé dans votre compte parent</h5>
            <p class="mb-0">Veuillez contacter l'administration si vous pensez qu'il s'agit d'une erreur</p>
        </div>
    @endif
</div>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        padding: 1rem 1.5rem;
    }
    .table th {
        font-weight: 600;
    }
    .alert {
        border-radius: 8px;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
    }
</style>
@endsection
