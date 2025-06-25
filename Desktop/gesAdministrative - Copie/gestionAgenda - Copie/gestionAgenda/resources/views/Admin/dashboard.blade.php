@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --success-color: #4cc9f0;
        --warning-color: #f72585;
    }

   .main-content {
   
    padding: 2.5rem;
    min-height: 100vh;
    background-color: #f8fafc;

    display: flex;
    align-items: center;
    justify-content: center;
}


    .welcome-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .welcome-card {
        background: white;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.03);
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    }

    .welcome-title {
        font-weight: 700;
        font-size: 2.25rem;
        color: var(--dark-color);
        margin-bottom: 1rem;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }

    .welcome-message {
        font-size: 1.125rem;
        color: #64748b;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .user-icon {
        width: 100px;
        height: 100px;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        border-radius: 50%;
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        animation: bounceIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .stats-card {
        background-color: #ffffff;
        padding: 2rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.03);
        position: relative;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    }

    .stats-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .stats-card .label {
        color: #64748b;
        font-size: 1rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3) translateY(20px);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.95);
        }
        100% {
            transform: scale(1) translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            padding: 1.5rem;
        }

        .welcome-card {
            padding: 2rem 1.5rem;
        }

        .stats-container {
            grid-template-columns: 1fr;
        }
    }


    .icon {
    background: var(--light-color);
    width: 50px;
    height: 50px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.icon-svg {
    width: 28px;
    height: 28px;
    color: var(--primary-color);
}

</style>

<div class="main-content">
    <div class="welcome-container">
        <div class="welcome-card">
            <!-- Icône avec animation -->
            <svg xmlns="http://www.w3.org/2000/svg" class="user-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM12 5a3 3 0 110 6 3 3 0 010-6zm0 14c-2.5 0-4.71-1.28-6-3.22.03-2 4-3.1 6-3.1s5.97 1.1 6 3.1C16.71 17.72 14.5 19 12 19z"/>
            </svg>

            <h1 class="welcome-title">
                Bienvenue, {{ auth()->user()->prenom }} {{ auth()->user()->nom }} !
            </h1>

            <p class="welcome-message">
                Vous êtes connecté à votre espace d'administration. Gérez facilement les élèves, enseignants et parents de votre établissement.
            </p>
        </div>

        <div class="stats-container">
    <div class="stats-card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m0-5a4 4 0 110-8 4 4 0 010 8zm6 4a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
        </div>
        <div class="number">{{ $totalEleves }}</div>
        <div class="label">Élèves inscrits</div>
    </div>

    <div class="stats-card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.042 12.042 0 0121 21.5M12 14L5.84 10.578A12.042 12.042 0 003 21.5" />
            </svg>
        </div>
        <div class="number">{{ $totalEnseignants }}</div>
        <div class="label">Enseignants</div>
    </div>

    <div class="stats-card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M12 14v6m-7-6v6m1-8a4 4 0 110-8 4 4 0 010 8z" />
            </svg>
        </div>
        <div class="number">{{ $totalParents }}</div>
        <div class="label">Parents</div>
    </div>
</div>

    </div>
</div>
@endsection