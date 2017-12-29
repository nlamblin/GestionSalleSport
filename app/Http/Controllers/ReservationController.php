<?php

namespace App\Http\Controllers;

// use App\Models\ReservationInterne;
use App\Models\ReservationInterne;
use App\Models\Seance;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

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
            /*ReservationInterne::create([
                'etat_res'       => 'reservee',
                'id_utilisateur' => Auth::user()->id_utilisateur,
                'id_seance'      => $seance->id_seance
            ]);

            // on reserve pour toutes les personnes ajoutées
            foreach ($request->personnesAAjouter as $idPersonneAAjouter) {
                ReservationInterne::create([
                    'etat_res'       => 'reservee',
                    'id_utilisateur' => $idPersonneAAjouter,
                    'id_seance'      => $seance->id_seance
                ]);
            }

            /*
            // on assigne le coach à la séance
            if ($request->idCoach !== null) {
                Seance::where('id_seance', $seance->id_seance)
                    ->update('id_coach', $request->idCoach);
            }
            */

            $message = "Votre réservation a bien été prise en compte.";
        }
        else {
            $message = "Votre réservation n'a pas été prise en compte. La séance n'a plus assez de place libre.";
        }

        return $message;
    }

    /**
     * Méthode qui annule la reservation d'une séance
     *
     * @param Request $request
     * @return string le message
     */
    public function annulerReservation(Request $request) {

         $reservationInterne = ReservationInterne::find($request->id_reservation);
         dd($reservationInterne);

         return ('Annulation prise en compte');
    }

}
