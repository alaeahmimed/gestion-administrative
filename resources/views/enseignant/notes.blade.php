@extends('layouts.enseignant')

@section('content')
<div class="container">
    <h2>Gestion des relevés de notes </h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <label>Classe</label>
            <select class="form-control" id="classe-select">
                <option value="">Sélectionnez une classe</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe }}">{{ $classe }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="eleves-container" style="display: none;">
        <form method="POST" action="{{ route('enseignant.notes.save') }}">
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
    // Ajoutez cette partie au début de votre script
const form = document.querySelector('form');
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';

    // Créez un FormData au lieu d'envoyer du JSON
    const formData = new FormData(this);
    const notesData = {};

    // Ajoutez les notes au FormData
    document.querySelectorAll('tr').forEach((tr, index) => {
        if (index > 0) { // Skip l'en-tête
            const inputs = tr.querySelectorAll('input[type="number"]');
            const eleveId = inputs[0].name.match(/\[(\d+)\]/)[1];
            notesData[eleveId] = {
                cc1: inputs[0].value,
                cc2: inputs[1].value,
                cc3: inputs[2].value,
                projet: inputs[3].value
            };
        }
    });

    // Ajoutez les notes au FormData
    formData.append('notes', JSON.stringify(notesData));

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },

            body: JSON.stringify({
                classe: document.getElementById('input-classe').value,
                matiere: document.getElementById('input-matiere').value,
                notes: Object.fromEntries(
                    Array.from(document.querySelectorAll('tr')).slice(1).map(tr => {
                        const id = tr.querySelector('input').name.match(/\[(\d+)\]/)[1];
                        return [
                            id,
                            {
                                cc1: tr.querySelector('input[name$="[cc1]"]').value,
                                cc2: tr.querySelector('input[name$="[cc2]"]').value,
                                cc3: tr.querySelector('input[name$="[cc3]"]').value,
                                projet: tr.querySelector('input[name$="[projet]"]').value
                            }
                        ];
                    })
                )
            })
        });

        const result = await response.json();
        
        if (response.ok) {
            Toastify({
                text: result.message || 'Notes enregistrées avec succès',
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            }).showToast();
        } else {
            throw new Error(result.message || 'Erreur lors de la sauvegarde');
        }
    } catch (error) {
        Toastify({
            text: error.message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Enregistrer les notes';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const classeSelect = document.getElementById('classe-select');
    const elevesContainer = document.getElementById('eleves-container');

    // Initialisation de la matière une seule fois ici
    const matiere = "{{ auth()->user()->enseignant->matiere ?? '' }}";
    document.getElementById('input-matiere').value = matiere;

    if (!matiere) {
        alert("Matière non définie pour cet enseignant");
    }

    const handleError = (error, element = null, message = 'Erreur de chargement') => {
        console.error(error);
        if (element) {
            element.innerHTML = `<option value="">${message}</option>`;
        }
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    };

  classeSelect.addEventListener('change', async function () {
    const classe = this.value.trim();
    console.log("Classe sélectionnée (avant encodage):", classe);
    
    try {
        const url = `/enseignant/eleves/${encodeURIComponent(classe)}?matiere=${encodeURIComponent(matiere)}`;
        console.log("URL complète:", url);
        
        const response = await fetch(url);
        console.log("Statut de la réponse:", response.status);
        
        const data = await response.json();
        console.log("Données brutes reçues:", data);

        if (data.error) {
            throw new Error(data.error);
        }

        if (!Array.isArray(data)) {
            throw new Error("Format de données invalide");
        }

        const tbody = document.getElementById('eleves-table-body');
        tbody.innerHTML = '';

        if (data.length === 0) {
            throw new Error("Aucun élève trouvé pour cette classe");
        }

        data.forEach(eleve => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${eleve.nom}</td>
                <td>${eleve.prenom}</td>
                <td><input type="number" name="notes[${eleve.id}][cc1]" 
                      value="${eleve.notes?.cc1 || ''}" class="form-control"></td>
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
        loadingToast.hideToast();

    } catch (error) {
        console.error("Erreur complète:", error);
        loadingToast.hideToast();
        alert("Erreur: " + error.message);
    }
});
});
</script>
@endsection
