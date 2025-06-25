@extends('layouts.app')

@section('content')
<div class="container">
    
    <h2>Gestion des relevés de notes (Admin)</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <label>Cycle</label>
            <select class="form-control" id="cycle-select">
                <option value="">Sélectionnez un cycle</option>
                @foreach($cycles as $cycle)
                    <option value="{{ $cycle }}">{{ $cycle }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-4">
            <label>Classe</label>
            <select class="form-control" id="classe-select" disabled>
                <option value="">Sélectionnez d'abord un cycle</option>
            </select>
        </div>
        
        <div class="col-md-4">
            <label>Matière</label>
            <select class="form-control" id="matiere-select" disabled>
                <option value="">Sélectionnez d'abord une classe</option>
            </select>
        </div>
    </div>
    
    <div id="eleves-container" style="display: none;">
        <form method="POST" action="{{ route('admin.notes.save') }}">
            @csrf
            <input type="hidden" name="classe" id="input-classe">
            <input type="hidden" name="matiere" id="input-matiere">
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>CC1</th>
                        <th>CC2</th>
                        <th>CC3</th>
                        <th>Projet</th>
                    </tr>
                </thead>
                <tbody id="eleves-table-body">
                    <!-- Rempli dynamiquement -->
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Enregistrer les notes</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vos sélecteurs
    const cycleSelect = document.getElementById('cycle-select');
    const classeSelect = document.getElementById('classe-select');
    const matiereSelect = document.getElementById('matiere-select');
    const elevesContainer = document.getElementById('eleves-container');

    // Fonction pour gérer les erreurs
    const handleError = (error, element, message = 'Erreur de chargement') => {
        console.error(error);
        element.innerHTML = `<option value="">${message}</option>`;
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    };

    // Gestion du cycle
    cycleSelect.addEventListener('change', async function() {
        const cycle = this.value;
        classeSelect.innerHTML = '<option value="">Chargement...</option>';
        classeSelect.disabled = true;
        matiereSelect.innerHTML = '<option value="">Sélectionnez une classe</option>';
        matiereSelect.disabled = true;
        elevesContainer.style.display = 'none';

        if (!cycle) {
            classeSelect.innerHTML = '<option value="">Sélectionnez un cycle</option>';
            return;
        }

        try {
            const response = await fetch(`/admin/classes/${encodeURIComponent(cycle)}`);
            if (!response.ok) throw new Error('Erreur réseau');
            
            const data = await response.json();
            
            classeSelect.innerHTML = '<option value="">Sélectionnez une classe</option>';
            if (data?.length > 0) {
                data.forEach(classe => {
                    const option = new Option(classe, classe);
                    classeSelect.add(option);
                });
                classeSelect.disabled = false;
            } else {
                classeSelect.innerHTML = '<option value="">Aucune classe disponible</option>';
            }
        } catch (error) {
            handleError(error, classeSelect);
        }
    });

    // Gestion de la classe
   // Gestion de la classe
classeSelect.addEventListener('change', async function() {
    const classe = this.value.trim();
    console.log("Classe sélectionnée:", classe);

    matiereSelect.innerHTML = '<option value="">Chargement...</option>';
    matiereSelect.disabled = true;
    elevesContainer.style.display = 'none';
    document.getElementById('input-classe').value = classe;

    if (!classe) {
        matiereSelect.innerHTML = '<option value="">Sélectionnez une classe</option>';
        return;
    }

    const loadingToast = Toastify({
        text: "Chargement des matières...",
        duration: -1,
        close: false,
        gravity: "top",
        position: "right",
        backgroundColor: "linear-gradient(to right, #4b6cb7, #182848)",
    }).showToast();

    try {
        const response = await fetch(`/admin/matieres/${encodeURIComponent(classe)}`);
        
        if (!response.ok) {
            throw new Error(`Erreur ${response.status}`);
        }
        
        const data = await response.json();
        console.log("Matières reçues:", data);
        
        loadingToast.hideToast();
        
        // Réinitialisation du select
        matiereSelect.innerHTML = '<option value="">Sélectionnez une matière</option>';
        
        // Ajout des options seulement si data est un tableau non vide
        if (Array.isArray(data) && data.length > 0) {
            data.forEach(matiere => {
                if (matiere && matiere.trim() !== '') { // Vérification supplémentaire
                    const option = new Option(matiere, matiere);
                    matiereSelect.add(option);
                }
            });
            matiereSelect.disabled = false;
        } else {
            matiereSelect.innerHTML = '<option value="">Aucune matière disponible</option>';
            Toastify({
                text: "Aucune matière trouvée pour cette classe",
                duration: 3000,
                backgroundColor: "linear-gradient(to right, #f46b45, #eea849)",
            }).showToast();
        }
    } catch (error) {
        console.error("Erreur:", error);
        loadingToast.hideToast();
        matiereSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        Toastify({
            text: "Erreur lors du chargement des matières",
            duration: 3000,
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    }
});

    // Gestion de la matière
    matiereSelect.addEventListener('change', async function() {
        const classe = classeSelect.value;
        const matiere = this.value;
        document.getElementById('input-matiere').value = matiere;
        
        if (!classe || !matiere) {
            elevesContainer.style.display = 'none';
            return;
        }

        const loadingToast = Toastify({
            text: "Chargement des élèves...",
            duration: -1,
            close: false,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #4b6cb7, #182848)",
        }).showToast();

        try {
            const response = await fetch(`/admin/eleves/${encodeURIComponent(classe)}?matiere=${encodeURIComponent(matiere)}`);
            if (!response.ok) throw new Error('Erreur serveur: ' + response.status);
            
            const eleves = await response.json();
            loadingToast.hideToast();
            
            const tbody = document.getElementById('eleves-table-body');
            tbody.innerHTML = '';
            
            if (!Array.isArray(eleves)) {
                throw new Error('Format de données invalide');
            }
            
            if (eleves.length === 0) {
                Toastify({
                    text: "Aucun élève trouvé",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #f46b45, #eea849)",
                }).showToast();
                return;
            }
            
            eleves.forEach(eleve => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${eleve.nom}</td>
                    <td>${eleve.prenom}</td>
                    <td><input type="number" name="notes[${eleve.id}][cc1]" 
                          class="form-control" min="0" max="20" step="0.5"
                          value="${eleve.notes?.cc1 || ''}"></td>
                    <td><input type="number" name="notes[${eleve.id}][cc2]" 
                          class="form-control" min="0" max="20" step="0.5"
                          value="${eleve.notes?.cc2 || ''}"></td>
                    <td><input type="number" name="notes[${eleve.id}][cc3]" 
                          class="form-control" min="0" max="20" step="0.5"
                          value="${eleve.notes?.cc3 || ''}"></td>
                    <td><input type="number" name="notes[${eleve.id}][projet]" 
                          class="form-control" min="0" max="20" step="0.5"
                          value="${eleve.notes?.projet || ''}"></td>
                `;
                tbody.appendChild(tr);
            });
            
            elevesContainer.style.display = 'block';
            Toastify({
                text: `${eleves.length} élèves chargés`,
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            }).showToast();
        } catch (error) {
            loadingToast.hideToast();
            handleError(error, null, "Erreur de chargement des élèves");
        }
    });
});
</script>
@endsection