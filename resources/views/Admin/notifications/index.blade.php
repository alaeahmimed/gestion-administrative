@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">
        <i class="fas fa-bell me-2"></i> Mes Notifications
    </h2>

    @if($notifications->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Aucune notification disponible
        </div>
    @else
        @foreach($notifications as $notification)
            @php
                // Tentative de décodage du message
                $messageData = json_decode($notification->message, true);
                $isJson = (json_last_error() === JSON_ERROR_NONE);
                
                // Correction pour les anciennes notifications mal formatées
                if (!$isJson && str_contains($notification->message, '"type":"justification"')) {
                    $fixedJson = '{'.str_replace(['[', ']'], '', $notification->message).'}';
                    $messageData = json_decode($fixedJson, true);
                    $isJson = (json_last_error() === JSON_ERROR_NONE);
                }
                
                // Récupération de l'ID
                $justificationId = $isJson 
                    ? ($messageData['justification_id'] ?? null) 
                    : ($notification->pivot->related_id ?? null);
            @endphp

            <div class="card mb-3 shadow-sm {{ $notification->pivot->vue ? 'border-start border-secondary' : 'border-start border-primary' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                {{ $notification->sender ? $notification->sender->first_name . ' ' . $notification->sender->last_name : 'Système' }}
                            </h5>
                            <p class="card-text mb-1">
                                @if($isJson)
                                    {{ $messageData['text'] ?? $messageData['message'] ?? $notification->message }}
                                @else
                                    {{ $notification->message }}
                                @endif
                            </p>

                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $notification->date->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        @if(!$notification->pivot->vue)
                            <span class="badge bg-primary">Nouveau</span>
                        @endif
                    </div>

                    @if($notification->type === 'justification' && $justificationId)
                        <a href="{{ route('admin.notifications.show', $justificationId) }}" 
                           class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-eye me-1"></i> Voir détails
                        </a>
                    @endif

                    <div class="d-flex justify-content-end mt-3 gap-2">
                        @if(!$notification->pivot->vue)
                            <form action="#" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check-circle me-1"></i> Marquer comme lu
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Supprimer cette notification ?')">
                                <i class="fas fa-trash-alt me-1"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection