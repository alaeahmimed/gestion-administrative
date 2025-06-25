@extends('layouts.parent')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4" style="font-size: 2.4rem; color: #0077C8; font-weight: bold;">
        <i class="fas fa-file-alt me-2" style="color: grey;"></i> Envoyer une justification à l'administration
    </h3>

    @if(session('success'))
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle me-1" style="color: green;"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('parent.justifyAdmin') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded p-4">
        @csrf

        <div class="mb-3">
            <label for="absence_id" class="form-label fw-bold text-secondary">
                <i class="fas fa-user-clock me-1" style="color: grey;"></i> Sélectionner l'absence
            </label>
            <select name="absence_id" id="absence_id" class="form-control">
                @foreach ($absences as $absence)
                    <option value="{{ $absence->id }}">
                        {{ $absence->dateEnvoi }} - {{ $absence->eleve->nom }} {{ $absence->eleve->prenom }}
                    </option>
                @endforeach
            </select>
            @error('absence_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="raison" class="form-label fw-bold text-secondary">
                <i class="fas fa-align-left me-1" style="color: grey;"></i> Justification
            </label>
            <textarea name="raison" id="raison" class="form-control" rows="4"></textarea>
            @error('raison') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="fichier" class="form-label fw-bold text-secondary">
                <i class="fas fa-paperclip me-1" style="color: grey;"></i> Fichier justificatif (PDF/image)
            </label>
            <input type="file" name="fichier" id="fichier" class="form-control">
            @error('fichier') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1" style="color: white;"></i> Envoyer
            </button>
            <a href="{{ route('parent.messages') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1" style="color: white;"></i> Retour
            </a>
        </div>
    </form>
</div>
@endsection
