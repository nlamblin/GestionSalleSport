<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Seance;
use App\Models\Activite;
use Illuminate\Support\Collection;
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

        $userValide = User::getUser(Auth::user()->id_utilisateur)->estValide();

        $utilisateursAboValides = User::select('email', 'utilisateur.id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur')
            ->join('connexion', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('abonnement.date_fin_abo', '>', date('Y-m-d', time()))
            ->distinct()
            ->get();

        $utilisateursCartesValides = User::select('email', 'utilisateur.id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur')
            ->join('connexion', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
            ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
            ->where('carte.active', '=', true)
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

}