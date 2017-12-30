<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Carte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Compte extends Controller
{

    public function index() {
        $user = User::getUser(Auth::user()->id_utilisateur);
        $email = $user->getEmail();

        $data = [
            'user'      => $user,
            'email'     => $email->email
        ];

        $abonnementActif = Abonnement::where([
                ['id_utilisateur', '=', $user->id_utilisateur],
                ['date_fin_abo', '>', date('Y-m-d', time())],
            ])->first();

        if(sizeof($abonnementActif) > 0) {
            $data['abonnement'] = $abonnementActif;
        }
        else {
            $carteActive = Carte::where([
                ['id_utilisateur', '=', $user->id_utilisateur],
                ['active', '=', true]
            ])->first();

            if(sizeof($carteActive) > 0) {
                $data['carte'] = $carteActive;
            }
        }

        return view('compte', $data);
    }

}
