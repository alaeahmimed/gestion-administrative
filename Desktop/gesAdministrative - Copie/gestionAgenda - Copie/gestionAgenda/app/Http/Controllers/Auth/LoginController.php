<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'login' => 'required|string',
        'motDePasse' => 'required|string',
    ]);

    $user = \App\Models\User::where('login', $credentials['login'])->first();

    if ($user && Hash::check($credentials['motDePasse'], $user->motDePasse)) {
        $remember = $request->has('remember'); // ➕ récupère la checkbox

        Auth::login($user, $remember); // ➕ applique le "remember me"

        // Redirection selon le rôle
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'enseignant':
                return redirect()->route('enseignant.dashboard');
            case 'parent':
                return redirect()->route('parent.dashboard');
            case 'eleve':
                return redirect()->route('eleve.dashboard');
            default:
                return redirect('/');
        }
    }

    return back()->withErrors([
        'login' => 'Login ou mot de passe incorrect.',
    ]);
}



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
