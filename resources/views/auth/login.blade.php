<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Baraim Errazi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex">

    <!-- Partie image -->
    <div class="w-1/2 h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/Br.png') }}')">
    </div>

    <!-- Partie formulaire avec fond gris clair -->
    <div class="w-1/2 h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-sm text-center px-8">
            <!-- Logo -->
            <div class="mb-4">
                <img src="{{ asset('images/errazi.png') }}" alt="Logo Baraim Errazi" class="mx-auto h-32 w-auto rounded">
            </div>

            <h2 class="text-2xl font-bold mb-6 text-gray-800">Bienvenue !</h2>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 text-left p-3 rounded mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4 text-left">
                @csrf

                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2 bg-white">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5.121 17.804A9.936 9.936 0 0112 15c2.21 0 4.252.717 5.879 1.927M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <input type="text" name="login" id="login" required class="w-full bg-transparent focus:outline-none" placeholder="Nom d'utilisateur">
                    </div>
                </div>

                <div>
                    <label for="motDePasse" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2 bg-white">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 15v2m0-6v2m-6 2a6 6 0 1112 0v4H6v-4z"/>
                        </svg>
                        <input type="password" name="motDePasse" id="motDePasse" required class="w-full bg-transparent focus:outline-none" placeholder="Mot de passe">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2">
                    <label for="remember" class="text-sm text-gray-600">Se souvenir de moi</label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-full transition duration-300">
                    Se connecter
                </button>
            </form>
        </div>
    </div>

</body>
</html>
