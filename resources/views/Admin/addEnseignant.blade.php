@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Ajouter un enseignant
                        </h5>
                        <a href="{{ route('listerEnseignant.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('addEnseignant.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Informations personnelles -->
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                    id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                    id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Informations de connexion -->
                            <div class="col-md-6">
                                <label for="login" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('login') is-invalid @enderror" 
                                    id="login" name="login" value="{{ old('login') }}" required>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="motDePasse" class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('motDePasse') is-invalid @enderror" 
                                        id="motDePasse" name="motDePasse" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('motDePasse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimum 8 caractères</small>
                            </div>

                            <!-- Matière -->
                            <div class="col-md-6">
                                <label for="matiere" class="form-label fw-semibold">Matière enseignée <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('matiere') is-invalid @enderror" 
                                    id="matiere" name="matiere" value="{{ old('matiere') }}" required>
                                @error('matiere')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Cycle -->
                            <div class="col-md-6">
                                <label for="cycle" class="form-label fw-semibold">Cycle <span class="text-danger">*</span></label>
                                <select class="form-select @error('cycle') is-invalid @enderror" 
                                    id="cycle" name="cycle" required>
                                    <option value="" disabled selected>Sélectionnez un cycle</option>
                                     <option value="all" {{ old('cycle') == 'all' ? 'selected' : '' }}>Tous les cycles</option> <!-- nouvelle option -->
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
                                        <!-- Bouton Tout sélectionner -->
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
    // Affichage ou masquage du mot de passe
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('motDePasse');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Filtrage dynamique des classes et gestion du bouton "tout sélectionner"
    document.addEventListener('DOMContentLoaded', function () {
        const cycleSelect = document.getElementById('cycle');
        const classOptions = document.querySelectorAll('.class-option');
        const selectAllCheckbox = document.getElementById('selectAllClasses');

       function filterClasses() {
    const selectedCycle = cycleSelect.value;
    let anyVisible = false;

    classOptions.forEach(option => {
        const checkbox = option.querySelector('input[type="checkbox"]');

        // Si 'all' est sélectionné ou si le cycle correspond
        if (selectedCycle === 'all' || option.dataset.cycle === selectedCycle) {
            option.style.display = 'block';
            checkbox.checked = false;
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

        // Si un cycle est déjà sélectionné au chargement
        if (cycleSelect.value) {
            cycleSelect.dispatchEvent(new Event('change'));
        }
    });
</script>

@endsection