<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Barain ER-Razzi - Enseignant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #3498db; /* Bleu */
            --secondary-color: #3498db;
            --accent-color: #3498db;
            --sidebar-width: 280px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            overflow-x: hidden;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 80px; /* Espace pour le bouton de déconnexion */
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .logo-img {
            width: 140px;
            height: auto;
            transition: all 0.3s;
        }
        
        .nav-item {
            margin: 0.25rem 0.5rem;
        }
        
        .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        
        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            color: var(--primary-color);
        }
        
        .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color) !important;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2);
        }

        .nav-link.active i {
            color: white !important;
        }

        .nav-link:active, 
        .nav-link:focus {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        .nav-link:active i, 
        .nav-link:focus i {
            color: white !important;
        }
        
        main {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            background-color: white;
            border-radius: 12px 0 0 0;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.02);
        }
        
        .notification-badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
            top: -5px;
            right: -5px;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .user-profile:hover {
            background-color: var(--medium-gray);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 0.75rem;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            position: absolute;
            bottom: 0;
            width: 100%;
            background: white;
        }
        
        .logout-link {
            color: var(--accent-color) !important;
            display: block;
            width: 100%;
        }
        
        .logout-link:hover {
            background-color: rgba(231, 76, 60, 0.1) !important;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="d-flex bg-light">

    <!-- Sidebar -->
    <aside class="sidebar p-0">
        <div class="sidebar-content">
            <div class="sidebar-header text-center">
                <div class="fw-bold text-dark fs-5 mb-3">Baraim ER-Razzi</div>
                <img src="{{ asset('images/errazi.jpg') }}" alt="Logo" class="logo-img mx-auto" />
            </div>
            
            <ul>
                <li class="nav-item">
                    <a href="{{ route('enseignant.dashboard') }}" 
                       class="nav-link text-decoration-none {{ request()->routeIs('enseignant.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('enseignant.absences.index') }}" 
                       class="nav-link text-decoration-none {{ request()->routeIs('enseignant.absences.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Absences
                    </a>
                </li>
                
                    <li class="nav-item">
    <a href="{{ route('enseignant.emploi.index') }}" 
       class="nav-link text-decoration-none {{ request()->routeIs('emploi.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>Emploi du temps
    </a>
</li>
                <li class="nav-item">
                    <a href="{{ route('enseignant.evenements.index') }}" 
                       class="nav-link text-decoration-none {{ request()->routeIs('enseignant.evenements.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i> Evenements
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('enseignant.devoirs.devoir') }}" 
                       class="nav-link text-decoration-none {{ request()->routeIs('enseignant.devoirs.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i> Devoirs
                    </a>
                </li>
                
               <li class="nav-item">
    <a href="{{ route('enseignant.notes') }}" 
       class="nav-link text-decoration-none {{ request()->routeIs('notes.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>Releve de notes
    </a>
</li>
  
            </ul>
        </div>

        <div class="sidebar-footer">
            <a href="#" class="nav-link logout-link text-decoration-none"
               onclick="event.preventDefault(); if(confirm('Voulez-vous vraiment vous déconnecter ?')) document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Se Déconnecter
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </aside>
    
    <!-- Main content -->
    <main>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">@yield('title')</h2>
            
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('enseignant.notifications.index') }}" class="position-relative text-decoration-none">
                    <i class="fas fa-bell text-secondary fs-5"></i>
                    
                    @php
                        $notificationsNonLues = DB::table('notification_user')
                            ->join('notifications', 'notifications.id', '=', 'notification_user.notification_id')
                            ->where('notification_user.user_id', auth()->id())
                            ->where('notification_user.vue', false)
                            ->where('notifications.receiver_id', auth()->id())
                            ->count();
                    @endphp
                    
                    @if($notificationsNonLues > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                            {{ $notificationsNonLues }}
                        </span>
                    @endif
                </a>
                
                <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        
        @yield('content')
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>
</html>