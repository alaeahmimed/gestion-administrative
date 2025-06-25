@extends('layouts.enseignant')

@section('content')

<style>
    .center-page {
        min-height: 80vh;
        margin-left: 240px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem;
        background: #f9f9f9;
    }

    .center-page .main-card {
        width: 100%;
        max-width: 700px;
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-primary, .btn-info, .btn-success, .btn-danger, .btn-secondary {
        color: #fff !important;
        border: none;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #0077C8;
    }

    .btn-primary:hover {
        background-color: #005fa3;
    }

    .btn-info {
        background-color: #005D91;
    }

    .btn-success {
        background-color: #43B047;
    }

    .btn-danger {
        background-color: #f44336;
    }

    .btn-danger:hover {
        background-color: #d32f2f;
    }

    .btn-secondary {
        background-color: grey;
    }

    .form-label {
        font-weight: bold;
        color: #6c757d;
    }

    .icon-grey {
        color: grey;
        margin-right: 5px;
    }

    @media (max-width: 768px) {
        .center-page {
            padding: 1rem;
            margin-left: 0;
        }
        .center-page .main-card {
            max-width: 90%;
        }
    }
</style>


<div class="container center-page">

    <h3 class="text-center mb-4" style="font-size: 2.4rem; color: #0077C8; font-weight: bold;">
         Détail de la Notification
    </h3>

    <div class="main-card">

        {{-- Détail de la notification --}}
        <div class="mb-4">
            <p class="mb-2">
                <i class="fas fa-user icon-grey"></i>
                <strong>De :</strong> {{ $from->prenom }} {{ $from->nom }}
                <span class="badge bg-primary ms-2">{{ ucfirst($from->role) }}</span>
            </p>
            <p>
                <i class="fas fa-comment-dots icon-grey"></i>
                <strong>Message :</strong> {{ $notification->message }}
            </p>
        </div>

        {{-- Liste des enfants --}}
        @if($from->role === 'parent' && count($enfants) > 0)
            <div class="mb-4">
                <p class="form-label">
                    <i class="fas fa-child icon-grey"></i> Ses enfants :
                </p>
                <ul class="list-group">
                    @foreach($enfants as $enfant)
                        <li class="list-group-item">
                            {{ $enfant->prenom }} {{ $enfant->nom }} — <strong>Classe :</strong> {{ $enfant->classe }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulaire de réponse --}}
        {{-- Formulaire de réponse --}}
@if($from->role === 'parent')
    <form action="{{ route('enseignant.notifications.respond', $notification) }}" method="POST" class="mb-4 mt-4">
        @csrf
        <label for="reponse" class="form-label">
            <i class="fas fa-reply icon-grey"></i> Réponse
        </label>
        <textarea name="reponse" id="reponse" class="form-control mb-3" rows="4" required></textarea>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-1" style="color: white;"></i> Envoyer la réponse
        </button>
    </form>
@endif

        {{-- Bouton de suppression --}}
        <form action="{{ route('enseignant.notifications.destroy', $notification) }}" method="POST"
              onsubmit="return confirm('Supprimer cette notification ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt me-1" style="color: white;"></i> Supprimer
            </button>
        </form>
    </div>
</div>

@endsection
