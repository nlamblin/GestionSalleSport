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

    public function showCreationActivite() {
        return view('admin/creationActivite');
    }

    public function showCreationSeance() {
        //On va transmettre toutes les activités existantes pour la création des séances
        $listeActivites = Activite::get();

        return view('admin/creationSeance', [
            'listeActivites'    => $listeActivites
        ]);
    }

    public function showAjoutEmploye() {
        return view('admin/ajoutEmploye');
    }

    public function showAjoutCoach() {
        return view('admin/ajoutCoach');
    }

    public function showReservationClient(){
        $listeActivites = Activite::get();

        return view('admin/reservationClient', [
            'utilisateurValide' => $this->getUtilisateursValides(),
            'listeActivites'     => $listeActivites
        ]);
    }

    public function showAnnulationClient(){
        return view('admin/annulationClient');
    }


    public function affichageSeances(Request $request){
        //On récupère toutes les séances de l'activite séléctionnée
        $listeSeances = Seance::where('id_activite', $request->id_activite)
                    ->where('seance.places_restantes','>',0)
                    ->get();
        //d($listeSeances);
        
        return view('listeSeanceReservationClient', [
            'listeSeanceReservationClient' => $listeSeances,
        ]);
    }

    public function creerActivite(Request $request){
    	$data = $request->all();
    	$nom = $data['nom_activite'];

    	Activite::create([
            'nom_activite' => $nom
        ]);

        return redirect()->back()->with('message', "L'activité a bien été créée.");
    }


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

        if(is_null($idCoach)) {
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

     public function enregistrerReservation(Request $request) {

        $message = null;
        // récupération de la séance
        $seance = Seance::find($request->idSeance);

        // on fait +1 pour ne pas oublier la personne qui fait la reservation (qui n'est pas compté comme une personne ajoutée)
        if($seance->places_restantes >= sizeof($request->personnesAAjouter) + 1) {

            // on reserve pour la personne connectée
            ReservationInterne::create([
                'etat_reservation'  => 'reservee',
                'id_utilisateur'    => Auth::user()->id_utilisateur,
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

            $user = User::getUser(Auth::user()->id_utilisateur);

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
            $archivage = DB::select('SELECT archivageSeance()');
        }
        catch (Exception $e) {
            // on stock l'erreur dans les logs storage/logs/laravel.log
            logger()->error($e->getMessage());
            return redirect()->back()->with('messageDanger', "Un problème a été rencontré lors de l'archivage des séances.");
        }

        return redirect()->back()->with('message', "L'archivage des séances a bien été effectué !");
    }
}
