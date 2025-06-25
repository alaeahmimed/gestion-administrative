@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Bouton Retour -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('emploi-evenement.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Titre + bouton Ajouter événement -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Liste des événements</h1>
        <a href="{{ route('create-evenement.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter un événement
        </a>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('Admin.liste-eve') }}" class="d-flex gap-2 align-items-center">
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
                        <a href="{{ route('Admin.liste-eve') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Message de succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tableau des événements -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-primary">
                        <tr>
                            <th class="ps-4">Description</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Heure</th>
                            <th>Document</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evenements as $evenement)
                            <tr>
                                <td class="ps-4">{{ $evenement->description }}</td>
                                <td>{{ $evenement->dateDebut }}</td>
                                <td>{{ $evenement->dateFin }}</td>
                                <td>{{ $evenement->heure }}</td>
                                <td>
                                    @if($evenement->image)
                                        <a href="{{ route('liste-eve.download', $evenement->id) }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                           <i class="fas fa-download me-1"></i>Voir
                                        </a>
                                    @else
                                        <span class="text-muted">Aucun disponible</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('edit-evenement.edit', $evenement->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                                           data-bs-toggle="tooltip" title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('delete-evenement.destroy', $evenement->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-calendar-alt fa-2x mb-3"></i>
                                    <p class="h5">
                                        @if($selectedClass)
                                            Aucun événement pour la classe {{ $selectedClass }}
                                        @else
                                            Aucun événement enregistré
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($evenements->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $evenements->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles spécifiques -->
<style>
    .bg-light-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>

<!-- Tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
