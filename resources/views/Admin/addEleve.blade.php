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
                <h1 class="h3 text-primary">Ajouter Élève</h1>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('addEleve.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom :</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" name="nom" placeholder="Entrer nom..." 
                                       value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom :</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                       id="prenom" name="prenom" placeholder="Entrer prénom..." 
                                       value="{{ old('prenom') }}" required>
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
                                    <option value="" disabled selected>Choisir un cycle...</option>
                                    <option value="Primaire" {{ old('cycle') == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                                    <option value="College" {{ old('cycle') == 'College' ? 'selected' : '' }}>Collège</option>
                                    <option value="Lycee" {{ old('cycle') == 'Lycee' ? 'selected' : '' }}>Lycée</option>
                                </select>
                                @error('cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="classe" class="form-label">Classe :</label>
                                <input type="text" class="form-control @error('classe') is-invalid @enderror" 
                                       id="classe" name="classe" placeholder="Ex: 6ème A, CM2 B, Tle S..." 
                                       value="{{ old('classe') }}" required>
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
                                       value="{{ old('login') }}" required>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="motDePasse" class="form-label">Mot de passe :</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control @error('motDePasse') is-invalid @enderror" 
                                           id="motDePasse" name="motDePasse" placeholder="Entrer le mot de passe..." required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                                    @error('motDePasse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 search-container">
                                <label for="parent_search" class="form-label">Rechercher un parent :</label>
                                <input type="text" class="form-control @error('parentt_id') is-invalid @enderror" 
                                       id="parent_search" placeholder="Rechercher par nom, prénom ou CIN..." 
                                       value="{{ old('parent_search') }}" required>
                                <input type="hidden" id="parentt_id" name="parentt_id" value="{{ old('parentt_id') }}">
                                <div id="parent_results" class="list-group mt-2" style="display:none;"></div>
                                @error('parentt_id')
                                    <div class="invalid-feedback d-block">Veuillez sélectionner un parent valide</div>
                                @enderror
                                <small class="text-muted">Commencez à taper pour rechercher un parent</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('listerEleves.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
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

        fetch(`{{ route('search.parents') }}?query=${encodeURIComponent(query)}`)
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
                     // Dans votre event listener pour la sélection d'un parent
                        item.addEventListener('click', function() {
                            parentSearch.value = `${parent.nom} ${parent.prenom}`;
                            parentIdInput.value = parent.id;
                            console.log('Parent sélectionné:', {id: parent.id, nom: parent.nom}); // Vérifiez dans la console
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
   // Validation avant soumission
// Add this to your form validation
// Modifiez la partie de validation du formulaire
// Ajoutez ceci dans votre script
// Remplacez l'écouteur d'événement submit par ceci
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

    // Envoi des données via Fetch API pour mieux capturer les erreurs
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
            // Afficher les erreurs de validation
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = form.querySelector(`[name="${field}"]`);
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

    // Fonction pour basculer la visibilité du mot de passe
    window.togglePassword = function() {
        const passwordInput = document.getElementById('motDePasse');
        const toggleIcon = document.querySelector('.password-toggle');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    };

    document.querySelector('form').addEventListener('submit', function(e) {
    const parentId = document.getElementById('parentt_id').value;
    if (!parentId) {
        e.preventDefault();
        alert('Veuillez sélectionner un parent valide');
        return false;
    }
    return true;
});
});

</script>
@endsection