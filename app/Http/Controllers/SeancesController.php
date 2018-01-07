<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Seance;
use App\Models\Activite;
use App\Models\ReservationInterne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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

        $heureSeance = $seance->heure_seance;
        $dateSeance = $seance->date_seance;

        foreach ($coachs as $coach) {
            $idCoach = $coach->id_utilisateur;

            $coachDispo = DB::select('SELECT coach_dispo_seance(?, ?, ?)', [$idCoach, $dateSeance, $heureSeance]);

            if($coachDispo) {
                array_push($coachsDisponibles, $coach);
            }
        }

        return $coachsDisponibles;
    }


    /**
     * Méthode qui affiche les seances disponibles en fonction d'une activité
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seancesParActivites(Request $request) {

        $TEMPlisteSeances = Seance::where('id_activite', $request->id_activite)
                                ->get();

        $user = User::getUser(Auth::user()->id_utilisateur);

        $seanceUser = $user->getSeancesAVenir();

        $listeSeances = [];
        foreach($TEMPlisteSeances as $value) {
            if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($listeSeances, $value);
            }
        }

        return view('listeSeances', [
            'listeSeances'          => $listeSeances,
            'utilisateurValide'     => $user->estValide(),
            'utilisateursValides'   => User::getUtilisateursValides($user->id_utilisateur)
        ]);
    }

    /**
     * Récupère les utilisateurs valides pas encore inscrit à une séance
     *
     * @param Request $request
     * @return array
     */
    public function getUtilisateursValidesEtNonInscrit(Request $request) {
        $user = User::getUser(Auth::user()->id_utilisateur);

        // On récupère tous les utilisateurs valides
        $utilisateursValides = User::getUtilisateursValides($user->id_utilisateur);

        $utilisateursValidesEtNonInscrit = [];
        // On vérifie pour chaque utilisateur si il est déjà inscrit ou pas à la séance
        foreach ($utilisateursValides as $utilisateur) {
            $reservation = $utilisateur->getSeancesAVenir();

            if(!$reservation->contains('id_seance', $request->id_seance)) {
                array_push($utilisateursValidesEtNonInscrit, $utilisateur);
            }
        }

        return $utilisateursValidesEtNonInscrit;
    }

    /**
     * Méthode qui récupère toutes les recommandations pour un utilisateur
     *
     * @param Request $request
     * @return mixed
     */
    public function getRecommandations(Request $request) {

        $user = User::getUser(Auth::user()->id_utilisateur);

        // On récupère l'id de la séance que l'utilisateur à choisit
        $idSeance = $request->idSeance;

        // On récupère les infos de la séance courante
        $seanceCourante = Seance::join('activite','seance.id_activite','=','activite.id_activite')
            ->where('id_seance','=', $idSeance)
            ->select()
            ->first();

        $activite   = $seanceCourante->id_activite;
        $date       = $seanceCourante->date_seance;
        $heure      = $seanceCourante->heure_seance;

        // On recherche les autres séances sur cette activite le même jour
        $TEMPrecommandationsMemeActiviteMemeDate = Seance::join('activite','seance.id_activite','=','activite.id_activite')
            ->where('seance.id_activite', '=',$activite)
            ->where('date_seance','=',$date)
            ->where('places_restantes','>',0)
            ->select()
            ->get();

               //On recherche les autres séances sur cette activite a la même heure
        $TEMPrecommandationsMemeActiviteMemeHeure = Seance::join('activite','seance.id_activite','=','activite.id_activite')
            ->where('seance.id_activite', '=',$activite)
            ->where('heure_seance','=',$heure)
            ->where('places_restantes','>',0)
            ->select()
            ->get();

        //On recherche toutes les séances à la meme heure/date
        $TEMPrecommandationsAutresActiviteMemeDateHeure = Seance::join('activite','seance.id_activite','=','activite.id_activite')
            ->where('date_seance','=',$date)
            ->where('heure_seance','=',$heure)
            ->where('seance.id_activite', '!=',$activite)
            ->where('places_restantes','>',0)
            ->select()
            ->get();

        $seanceUser = $user->getSeancesAVenir();

        //Pour ne garder que ceux où il n'est pas inscrit
        $recommandationsMemeActiviteMemeHeure = [];

        foreach($TEMPrecommandationsMemeActiviteMemeHeure as $value) {
            if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($recommandationsMemeActiviteMemeHeure, $value);
            }
        }

        $recommandationsMemeActiviteMemeDate = [];
        foreach($TEMPrecommandationsMemeActiviteMemeDate as $value) {
            if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($recommandationsMemeActiviteMemeDate, $value);
            }
        }

        $recommandationsAutresActiviteMemeDateHeure = [];
        foreach($TEMPrecommandationsAutresActiviteMemeDateHeure as $value) {
           if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($recommandationsAutresActiviteMemeDateHeure, $value);
            }
        }
   
        return view('listeRecommandations', [
            'recommandationsAutresActivitesMemeDateHeure' => $recommandationsAutresActiviteMemeDateHeure,
            'recommandationsMemeActiviteMemeHeure'        => $recommandationsMemeActiviteMemeHeure,
            'recommandationsMemeActiviteMemeDate'         => $recommandationsMemeActiviteMemeDate,
            'utilisateursValides'                         => User::getUtilisateursValides($user->id_utilisateur),
            'utilisateurValide'                           => $user->estValide()
        ]);
    }

}