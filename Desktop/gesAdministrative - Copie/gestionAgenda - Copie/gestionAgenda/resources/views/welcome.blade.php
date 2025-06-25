<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baraim Errazi - Gestion Scolaire</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: url('{{ asset('images/OIP.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.55);
            padding: 40px;
            border-radius: 20px;
            max-width: 650px;
            width: 90%;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            animation: fadeIn 1.2s ease;
        }

        img.logo {
            width: 120px;
            margin-bottom: 20px;
            animation: zoomIn 1.2s ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        a.button {
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            background-color: #ffffff;
            color: #007BFF;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        a.button:hover {
            background-color: #e0e0e0;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 600px) {
            h1 { font-size: 2rem; }
            p { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <img src="{{ asset('images/errazi.png') }}" alt="Logo Baraim Errazi" class="logo">
        <h1>Bienvenue sur Baraim Errazi</h1>
        <p>Solution complète de gestion scolaire conçue pour les administrateurs, enseignants et parents.</p>
        <a href="{{ route('login') }}" class="button">Se connecter</a>
    </div>
</body>
</html>
