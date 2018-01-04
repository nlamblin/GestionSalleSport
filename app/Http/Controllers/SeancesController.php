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

    
    
    public function getUtilisateursValides($userId) {
        
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

        return $utilisateursValides;
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


    /**
     * Méthode qui affiche les seances disponibles en fonction d'une activité
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seancesParActivites(Request $request) {

        $TEMPlisteSeances = Seance::where('id_activite', $request->id_activite)
                                ->get();

        $userId = Auth::user()->id_utilisateur;
        $userValide = User::getUser($userId)->estValide();

        //On cherche les reservation de l'utilisateur
        $seanceUser = ReservationInterne::join('seance','reservation_interne.id_seance','=','seance.id_seance')
            ->where('reservation_interne.id_utilisateur','=',$userId)
            ->where('reservation_interne.etat_reservation','=','reservee')
            ->select("seance.id_seance","seance.type_seance" ,"seance.capacite_seance" ,"seance.places_restantes" ,"seance.niveau_seance" ,"seance.avec_coach","seance.date_seance" ,"seance.heure_seance" ,"seance.id_activite","seance.id_coach")
            ->get();

        $listeSeances = [];
        foreach($TEMPlisteSeances as $value) {
            if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($listeSeances, $value);
            }
        }

        return view('listeSeances', [
            'listeSeances'          => $listeSeances,
            'utilisateurValide'     => $userValide,
            'utilisateursValides'   => $this->getUtilisateursValides($userId)
        ]);
    }




    /**
     * Méthode qui récupère toutes les recommandations pour un utilisateur
     *
     * @param Request $request
     * @return mixed
     */
    public function getRecommandations(Request $request)
    {
        $userId = Auth::user()->id_utilisateur;

        $userValide = User::getUser($userId)->estValide();

        // On récupère l'id de la séance que l'utilisateur à choisit
        $id = $request->idSeance;

        // On récupère les infos de la séance courante
        $seanceCourante = Seance::join('activite','seance.id_activite','=','activite.id_activite')
            ->where('id_seance','=',$id)
            ->select()
            ->first();
        
    
        $activite   = $seanceCourante->id_activite;
        $date       = $seanceCourante->date_seance;
        $heure      = $seanceCourante->heure_seance;

               //On recherche les autres séances sur cette activite le même jour

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

               
        $seanceUser = ReservationInterne::join('seance','reservation_interne.id_seance','=','seance.id_seance')
        ->join('activite','seance.id_activite','=','activite.id_activite')
        ->where('reservation_interne.id_utilisateur','=',$userId)
        ->where('reservation_interne.etat_reservation','=','reservee')
        ->select("seance.id_seance","seance.type_seance" ,"seance.capacite_seance" ,"seance.places_restantes" ,"seance.niveau_seance" ,"seance.avec_coach","seance.date_seance" ,"seance.heure_seance" ,"seance.id_activite","seance.id_coach","activite.nom_activite" )
        ->get();


        //Pour ne garder que ceux où il n'est pas inscrit
        $recommandationsMemeActiviteMemeHeure = array();
        foreach($TEMPrecommandationsMemeActiviteMemeHeure as $value) {
            if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($recommandationsMemeActiviteMemeHeure, $value);
            }
        }

        $recommandationsMemeActiviteMemeDate = array();
        foreach($TEMPrecommandationsMemeActiviteMemeDate as $value) {
            if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($recommandationsMemeActiviteMemeDate, $value);
            }
        }

        $recommandationsAutresActiviteMemeDateHeure = array();
        foreach($TEMPrecommandationsAutresActiviteMemeDateHeure as $value) {
           if(!$seanceUser->contains('id_seance', $value->id_seance)) {
                array_push($recommandationsAutresActiviteMemeDateHeure, $value);
            }
        }
   

        return view('listeRecommandations', [
            'recommandationsAutresActivitesMemeDateHeure' => $recommandationsAutresActiviteMemeDateHeure,
            'recommandationsMemeActiviteMemeHeure'      => $recommandationsMemeActiviteMemeHeure,
            'recommandationsMemeActiviteMemeDate'       => $recommandationsMemeActiviteMemeDate,
            'utilisateursValides'                       => $this->getUtilisateursValides($userId),
            'utilisateurValide'                         => $userValide
        ]);
    }

}