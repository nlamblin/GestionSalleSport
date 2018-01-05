<?php

namespace App\Http\Controllers;

use App\Models\ReservationInterne;
use App\Models\ReservationInterneArchivage;
use App\Models\Seance;
use App\Models\User;
use App\Models\SeanceArchivage;
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
       
        $idUtilisateur = Auth::user()->id_utilisateur;

        /** On récupère toutes les reservations à venir de cet utilisateur **/      
        $reservationPassees = ReservationInterneArchivage::where('id_utilisateur','=',$idUtilisateur)
        ->join('seance_archivage','reservation_interne_archivage.id_seance_archivage','=','seance_archivage.id_seance')
        ->join('activite','seance_archivage.id_activite','=','activite.id_activite')
        ->orderBy('seance_archivage.date_seance', 'DESC')
        ->select()
        ->get();

        return view('seancesPassees', [
            'reservationPassees' => $reservationPassees
        ]);
    }

    /**
     * Récupère les séances auxquelles est inscrit l'utilisateur connecté
     *
     * @param Request $request
     * @return array
     */
    public function seancesVenir(Request $request) {

        $idUtilisateur = Auth::user()->id_utilisateur;

        $utilisateur = User::select()
        ->where('id_utilisateur','=',$idUtilisateur)
        ->join('statut','utilisateur.id_statut','=','statut.id_statut')
        ->first();
        
        //On initialise toutes les listes 
        $seanceVenirCoach = [];
        $reservationVenirClient = [];

        if($utilisateur->nom_statut == 'ROLE_COACH'){
            // Si la personne connectée est un coach
            //On récupère toutes les séances à venir auxquelles le coach est affilié.
            $seanceVenirCoach = Seance::select()
            ->join('activite','seance.id_activite','=','activite.id_activite')
            ->where('id_coach','=',$utilisateur->id_utilisateur)
            ->get();
        }
        else if ($utilisateur->nom_statut == 'ROLE_CLIENT'){
            //Si la personne connectée est un client
            //on récupère toutes les reservations à venir de ce client    
            $reservationVenirClient = ReservationInterne::where('etat_reservation', '=', 'reservee')
            ->where('id_utilisateur','=',$idUtilisateur)
            ->join('seance','reservation_interne.id_seance','=','seance.id_seance')
            ->join('activite','seance.id_activite','=','activite.id_activite')
            ->orderBy('seance.date_seance', 'ASC')
            ->select()
            ->get();

        }

        return view ('seancesVenir', [
            'reservationVenirClient' => $reservationVenirClient,
            'seanceVenirCoach'       => $seanceVenirCoach,
            'utilisateur'            => $utilisateur
        ]);
    }

    /**
     * Récupère les séances auxquelles est inscrit l'utilisateur connecté
     *
     * @param Request $request
     * @return array
     */
    public function seancesVenirClient(Request $request) {
        //On récupère l'id du client choisis
        $idClient = $request->id_client;
        //On récupère les reservations en interne de ce client
        $reservationVenirClient = ReservationInterne::where('etat_reservation', '=', 'reservee')
        ->where('id_utilisateur','=',$idClient)
        ->join('seance','reservation_interne.id_seance','=','seance.id_seance')
        ->join('activite','seance.id_activite','=','activite.id_activite')
        ->orderBy('seance.date_seance', 'ASC')
        ->select()
        ->get();

        return view ('seancesVenirClient', [
            'reservationVenirClient' => $reservationVenirClient
        ]);
    }
}
