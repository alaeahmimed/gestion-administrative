@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>Ajouter un événement
                        </h5>
                        <a href="{{ route('Admin.liste-eve') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('create-evenement.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" 
                                    value="{{ old('description') }}" 
                                    placeholder="Entrez la description de l'événement" required>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Cycle -->
                            <div class="col-md-6">
                                <label for="cycle" class="form-label fw-semibold">Cycle <span class="text-danger">*</span></label>
                                <select class="form-select @error('cycle') is-invalid @enderror" 
                                    id="cycle" name="cycle" required>
                                    <option value="" disabled selected>Sélectionnez un cycle</option>
                                    <option value="all" {{ old('cycle') == 'all' ? 'selected' : '' }}>Tous les cycles</option>
                                    @foreach($cycles as $cycle)
                                        <option value="{{ $cycle }}" {{ old('cycle') == $cycle ? 'selected' : '' }}>
                                            {{ $cycle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Classes assignées -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Classes assignées <span class="text-danger">*</span></label>
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                            <label class="form-check-label fw-normal" for="selectAllClasses">
                                                Sélectionner toutes les classes visibles
                                            </label>
                                        </div>
                                        <div class="row" id="classes-container">
                                            @foreach($classesByCycle as $cycle => $classes)
                                                @foreach($classes as $classe)
                                                    <div class="col-md-3 mb-2 class-option" data-cycle="{{ $cycle }}" style="display: none;">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                name="classe[]" value="{{ $classe }}"
                                                                id="classe-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                {{ is_array(old('classe')) && in_array($classe, old('classe')) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="classe-{{ $loop->parent->index }}-{{ $loop->index }}">
                                                                {{ $classe }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @error('classe')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dates -->
                            <div class="col-md-6">
                                <label for="dateDebut" class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('dateDebut') is-invalid @enderror" 
                                    id="dateDebut" name="dateDebut" 
                                    value="{{ old('dateDebut') }}" required>
                                @error('dateDebut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="dateFin" class="form-label fw-semibold">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('dateFin') is-invalid @enderror" 
                                    id="dateFin" name="dateFin" 
                                    value="{{ old('dateFin') }}" required>
                                @error('dateFin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Heure et Fichier -->
                            <div class="col-md-6">
                                <label for="heure" class="form-label fw-semibold">Heure <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('heure') is-invalid @enderror" 
                                    id="heure" name="heure" 
                                    value="{{ old('heure') }}" required>
                                @error('heure')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="image" class="form-label fw-semibold">Fichier joint (optionnel)</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                    id="image" name="image">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons de soumission -->
                        <div class="d-flex justify-content-between mt-5">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-eraser me-2"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: none;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
    }
    .form-label {
        font-size: 0.9rem;
    }
    .form-check-input {
        width: 1.1em;
        height: 1.1em;
        margin-top: 0.15em;
    }
    .form-check-label {
        margin-left: 0.3em;
    }
    #classes-container {
        max-height: 200px;
        overflow-y: auto;
    }
    .bg-light {
        background-color: #f8f9fa!important;
    }
    .class-option {
        transition: all 0.3s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cycleSelect = document.getElementById('cycle');
    const classOptions = document.querySelectorAll('.class-option');
    const selectAllCheckbox = document.getElementById('selectAllClasses');

    function filterClasses() {
        const selectedCycle = cycleSelect.value;
        let anyVisible = false;

        classOptions.forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            const cycle = option.getAttribute('data-cycle');

            if (selectedCycle === 'all') {
                option.style.display = 'block';
                checkbox.checked = true;
                anyVisible = true;
            } else if (cycle === selectedCycle) {
                option.style.display = 'block';
                anyVisible = true;
            } else {
                option.style.display = 'none';
                checkbox.checked = false;
            }
        });

        selectAllCheckbox.checked = false;
        selectAllCheckbox.disabled = !anyVisible;
    }

    cycleSelect.addEventListener('change', filterClasses);

    selectAllCheckbox.addEventListener('change', function () {
        const isChecked = this.checked;
        classOptions.forEach(option => {
            if (option.style.display !== 'none') {
                option.querySelector('input[type="checkbox"]').checked = isChecked;
            }
        });
    });

    // Initialiser le filtre au chargement
    filterClasses();
});
</script>
@endsection