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
        $seancesPassees = [];
        $idUtilisateur = Auth::user()->id_utilisateur;

        $idsSeancesArchivage = ReservationInterneArchivage::select('id_seance_archivage')->where('id_utilisateur', $idUtilisateur)->get();

        foreach ($idsSeancesArchivage as $idSeanceArchivage) {
            $seanceArchivage= SeanceArchivage::find($idSeanceArchivage);
            array_push($seancesPassees, $seanceArchivage);
        }

        return view('seancesPassees', [
            'seancesPassees' => $seancesPassees
        ]);
    }

    /**
     * Récupère les séances auxquelles est inscrit l'utilisateur connecté
     *
     * @param Request $request
     * @return array
     */
    public function seancesVenir(Request $request) {
        $seancesVenir = [];
        $idUtilisateur = Auth::user()->id_utilisation;

        $idsSeances = ReservationInterne::select('id_seance')->where('id_utilisateur', $idUtilisateur)->get();

        foreach ($idsSeances as $idSeance) {
            $seance = Seance::find($idSeance);
            array_push($seancesVenir, $seance);
        }

        return view ('seancesVenir', [
            'seancesVenir' => $seancesVenir
        ]);
    }
}
