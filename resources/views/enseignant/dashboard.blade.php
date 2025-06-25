@extends('layouts.enseignant')

@section('content')
<style>
    
    .main-content {
        padding: 3rem 2rem;
        text-align: center;
        margin-left:240px;
        border-radius: 1rem;
        min-width: 800px;
        margin: 0 auto;
        animation: fadeIn 0.8s ease-in-out;
    }
    .welcome-card{
        background-color:while;
        padding: 3rem 2rem;
        text-align: center;
        margin-left:240px;
        box-shadow: 0 0.75rem 1.25rem rgba(0, 0, 0, 0.1);
        border-radius: 1rem;
        min-width: 800px;
        margin: 0 auto;
        animation: fadeIn 0.8s ease-in-out;
    }
    .welcome-title {
        font-weight: 700;
        font-size: 2rem;
        color: #0077C8;
        margin-bottom: 1rem;
    }

    .welcome-message {
        font-size: 1.125rem;
        color: #4b5563;
    }

    .user-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
        color: #0077C8;
        animation: zoomIn 0.6s ease forwards;
        transform: scale(0);
    }

    @keyframes zoomIn {
        to {
            transform: scale(1);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="main-content">
    <div class="container py-5">
        <div class="welcome-card">
            <!-- Icône avec animation zoom -->
            <svg xmlns="http://www.w3.org/2000/svg" class="user-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM12 5a3 3 0 110 6 3 3 0 010-6zm0 14c-2.5 0-4.71-1.28-6-3.22.03-2 4-3.1 6-3.1s5.97 1.1 6 3.1C16.71 17.72 14.5 19 12 19z"/>
            </svg>

            <h1 class="welcome-title">
                Bienvenue, {{ auth()->user()->prenom }} {{ auth()->user()->nom }} !
            </h1>

            <p class="welcome-message">
                Vous êtes connecté à votre espace
            </p>
        </div>
    </div>
</div>
@endsection
