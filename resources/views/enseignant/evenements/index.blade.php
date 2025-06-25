@extends('layouts.enseignant')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Événements de la classe</h3>
                    @if($classeSelectionnee)
                        <span class="badge bg-primary">{{ $classeSelectionnee }}</span>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <form method="GET" action="{{ route('enseignant.evenements.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="classe" class="form-label">Filtrer par classe</label>
                                <select class="form-select" name="classe" id="classe" onchange="this.form.submit()">
                                    <option value="">Toutes les classes</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe }}" {{ $classeSelectionnee == $classe ? 'selected' : '' }}>
                                            {{ $classe }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>

                    @if($classeSelectionnee)
                        @if($evenements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th>Date de début</th>
                                            <th>Date de fin</th>
                                            <th class="text-center">Document</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evenements as $evenement)
                                            <tr>
                                                <td>{{ Str::limit($evenement->description, 50) }}</td>
                                                <td>{{ $evenement->dateDebut->format('d/m/Y H:i') }}</td>
                                                <td>{{ $evenement->dateFin->format('d/m/Y H:i') }}</td>
                                                <td class="text-center">
                                                 @if($evenement->image)
        <a href="{{ route('enseignant.evenements.voir', $evenement->id) }}" 
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

                            <div class="d-flex justify-content-center mt-4">
                                {{ $evenements->links() }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Aucun événement trouvé pour la classe <strong>{{ $classeSelectionnee }}</strong>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Veuillez sélectionner une classe pour afficher les événements associés.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection