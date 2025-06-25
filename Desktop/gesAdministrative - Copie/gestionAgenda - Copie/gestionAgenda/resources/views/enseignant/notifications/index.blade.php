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
        background-color: #f4f6f9;
    }

    .notification-card {
        width: 100%;
        max-width: 800px;
        min-width: 700px;
        background-color: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease-in-out;
    }

    .notification-card:hover {
        transform: translateY(-4px);
    }

    .section-title {
        font-size: 2.4rem;
        font-weight: bold;
        color: #0077C8;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .alert {
        max-width: 650px;
        width: 100%;
    }

    @media (max-width: 768px) {
        .center-page {
            padding: 1rem;
            margin-left: 0;
        }

        .notification-card, .alert {
            max-width: 90%;
        }
    }
    .btn-outline-info {
    color: #0077C8;
    border-color: #0077C8;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-outline-info:hover {
    background-color: #0077C8;
    color: white;
    border-color: #0077C8;
}

</style>

<div class="center-page">
    <h1 class="section-title">
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
                <i class="fas fa-info-circle" style="color: #0077C8;"></i> Aucune notification pour le moment.
            </p>
        </div>
    @else
        @foreach ($notifications as $notification)
            @php
                $fromUser = \App\Models\User::find($notification->sender_id);
            @endphp

            <div class="notification-card border-start border-4 
                @if(!$notification->vue) border-primary @else border-success opacity-50 @endif">
                
                <p class="mb-2 text-dark">
                    <strong>
                        <i class="fas fa-user me-1" style="color: grey;"></i>
                        De : {{ $fromUser?->prenom }} {{ $fromUser?->nom ?? 'Inconnu' }}
                    </strong>
                </p>

                <p class="mb-2 text-dark">
                    <i class="fas fa-comment-dots me-1" style="color: grey;"></i>
                    {{ Str::limit($notification->message, 150) }}
                </p>

                <p class="text-muted mb-3">
                    <i class="fas fa-clock me-1" style="color: grey;"></i>
                    Reçu le {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                </p>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('enseignant.notifications.show', $notification->id) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-1"></i> Voir le message
                    </a>
                </div>
            </div>
        @endforeach
    @endif
</div>

@endsection

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>
