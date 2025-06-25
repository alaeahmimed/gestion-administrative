@extends('layouts.enseignant')

@section('content')
<div class="container-fluid">
    <div class="row">
       

        <!-- Main Content -->
        <main class="container-fluid">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Ajouter un devoir</h1>
            </div>

            <div class="card shadow-sm" style="max-width: 800px; margin: 0 auto;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nouveau devoir</h5>
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

                    <form action="{{ route('enseignant.devoirs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="titre" value="{{ old('titre') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="classe" class="form-label">Classe <span class="text-danger">*</span></label>
                                <select id="classe" name="classe" class="form-select" required>
                                    <option value="">-- Sélectionner une classe --</option>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe }}" {{ old('classe') == $classe ? 'selected' : '' }}>
                                            {{ $classe }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="dateLimite" class="form-label">Date limite <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="dateLimite" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fichierJoint" class="form-label">Fichier joint (facultatif)</label>
                            <input type="file" class="form-control" name="fichierJoint">
                            <small class="text-muted">Formats acceptés: PDF, DOCX, JPG, PNG (max 2MB)</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('enseignant.devoirs.devoir') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 48px 0 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }
    
    main {
        margin-left: 250px;
        padding-top: 1rem;
    }
    
    @media (max-width: 767.98px) {
        .sidebar {
            width: 100%;
            position: relative;
            height: auto;
        }
        
        main {
            margin-left: 0;
        }
    }
    
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .card-header {
        padding: 1rem 1.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
    }
    
    .btn {
        border-radius: 0.375rem;
        padding: 0.5rem 1.25rem;
    }
</style>
@endsection