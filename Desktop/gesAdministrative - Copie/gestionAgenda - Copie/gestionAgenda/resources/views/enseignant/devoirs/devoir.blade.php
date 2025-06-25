@extends('layouts.enseignant')

@section('content')
<div class="container py-4">

    <!-- Titre + bouton Ajouter aligné -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-black">
            <i class="fas fa-book me-2"></i>Liste des Devoirs
        </h2>
        <a href="{{ route('enseignant.devoirs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter un devoir
        </a>
    </div>

                <form method="GET" action="{{ route('devoirs.parClasse') }}" class="d-flex gap-2 align-items-center">
                <div class="input-group">
                    <select name="classe" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">Choisir Classe</option>
                       @foreach ($classes as $c)
                            <option value="{{ $c }}" {{ (isset($classe) && $classe == $c) ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                    @if($classeSelectionnee)
                        <a href="{{ route('listerEleves.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

    <!-- Tableau des devoirs -->
    <div class="table-responsive shadow-sm">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-primary">
                <tr>
                    
                    <th>Titre</th>
                     <th>Description</th>
                    <th>Fichier</th>
                    <th>Date limite</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($devoirs as $devoir)
                    <tr>
                        <td>{{ $devoir->titre }}</td>
                            <td>{{ Str::limit($devoir->description, 50) }}</td>
                        <td>
                            @if ($devoir->fichier)
                                <a href="{{ asset('storage/devoirs/' . $devoir->fichier) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-file-alt"></i> Voir
                                </a>
                            @else
                                <span class="text-muted">Aucun</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($devoir->date_limite)->format('d/m/Y') }}</td>
                        
                        <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('enseignant.devoirs.edit', $devoir->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1"
                                           data-bs-toggle="tooltip" title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('devoirs.destroy', $devoir->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce devoir?')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted text-center">Aucun devoir trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

   
</div>
@endsection
