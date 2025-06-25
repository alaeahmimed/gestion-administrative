@extends('layouts.app')

@section('content')
<style>
    .list-group-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    #parent_results {
        z-index: 1000;
        position: absolute;
        width: calc(100% - 30px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.25rem;
    }
    .is-invalid {
        border-color: #dc3545;
    }
    .search-container {
        position: relative;
    }
    .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h1 class="h3 text-primary">Modifier Élève</h1>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('editEleve.update', $eleve) }}">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom :</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" name="nom" placeholder="Entrer nom..." 
                                       value="{{ old('nom', $eleve->user->nom) }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom :</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                       id="prenom" name="prenom" placeholder="Entrer prénom..." 
                                       value="{{ old('prenom', $eleve->user->prenom) }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                         <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cycle" class="form-label">Cycle :</label>
                                <select class="form-select @error('cycle') is-invalid @enderror" 
                                        id="cycle" name="cycle" required>
                                    <option value="" disabled {{ old('cycle', $eleve->cycle) ? '' : 'selected' }}>Choisir un cycle...</option>
                                    <option value="primaire" {{ old('cycle', $eleve->cycle) == 'primaire' ? 'selected' : '' }}>Primaire</option>
                                    <option value="college" {{ old('cycle', $eleve->cycle) == 'college' ? 'selected' : '' }}>Collège</option>
                                    <option value="lycee" {{ old('cycle', $eleve->cycle) == 'lycee' ? 'selected' : '' }}>Lycée</option>
                                </select>
                                @error('cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="classe" class="form-label">Classe :</label>
                                <input type="text" class="form-control @error('classe') is-invalid @enderror" 
                                       id="classe" name="classe" placeholder="Ex: 6ème A, CM2 B, Tle S..." 
                                       value="{{ old('classe', $eleve->classe) }}" required>
                                @error('classe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: Niveau + Section (ex: 5ème B, Tle D)</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="login" class="form-label">Login :</label>
                                <input type="text" class="form-control @error('login') is-invalid @enderror" 
                                       id="login" name="login" placeholder="Entrer le login..." 
                                       value="{{ old('login', $eleve->user->login) }}" required>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mot de passe :</label>
                                <div class="position-relative">
                                    <input type="password"  id="motDePasse"class="form-control" 
                                           placeholder="Laisser vide pour ne pas modifier" 
                                           name="motDePasse">
                                           <small id="password-strength" class="form-text mt-1 text-muted"></small>

                          <i id="togglePassword" class="fas fa-eye password-toggle"></i>

                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 search-container">
                                <label for="parent_search" class="form-label">Rechercher un parent :</label>
                                <input type="text" class="form-control @error('parentt_id') is-invalid @enderror" 
                                       id="parent_search" placeholder="Rechercher par nom, prénom ou CIN..." 
                                       value="{{ old('parent_search', $eleve->parentt ? $eleve->parentt->user->nom.' '.$eleve->parentt->user->prenom : '') }}">
                                <input type="hidden" id="parentt_id" name="parentt_id" value="{{ old('parentt_id', $eleve->parentt_id) }}">
                                <div id="parent_results" class="list-group mt-2" style="display:none;"></div>
                                @error('parentt_id')
                                    <div class="invalid-feedback d-block">Veuillez sélectionner un parent valide</div>
                                @enderror
                                <small class="text-muted">Commencez à taper pour rechercher un parent</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('listerEleves.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const parentSearch = document.getElementById('parent_search');
    const parentIdInput = document.getElementById('parentt_id');
    const parentResults = document.getElementById('parent_results');
    const form = document.querySelector('form');

    // Fonction de recherche avec debounce
    const searchParents = debounce(function(query) {
        if (query.length < 2) {
            parentResults.style.display = 'none';
            parentIdInput.value = '';
            return;
        }

        fetch({{ route('search.parents') }}?query=${encodeURIComponent(query)})
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                parentResults.innerHTML = '';
                
                if (data.length === 0) {
                    const noResult = document.createElement('div');
                    noResult.className = 'list-group-item text-muted';
                    noResult.textContent = 'Aucun parent trouvé';
                    parentResults.appendChild(noResult);
                } else {
                    data.forEach(parent => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <strong>${parent.nom} ${parent.prenom}</strong><br>
                            <small>CIN: ${parent.cin} | Enfants: ${parent.classes_str}</small>
                        `;
                        item.addEventListener('click', function() {
                            parentSearch.value = ${parent.nom} ${parent.prenom};
                            parentIdInput.value = parent.id;
                            parentResults.style.display = 'none';
                            
                            // Supprimer les messages d'erreur existants
                            document.querySelectorAll('.parent-error').forEach(el => el.remove());
                            parentSearch.classList.remove('is-invalid');
                        });
                        parentResults.appendChild(item);
                    });
                }
                parentResults.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                parentResults.innerHTML = '';
                const errorItem = document.createElement('div');
                errorItem.className = 'list-group-item text-danger';
                errorItem.textContent = 'Erreur lors de la recherche';
                parentResults.appendChild(errorItem);
                parentResults.style.display = 'block';
            });
    }, 300);

    // Validation du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validation côté client
        if (!parentIdInput.value) {
            parentSearch.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = 'Veuillez sélectionner un parent valide';
            parentSearch.parentElement.appendChild(errorDiv);
            return;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = "{{ route('listerEleves.index') }}";
            } else {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        const input = form.querySelector([name="${field}"]);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = messages.join(', ');
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de l\'envoi du formulaire');
        }
    });

    // Événements
    parentSearch.addEventListener('input', function() {
        searchParents(this.value);
    });

    parentSearch.addEventListener('focus', function() {
        if (this.value.length > 1 && parentResults.innerHTML) {
            parentResults.style.display = 'block';
        }
    });

    document.addEventListener('click', function(e) {
        if (!parentSearch.contains(e.target) && !parentResults.contains(e.target)) {
            parentResults.style.display = 'none';
        }
    });

    // Fonction debounce pour limiter les requêtes
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
// Add this to your script section
document.getElementById('motDePasse').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('password-strength');
    
    if (!strengthText) return;
    
    const strength = {
        0: "Très faible",
        1: "Faible",
        2: "Moyen",
        3: "Fort",
        4: "Très fort"
    };
    
    let score = 0;
    if (password.length >= 8) score++;
    if (password.match(/[a-z]/)) score++;
    if (password.match(/[A-Z]/)) score++;
    if (password.match(/[0-9]/)) score++;
    if (password.match(/[^a-zA-Z0-9]/)) score++;
    
    strengthText.textContent = Force: ${strength[score]};
    strengthText.className = text-${['danger', 'danger', 'warning', 'success', 'success'][score]};
});
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
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
});
</script>
@endsection