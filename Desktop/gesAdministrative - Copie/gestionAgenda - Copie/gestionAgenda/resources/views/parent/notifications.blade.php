@extends('layouts.parent')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-center mb-4" style="font-size: 2.4rem; color: #0077C8; font-weight: bold;">
        Mes Notifications
    </h1>

    @if(session('success'))
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle" style="color: green;"></i> {{ session('success') }}
        </div>
    @endif

    @if ($notifications->isEmpty())
        <div class="text-center">
            <p class="text-muted">
                <i class="fas fa-info-circle" style="color: #0077C8;"></i> Aucune notification reçue.
            </p>
        </div>
    @else
        @foreach ($notifications as $notification)
            <div class="bg-white shadow-sm rounded p-4 mb-4 border-start border-4 
                @if(!$notification->vue) border-primary @else border-success @endif 
                {{ $notification->vue ? 'opacity-50' : '' }}">
                
                <p class="mb-1 text-dark">
                    <strong>
                        <i class="fas fa-user me-1" style="color: grey;"></i> De : 
                        {{ optional($notification->sender)->nom 
                            ? optional($notification->sender)->nom . ' ' . optional($notification->sender)->prenom 
                            : 'Système' }}
                    </strong>
                </p>

                <p class="mb-1 text-dark">
                    <i class="fas fa-comment-dots me-1" style="color: grey;"></i> {{ $notification->message }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fas fa-clock me-1" style="color: grey;"></i>
                    Reçu le {{ \Carbon\Carbon::parse($notification->date)->format('d/m/Y H:i') }}
                </p>

                <div class="d-flex justify-content-between">
                    @if (!$notification->vue)
                        <form action="{{ route('parent.notifications.markAsRead', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-check-circle me-1"></i> Marquer comme lu
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('parent.notifications.delete', $notification->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette notification ?')">
                            <i class="fas fa-trash-alt me-1" style="color: grey;"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
