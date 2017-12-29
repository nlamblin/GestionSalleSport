<?php

namespace App\Http\Controllers;

// use App\Models\ReservationInterne;
use App\Models\ReservationInterne;
use App\Models\Seance;
use App\Models\User;
use App\Models\Abonnement;
use App\Models\Carte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ReservationInterne::create([
                'etat_reservation'  => 'reservee',
                'id_utilisateur'    => Auth::user()->id_utilisateur,
                'id_seance'         => $seance->id_seance
            ]);

            if(sizeof($request->personneAAjouter) > 0) {
                // on reserve pour toutes les personnes ajoutées
                foreach ($request->personnesAAjouter as $idPersonneAAjouter) {
                    ReservationInterne::create([
                        'etat_reservation' => 'reservee',
                        'id_utilisateur' => $idPersonneAAjouter,
                        'id_seance' => $seance->id_seance
                    ]);
                }
            }

            // on assigne le coach à la séance
            if ($request->idCoach !== null) {
                Seance::where('id_seance', $seance->id_seance)
                    ->update(['id_coach' => $request->idCoach]);
            }

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

        $message = '';
        $reservationInterne = ReservationInterne::find($request->id_reservation);

        $id = $reservationInterne->id_utilisateur;


        #Pour annuler la reservation courante
        /*
         ReservationInterne::where('id_reservation', $reservationInterne->id_reservation)
                ->update(['etat_reservation' => 'annulee']);
        */ 

        /*On récupère les abos valides de cette personne*/
        $aboValideUser = User::select()
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where([
                ['abonnement.date_fin_abo', '>', date('Y-m-d', time())],
                ['utilisateur.id_utilisateur','=',$id],
                ])
            ->distinct()
            ->get();

        $nbAboValide = sizeof($aboValideUser);
        #S'il a au moins un abo valide
        
        if ($nbAboValide > 0) {
                $message = 'Votre annulation a bien était prise en compte. Aucun remboursement puisque vous utilisez un abonnement';
            }
        else {
            #S'il n'a pas d'abonnement valide
            $carteValideUser = User::select()
                ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
                ->where([
                    ['carte.active', '=', true],
                    ['utilisateur.id_utilisateur','=',$id],
                    ])
                ->distinct()
                ->first();  

            $nbCarteValide = sizeof($carteValideUser);
            if ($nbCarteValide > 0) {

                //On récupère l'id de la carte qui est valide
                $carte = $carteValideUser->id_carte;
                //Les séances restantes sur la carte
                $places = $carteValideUser->seance_dispo;

                Carte::where('id_carte', $carte)
                    ->update(['seance_dispo' => $places + 1]);
                
                $message = 'Votre annulation a bien était prise en compte. 
                Vous avez été remboursé sur votre carte';

            }   
            else {
                #Pas de carte valide, on récupère une de ses cartes invalides
                $carteInvalideUser = User::select()
                ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
                ->where([
                    ['carte.active', '=', false],
                    ['utilisateur.id_utilisateur','=',$id],
                    ])
                ->distinct()
                ->first();   

                //On récupère l'id de la première carte invalide
                $carte = $carteInvalideUser->id_carte;
                
                Carte::where('id_carte', $carte)
                    ->update(['seance_dispo' => 1]);

                $message = 'Votre annulation a bien était prise en compte. 
                Nous avons réactivé une ancienne de vos cartes pour vous rembourser';
            }

        } 
        

     return($message);
    }

}
