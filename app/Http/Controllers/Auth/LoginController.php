<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Connexion;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

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
     * Méthode appelée lors de l'authentification
     *
     * @param Request $request
     * @param Connexion $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, Connexion $user){
        User::where('id_utilisateur', '=', $user->id_utilisateur)
            ->update(['date_derniere_activite' => date('Y-m-d', time())]);

        User::where('id_utilisateur', '=', $user->id_utilisateur)
            ->update(['actif' => true]);
            
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Deconnexion
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }
}