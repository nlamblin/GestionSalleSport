<?php

namespace App\Http\Controllers;

use App\Models\Carte;
use App\Models\ReservationInterne;
use App\Models\User;
use App\Models\Activite;
use App\Models\Seance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdministrationController extends Controller
{

    /**
     * Affiche le formulaire de création d'une activité
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCreationActivite() {
        return view('admin/creationActivite');
    }

    /**
     * Affiche le formulaire de création d'une seance
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCreationSeance() {
        //On va transmettre toutes les activités existantes pour la création des séances
        $listeActivites = Activite::get();

        return view('admin/creationSeance', [
            'listeActivites'    => $listeActivites
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un employé
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAjoutEmploye() {
        return view('admin/ajoutEmploye');
    }

    /**
     * Affiche le formulaire d'ajout d'un coach
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAjoutCoach() {
        return view('admin/ajoutCoach');
    }

    /**
     * Affiche le formulaire de reservation pour un client
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showReservationClient(){
        $listeActivites = Activite::get();

        return view('admin/reservationClient', [
            'utilisateurValide'  => User::getUtilisateursValides(),
            'listeActivites'     => $listeActivites
        ]);
    }

    /**
     * Selectionne tous les utilisateurs
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAnnulationClient(){
        $clients = User::select()
        ->join('connexion','connexion.id_utilisateur','=','utilisateur.id_utilisateur')
        ->where('id_statut','=','4')
        ->get();

        return view('admin/annulationClient', [
            'utilisateur' => $clients
        ]);
    }

    /**
     * Affiche les seances avec encore de la place
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function affichageSeances(Request $request){
        //On récupère toutes les séances de l'activite séléctionnée
        $listeSeances = Seance::where('id_activite', $request->id_activite)
                    ->where('seance.places_restantes','>',0)
                    ->get();
        
        return view('admin/listeSeanceReservationClient', [
            'listeSeanceReservationClient' => $listeSeances,
        ]);
    }

    /**
     * Permet de créer une activité
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function creerActivite(Request $request){
    	$data = $request->all();
    	$nom = $data['nom_activite'];

    	Activite::create([
            'nom_activite' => $nom
        ]);

        return redirect()->back()->with('message', "L'activité a bien été créée.");
    }

    /**
     * Permet de créer une seance
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function creerSeance(Request $request){
  		$data = $request->all();

		/*Récupération des données*/
		$idactivite = $data['activite_seance'];
		$typeseance = $data['type_seance'];
		$niveauseance = $data['niveau_seance'];
		$date = $data['date_seance'];
		$heure = $data['heure_seance'];
		$coach = isset($data['coach_seance']);

		if ($typeseance == 'individuelle') {
			$places = 1;
		}
		else {
			$places = $data['places_seance'];
		}

		// On récupère les coachs et on essaie d'en trouver un libre
		$idCoach = null;
		if(!is_null($coach) && $typeseance == 'collective') {
            $coachs = User::select('nom_utilisateur', 'prenom_utilisateur', 'id_utilisateur')
                ->where('id_statut', 2)
                ->where('actif', true)
                ->get();

            $trouve = false;
            $i = 0;
            while(!$trouve && $i < sizeof($coachs)) {
                $coachDispo = DB::select('SELECT coach_dispo_seance(?, ?, ?)', [$coachs[$i]->id_utilisateur, $date, $heure]);

                if($coachDispo) {
                    $idCoach = $coachs[$i]->id_utilisateur;
                    $trouve = true;
                }

                $i++;
            }
        }
        //Si la seance est individuelle pas besoin de coach donc idcoach est null
        if(is_null($idCoach) && $typeseance == 'collective') {
		    return redirect()->back()->with('messageDanger', "La séance n'a pas pu être créer car aucun coach n'est disponibles");
        }

		Seance::create([
	                'id_activite' => $idactivite,
					'type_seance' => $typeseance ,
					'niveau_seance' => $niveauseance,
					'date_seance' => $date,
					'heure_seance' => $heure,
					'avec_coach' => $coach,
					'places_restantes' => $places,
					'capacite_seance' => $places,
                    'id_coach' => $idCoach
	            ]);

        return redirect()->back()->with('message', "La séance a bien été créée.");
    }

    /**
     * Permet d'ajouter un employe
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ajouterEmploye(Request $request) {

        $this->validate($request,[
            'email' => 'required|email|exists:connexion'
        ] , [
            'email.required' => 'Ce champ est requis',
            'email.email'    => 'Ce champ doit être un email',
            'email.exists'   => 'Cet email est inconnu'
        ]);

        $user = User::getUserByEmail($request->email);

        User::where('id_utilisateur', '=', $user->id_utilisateur)
            ->update(['id_statut' => 1]);

        return redirect()->back()->with('message', "L'employé a bien été ajouté.");
    }

    /**
     * Permet d'ajouter un coach
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ajouterCoach(Request $request) {

        $this->validate($request,[
            'email' => 'required|email|exists:connexion'
        ] , [
            'email.required' => 'Ce champ est requis',
            'email.email'    => 'Ce champ doit être un email',
            'email.exists'   => 'Cet email est inconnu'
        ]);

        $user = User::getUserByEmail($request->email);

        User::where('id_utilisateur', '=', $user->id_utilisateur)
            ->update(['id_statut' => 2]);

        return redirect()->back()->with('message', 'Le coach a bien été ajouté.');
    }


    /**
     * Méthode qui recupère l'ensemble des utilisateurs valides
     *
     * @return Collection|static
     */
    public function getUtilisateursValides() {
        
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

        return $utilisateursValides;
    }

    /**
     * Affiche la page d'archivage
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showArchivage(Request $request)
    {
        return view('admin/archivage');
    }

    /**
     * Appelle la fonction d'archivage et retourne un message
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archiverSeance(Request $request) {

        try {
            DB::select('SELECT changementetatreservationinterne()');
        }
        catch (Exception $e) {
            // on stock l'erreur dans les logs storage/logs/laravel.log
            logger()->error($e->getMessage());
            return redirect()->back()->with('messageDanger', "Un problème a été rencontré lors du changement d'état des réservations des clients internes.");
        }

        try {
            DB::select('SELECT changementetatreservationexterne()');
        }
        catch (Exception $e) {
            // on stock l'erreur dans les logs storage/logs/laravel.log
            logger()->error($e->getMessage());
            return redirect()->back()->with('messageDanger', "Un problème a été rencontré lors du changement d'état des réservations des clients extérieurs.");
        }

        try {
            DB::select('SELECT archivageSeance()');
        }
        catch (Exception $e) {
            // on stock l'erreur dans les logs storage/logs/laravel.log
            logger()->error($e->getMessage());
            return redirect()->back()->with('messageDanger', "Un problème a été rencontré lors de l'archivage des séances.");
        }

        return redirect()->back()->with('message', "L'archivage des séances a bien été effectué !");
    }

    /**
     * Récupère les séances d'un client donné
     *
     * @param Request $request
     * @return array
     */
    public function seancesVenirClient(Request $request) {

        // On récupère le client choisi
        $user = User::getUser($request->id_client);

        $reservationVenirClient = $user->getSeancesAVenir();

        return view ('admin/seancesVenirClient', [
            'reservationVenirClient' => $reservationVenirClient
        ]);
    }
}
