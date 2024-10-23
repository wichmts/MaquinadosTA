<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Sobrescribe el método username() para usar codigo_acceso en lugar de email
     *
     * @return string
     */
    public function username()
    {
        return 'codigo_acceso';
    }

    /**
     * Valida los datos del request antes de intentar autenticarse.
     *
     * @param Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'codigo_acceso' => 'required|string',
        ]);
    }

    /**
     * Intenta autenticar al usuario utilizando el campo codigo_acceso.
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // Autenticación personalizada sin contraseña
        $user = \App\User::where('codigo_acceso', $request->codigo_acceso)->first();

        if ($user) {
            // Autenticar al usuario manualmente
            Auth::login($user);
            return true;
        }

        return false;
    }

    /**
     * Si el usuario es autenticado, verifica si su cuenta está activa.
     *
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Si la cuenta no está activa, cierra la sesión
        if (!$user->active) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'Tu cuenta no está activa.');
        }

        // Redirige al usuario a la página de inicio
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Define la ruta de redirección después del login exitoso.
     *
     * @return string
     */
    public function redirectPath()
    {
        return '/home'; // Cambia '/home' por la ruta a donde quieras redirigir después del login
    }
}
