<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Seance;
use App\Models\Activite;
use Illuminate\Support\Facades\Auth;

class SeancesController extends Controller
{

    public function index()
    {
    	$listeActivites = Activite::get();

        return view('seances', [
            'listeActivites'    => $listeActivites
        ]);
    }

    /**
     * Méthode qui affiche les seances disponibles en fonction d'une activité
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seancesParActivites(Request $request) {

        $listeSeances = Seance::where('places_restantes', '>' ,0)
                                ->where('id_activite', $request->id_activite)
                                ->get();

        $user = User::getUser(Auth::user()->id_utilisateur);

        return view('listeSeances', [
            'listeSeances'          => $listeSeances,
            'utilisateurValide'     => true
            // 'utilisateurValide'     => $user->estValide()
            // 'utilisateursValides'   => User::getUtilisateursValides()
        ]);
    }

}