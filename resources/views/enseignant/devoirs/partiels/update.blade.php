@extends('layouts.enseignant')

@section('content')
<div class="d-flex justify-content-center align-items-center" id="main-card" style="min-height: 90vh; padding: 30px;">
    <div class="card shadow rounded" style="width: 600px; margin-left: 20px; padding: 20px;">
        <div class="card-header bg-primary text-white text-center rounded">
            <h2 class="mb-0">Modifier le devoir</h2>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('enseignant.devoirs.update', $devoir->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="titre" class="form-label">Titre *</label>
                    <input type="text" class="form-control" name="titre" value="{{ old('titre', $devoir->titre) }}" required>
                </div>

                <div class="mb-3">
                    <label for="classe" class="form-label">Classe *</label>
                    <select class="form-select" name="classe" required>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ $devoir->classe == $c ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4">{{ old('description', $devoir->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="dateLimite" class="form-label">Date limite *</label>
                    <input type="datetime-local" class="form-control" name="dateLimite" 
                           value="{{ old('dateLimite', $devoir->dateLimite->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="fichierJoint" class="form-label">Nouveau fichier (PDF, DOCX, ZIP - max 2MB)</label>
                    <input type="file" class="form-control" name="fichierJoint" accept=".pdf,.docx,.zip">
                    
                       @if($devoir->fichierJoint)
                    <div>
        <a href="{{ route('devoirs.download', $devoir->id) }}" 
           class="btn btn-sm btn-outline-primary rounded-pill">
           <i class="fas fa-download me-1"></i> Voir
        </a>
    @else
        <span class="text-muted">Aucun disponible</span>
                    </div>
    @endif
 
                
                </div>

                <input type="hidden" name="classe_originale" value="{{ $classe }}">

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('devoirs.parClasse', ['classe' => $classe]) }}" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #main-card {
        margin-left: 200px;
    }
    .card {
        border: none;
        border-radius: 10px;
    }
    .form-control, .form-select {
        border-radius: 5px;
        padding: 10px;
    }
    .btn {
        border-radius: 5px;
        padding: 8px 20px;
    }
</style>
@endsection