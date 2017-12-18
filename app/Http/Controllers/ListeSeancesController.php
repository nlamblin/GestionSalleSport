<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seance;
use App\Models\Activite;

class ListeSeancesController extends Controller
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

        return view('listeSeances', [
            'id'    => $request->id_activite,
            'listeSeances' => $listeSeances
        ]);
    }

}