@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2><i class="fas fa-file-alt me-2"></i> Détail de la justification</h2>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="card-title">Parent : {{ $justification->parentt->user->first_name }} {{ $justification->parentt->user->last_name }}</h5>
            <p><strong>Élève :</strong> {{ $justification->absence->eleve->first_name }} {{ $justification->absence->eleve->last_name }}</p>
            <p><strong>Date de l'absence :</strong> {{ $justification->absence->date }}</p>
            <p><strong>Raison :</strong> {{ $justification->raison }}</p>

            @if ($justification->fichier)
    <p><strong>Fichier :</strong> 
        <a href="{{ route('admin.notifications.download', $justification->id) }}" target="_blank">
            <i class="fas fa-download me-1"></i> Voir le fichier
        </a>
    </p>
@endif


            <p><strong>Statut actuel :</strong> 
    <span class="badge bg-{{ $justification->absence->status === 'justifiee' ? 'success' : ($justification->absence->status === 'non justifiee' ? 'danger' : 'secondary') }}">
        {{ ucfirst($justification->absence->status ?? 'en attente') }}
    </span>
</p>


            <div class="d-flex gap-3 mt-4">
                <form method="POST" action="{{ route('admin.notifications.accepter', $justification->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Accepter
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.notifications.refuser', $justification->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Refuser
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
