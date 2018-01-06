<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesSeancesController extends Controller
{

    /**
     * Récupère les séances archivées de l'utilisateur connecté
     *
     * @param Request $request
     * @return array
     */
    public function seancesPassees(Request $request) {
       
        $utilisateur = User::getUser(Auth::user()->id_utilisateur);

        return view('seancesPassees', [
            'reservationPassees' => $utilisateur->getSeancesPassees()
        ]);
    }

    /**
     * Récupère les séances auxquelles est inscrit l'utilisateur connecté
     *
     * @param Request $request
     * @return array
     */
    public function seancesVenir(Request $request) {

        $utilisateur = User::getUser(Auth::user()->id_utilisateur);

        $seancesVenir = $utilisateur->getSeancesAVenir();

        if($utilisateur->estClient()) {
            $blade = 'seancesVenirClient';
        }
        else if($utilisateur->estCoach()) {
            $blade = 'seancesVenirCoach';
        }

        return view($blade, [
            'utilisateur'   => $utilisateur,
            'seancesVenir'  => $seancesVenir
        ]);
    }

}
