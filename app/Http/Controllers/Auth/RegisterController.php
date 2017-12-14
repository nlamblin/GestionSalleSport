<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Connexion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'last-name'     => 'required|string|max:30',
            'first-name'    => 'required|string|max:30',
            'email'         => 'required|string|email|max:100|unique:connexion',
            'password'      => 'required|string|min:6|confirmed',
            'birth-date'    => 'required|date'
        ]);
    }

    /**
     * Create a new user and connexion instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'nom_utilisateur'        => $data['last-name'],
            'prenom_utilisateur'     => $data['first-name'],
            'date_naiss_utilisateur' => $data['birth-date'],
            'id_statut'              => 4
        ]);

        Connexion::create([
            'id_utilisateur'    => $user->id_utilisateur,
            'email'             => $data['email'],
            'password'          => bcrypt($data['password'])
        ]);

        return $user;
    }
}
