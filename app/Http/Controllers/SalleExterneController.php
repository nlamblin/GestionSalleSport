<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalleExterneController extends Controller
{

    public function showSeances()
    {
        $user = User::getUser(Auth::user()->id_utilisateur);

        $seances = DB::connection('pgsql_externe')->table('Seance')
            ->join('Activite', 'Seance.idActivite', '=', 'Activite.idActivite')
            ->get();

        return view('externe/seances', ['listeSeances' => $seances, 'utilisateurValide' => $user->estValide()]);
    }

    /**
     * Effectue la reservation
     *
     * @param Request $request
     */
    public function effectuerReservation(Request $request) {

        $utilisateur = User::getUser(Auth::user()->id_utilisateur);
        $idSeance = $request->idSeance;

        $typePaiment = null;

        $validiteAbo = User::select('abonnement.date_fin_abo')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('utilisateur.id_utilisateur', '=', $utilisateur->id_utilisateur)
            ->where('abonnement.date_fin_abo', '>', date('Y-m-d', time()))
            ->get();

        if(sizeof($validiteAbo) > 0) {
            $typePaiment = 2;
        }
        else {
            $validiteCarte = User::select('carte.active')
                ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
                ->where('utilisateur.id_utilisateur', '=', $utilisateur->id_utilisateur)
                ->where('carte.active', '=', true)
                ->get();

            if(sizeof($validiteCarte) > 0) {
                $typePaiment = 3;
            }
            else {
                return 'Une erreur est survenue. Vous n\'avez pas d\'abonnement ou de carte valide';
            }
        }

        DB::connection('pgsql_externe')->table('vueReservationExterne')->insert([
            'idSeance'           => $idSeance,
            'idUtilisateur'      => $utilisateur->id_utilisateur
        ]);

        return 'Votre réservation a bien été prise en compte';
    }

    /**
     * Affiche les séances à venir du client dans la salle externe
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSeancesVenir() {
        return view('externe/seancesVenir');
    }


    /**
     * Affiche les séances passées du client dans la salle externe
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSeancesPassees() {
        return view('externe/seancesPassees');
    }
}
