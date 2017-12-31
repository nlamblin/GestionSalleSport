<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activite;
use App\Models\Seance;
use Illuminate\Http\Request;


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

    public function creerActivite(Request $request){
    	$data = $request->all();
    	$nom = $data['nom_activite'];

    	Activite::create([
                'nom_activite'  => $nom
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
		$heure=$data['heure_seance'];
		$coach = isset($data['coach_seance']);
		if ( $typeseance = 'individuelle'){
			$places = '1';
		}
		else{
			$places = $data['places_seance'];
		}


		Seance::create([
	                'id_activite' => $idactivite,
					'type_seance' => $typeseance ,
					'niveau_seance' => $niveauseance,
					'date_seance' => $date,
					'heure_seance' => $heure,
					'avec_coach' => $coach,
					'places_restantes' => $places,
					'capacite_seance' => $places
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

}
