<?php

namespace App\Http\Controllers;

use App\Models\ReservationInterne;
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



        return view('listeSeances', [
            'idActivites'           => $request->id_activite,
            'listeSeances'          => $listeSeances
            // 'utilisateursValides'   => User::getUtilisateursValides()
        ]);
    }

    /**
     * Méthode qui enregistre la reservation en base de données
     *
     * @param Request $request
     * @return mixed
     */
    public function effectuerReservation(Request $request) {

        $message = null;
        // récupération de la séance
        $seance = Seance::find($request->idSeance);

        // on fait +1 pour ne pas oublier la personne qui fait la reservation (qui n'est pas compté comme une personne ajoutée)
        if($seance->places_restantes >= sizeof($request->personnesAAjouter) + 1) {

            // on reserve pour la personne connectée
            ReservationInterne::create([
                'etat_res'       => 'reservee',
                'id_utilisateur' => Auth::user()->id_utilisateur,
                'id_seance'      => $seance->id_seance
            ]);

            // on reserve pour toutes les personnes ajoutées
            foreach ($request->personnesAAjouter as $personneAAjouter) {
                ReservationInterne::create([
                    'etat_res'       => 'reservee',
                    'id_utilisateur' => $personneAAjouter,
                    'id_seance'      => $seance->id_seance
                ]);
            }

            $message = "Votre réservation a bien été prise en compte.";
        }
        else {
            $message = "Votre réservation n'a pas été prise en compte. La séance n'a plus assez de place libre.";
        }

        return $message;
    }

}