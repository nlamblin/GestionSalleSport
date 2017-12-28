<?php

namespace App\Http\Controllers;

use App\Models\ReservationInterne;
use App\Models\ReservationInterneArchivage;
use App\Models\Seance;
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

        /** On récupère toutes les reservations à venir de cet utilisateur **/      
        $reservationVenir = ReservationInterne::where('etat_reservation', '=', 'reservee')
        ->where('id_utilisateur','=',$idUtilisateur)
        ->join('seance','reservation_interne.id_seance','=','seance.id_seance')
        ->join('activite','seance.id_activite','=','activite.id_activite')
        ->orderBy('seance.date_seance', 'ASC')
        ->select()
        ->get();

        return view ('seancesVenir', [
            'reservationVenir' => $reservationVenir
        ]);
    }
}
