<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activite;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdministrationController extends Controller
{
    public function index() {
    	//On va transmettre toutes les activités existantes pour la création des séances
    	$listeActivites = Activite::get();


        return view('administration', [
            'listeActivites'    => $listeActivites
        ]);
    }

    public function creerActivite(Request $request){
    	$data = $request->all();
    	$nom = $data['nom_activite'];

    	Activite::create([
                'nom_activite'  => $nom
            ]);

    	return redirect('administration');

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

    	return redirect('administration');

    }

}
