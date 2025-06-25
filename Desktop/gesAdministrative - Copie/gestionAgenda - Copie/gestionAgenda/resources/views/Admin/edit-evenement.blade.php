@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Modifier l'événement</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('edit-evenement.update', $evenement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" 
                               id="description" name="description" 
                               value="{{ old('description', $evenement->description) }}" 
                               placeholder="Entrer la description..." required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cycle -->
                    <div class="col-md-6 mb-3">
                        <label for="cycle" class="form-label fw-semibold">Cycle <span class="text-danger">*</span></label>
                        <select class="form-select @error('cycle') is-invalid @enderror" id="cycle" name="cycle" required>
                            <option value="" disabled {{ old('cycle', $cycleSelectionne ?? '') ? '' : 'selected' }}>Sélectionnez un cycle</option>
                            <option value="all" {{ old('cycle', $cycleSelectionne ?? '') == 'all' ? 'selected' : '' }}>Tous les cycles</option>
                            @foreach($cycles as $cycle)
                                <option value="{{ $cycle }}" {{ old('cycle', $cycleSelectionne ?? '') == $cycle ? 'selected' : '' }}>
                                    {{ $cycle }}
                                </option>
                            @endforeach
                        </select>
                        @error('cycle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Classes assignées -->
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Classes assignées <span class="text-danger">*</span></label>
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                    <label class="form-check-label fw-semibold" for="selectAllClasses">
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
                                                           {{ is_array(old('classe', $evenement->eleves->pluck('classe')->unique()->toArray())) && in_array($classe, old('classe', $evenement->eleves->pluck('classe')->unique()->toArray())) ? 'checked' : '' }}>
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

                    <!-- Date Début -->
                    <div class="col-md-6 mb-3">
                        <label for="dateDebut" class="form-label">Date Début *</label>
                        <input type="date" class="form-control @error('dateDebut') is-invalid @enderror" 
                               id="dateDebut" name="dateDebut" 
                               value="{{ old('dateDebut', $evenement->dateDebut->format('Y-m-d')) }}" required>
                        @error('dateDebut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Date Fin -->
                    <div class="col-md-6 mb-3">
                        <label for="dateFin" class="form-label">Date Fin *</label>
                        <input type="date" class="form-control @error('dateFin') is-invalid @enderror" 
                               id="dateFin" name="dateFin" 
                               value="{{ old('dateFin', $evenement->dateFin->format('Y-m-d')) }}" required>
                        @error('dateFin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Heure -->
                    <div class="col-md-6 mb-3">
                        <label for="heure" class="form-label">Heure *</label>
                        <input type="time" class="form-control @error('heure') is-invalid @enderror" 
                               id="heure" name="heure" 
                               value="{{ old('heure', $evenement->heure) }}" required>
                        @error('heure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fichier justificatif -->
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Fichier associé (PDF, DOCX, ZIP, JPG, PNG)</label>
                        @if($evenement->image)
                            <p>Fichier actuel : 
                                <a href="{{ route('liste-eve.download', $evenement->id) }}" target="_blank">
                                    Voir / Télécharger
                                </a>
                            </p>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept=".pdf,.docx,.zip,.jpg,.jpeg,.png">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Taille maximale : 2MB</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('Admin.liste-eve') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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

    // Initialisation : définir le cycle courant pour afficher les classes
    filterClasses();
});
</script>
@endpush

@endsection
