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
use DateTime;

class ReservationController extends Controller
{

    /**
     * Méthode qui enregistre la reservation en base de données
     *
     * @param Request $request
     * @return mixed
     */
    public function effectuerReservation(Request $request) {

        if($request->idUtilisateur == null){
            #Si c'est un client qui fait sa propre reservation 
            $idutilisateur = Auth::user()->id_utilisateur;
        }
        else{
            $idutilisateur = $request->idUtilisateur;
        }

        #Dans le cas d'un client, on fait la reservation en son nom
        $message = null;
        // récupération de la séance
        $seance = Seance::find($request->idSeance);

        //Dans le cas où c'est une reservation par un employé, on va verifier que le client choisit ne possède pas deja de reservation sur la seance choisie.
        if($request->idUtilisateur != null){
            $reservation = ReservationInterne::select()
            ->where('id_utilisateur','=',$idutilisateur)
            ->where('id_seance','=',$seance->id_seance)
            ->where('etat_reservation','=','reservee')
            ->get();

            if(sizeof($reservation)>0){
                //S'il on a trouvé une reservation, c'est que le client est deja inscrit
                $message = "Réservation impossible : Le client est deja inscrit à cette séance. Veuillez choisir une autre séance. ";
                return $message;
            }
        }
    

        // on fait +1 pour ne pas oublier la personne qui fait la reservation (qui n'est pas compté comme une personne ajoutée)
        if($seance->places_restantes >= sizeof($request->personnesAAjouter) + 1) {

            // on reserve pour la personne connectée
            ReservationInterne::create([
                'etat_reservation'  => 'reservee',
                'id_utilisateur'    => $idutilisateur,
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

            //On récupère l'utilisateur pour qui la reservation est faite
            $user = User::select()
            ->where('id_utilisateur','=',$idutilisateur)
            ->first();

            // si il n'est pas valide c'est paiement à l'unité donc on lui créé une carte avec 1 seance dispo
            if(!$user->estValide()) {
                Carte::create([
                    'seance_dispo'   => 1,
                    'active'         => true,
                    'id_utilisateur' => $user->id_utilisateur
                ]);
            }

            // on récupère si l'abonné a une carte
            $carteActive = Carte::where([
                ['id_utilisateur', '=', $user->id_utilisateur],
                ['active', '=', true]
            ])->first();

            // si il a une carte on lui retire une séance
            if (sizeof($carteActive) > 0) {
                Carte::where('id_carte', '=', $carteActive->id_carte)
                    ->update(['seance_dispo' => $carteActive->seance_dispo - 1]);
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


        //Pour annuler la reservation courante
        ReservationInterne::where('id_reservation', $reservationInterne->id_reservation)
                ->update(['etat_reservation' => 'annulee']);
         
        
        //On récupère la séance de la reservation
                $seance = Seance::select()
        ->where('id_seance','=',$reservationInterne->id_seance)
        ->first();

        $date_seance = strtotime($seance->date_seance);
        $heure_seance = strtotime($seance->heure_seance);
        
        $now = time();
        $diff =$date_seance - $now;

        $jours=floor($diff/86400);
        $heures=floor(($diff%86400)/3600);

        if(!($jours>=2 & $heures>=0)){
            $message = 'Votre annulation a bien été prise en compte. Aucun remboursement n\'est possible puisque que la séance a lieu dans moins de 48h';
        }
        else{

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
                    
                    $message = 'Votre annulation a bien était prise en compte. Vous avez été remboursé sur votre carte';

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

                    $message = 'Votre annulation a bien était prise en compte. Nous avons réactivé une ancienne de vos cartes pour vous rembourser';
                }

            } 
        }

     return($message);
    }

}
