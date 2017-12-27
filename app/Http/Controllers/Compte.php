<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Compte extends Controller
{

    public function index() {
        $user = User::getUser(Auth::user()->id_utilisateur);
        $email = $user->getEmail();

        return view('compte', [
            'user'  => $user,
            'email' => $email->email
        ]);
    }

}
