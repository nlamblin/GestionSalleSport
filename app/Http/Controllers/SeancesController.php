<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Seance;
use App\Models\Activite;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $listeSeances = Seance::where('id_activite', $request->id_activite)
                                ->get();

        $userId = Auth::user()->id_utilisateur;

        $userValide = User::getUser($userId)->estValide();

        $utilisateursAboValides = User::select('email', 'utilisateur.id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur')
            ->join('connexion', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('abonnement.date_fin_abo', '>', date('Y-m-d', time()))
            ->where('utilisateur.id_utilisateur', '!=', $userId)
            ->distinct()
            ->get();

        $utilisateursCartesValides = User::select('email', 'utilisateur.id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur')
            ->join('connexion', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
            ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
            ->where('carte.active', '=', true)
            ->where('utilisateur.id_utilisateur', '!=', $userId)
            ->distinct()
            ->get();

        // on merge les resultats dans une collection commune
        $utilisateursValides = new Collection();
        $utilisateursValides = $utilisateursValides->merge($utilisateursAboValides);
        $utilisateursValides = $utilisateursValides->merge($utilisateursCartesValides);

        $coachs = User::select('nom_utilisateur', 'prenom_utilisateur', 'id_utilisateur')
            ->where('id_statut', 2)
            ->where('actif', true)
            ->get();

        return view('listeSeances', [
            'listeSeances'          => $listeSeances,
            'utilisateurValide'     => $userValide,
            'utilisateursValides'   => $utilisateursValides,
            'coachs'                => $coachs
        ]);
    }

    /**
     * Méthode qui donne l'ensemble des coachs disponibles pour une seance donnée
     * appelle la fonction qui retourne si un coachs est disponible ou non
     *
     * @param Request $request
     * @return array des coachs disponibles
     */
    public function getCoachsDisponibles(Request $request) {
        $coachsDisponibles = [];

        $seance = Seance::select('id_seance', 'date_seance', 'heure_seance')
            ->where('id_seance', '=', $request->idSeance)
            ->first();

        $coachs = User::select('nom_utilisateur', 'prenom_utilisateur', 'id_utilisateur')
            ->where('id_statut', 2)
            ->where('actif', true)
            ->get();

        foreach ($coachs as $coach) {
            $idCoach = $coach->id_utilisateur;
            $heureSeance = $seance->heure_seance;
            $dateSeance = $seance->date_seance;

            $coachDispo = DB::select('SELECT coach_dispo_seance(?, ?, ?)', [$idCoach, $dateSeance, $heureSeance]);

            if($coachDispo) {
                array_push($coachsDisponibles, $coach);
            }
        }

        return $coachsDisponibles;
    }

}