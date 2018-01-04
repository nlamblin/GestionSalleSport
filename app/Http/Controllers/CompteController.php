<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Carte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompteController extends Controller
{

    public function index() {
        $user = User::getUser(Auth::user()->id_utilisateur);
        $email = $user->getEmail();

        $data = [
            'user'      => $user,
            'email'     => $email->email
        ];

        $abonnementActif = Abonnement::where([
                ['id_utilisateur', '=', $user->id_utilisateur],
                ['date_fin_abo', '>', date('Y-m-d', time())],
            ])->first();

        if(sizeof($abonnementActif) > 0) {
            $data['abonnement'] = $abonnementActif;
        }
        else {
            $carteActive = Carte::where([
                ['id_utilisateur', '=', $user->id_utilisateur],
                ['active', '=', true]
            ])->first();

            if(sizeof($carteActive) > 0) {
                $data['carte'] = $carteActive;
            }
        }

        return view('compte', $data);
    }

    /**
     * Méthode de prise d'un abonnement ou d'une carte
     *
     * @param Request $request
     * @return string
     */
    public function prendreCarteAbonnement(Request $request) {
        $user = User::getUser(Auth::user()->id_utilisateur);
        $typeAbo = $request->typeAbo;

        if($typeAbo == 'carte') {
            // création de la carte
            Carte::create([
                'seance_dispo'   => 10,
                'active'         => true,
                'id_utilisateur' => $user->id_utilisateur
            ]);
        }
        else {
            $data = [
                'id_utilisateur' => $user->id_utilisateur,
            ];

            if($typeAbo == 'abo-annuel') {
                $data['date_fin_abo'] = $date = date("Y-m-d", strtotime("+1 year"));
                $data['type_abo'] = 'annuel';
            }
            else {
                $data['date_fin_abo'] = $date = date("Y-m-d", strtotime("+1 month"));
                $data['type_abo'] = 'mensuel';
            }

            // création de l'abonnement
            Abonnement::create($data);
        }

        return 'Votre abonnement a bien été pris en compte.';
    }

    public function mettreAJour(Request $request) {
        $user = User::getUser(Auth::user()->id_utilisateur);

        /*$this->validate($request,[
            'password'              => 'string|min:6|confirmed'
        ] , [
            'password.min'          => 'Le mot de passe doit contenir au minimum 6 caractères',
            'password.confirmed'    => 'Les mots de passes sont différents',
        ]);*/

        $demandeRelance = $request->demande_relance;
        $delaiRelance = $request->select_delai;

        if($demandeRelance == 'on') {
            $demandeRelance = true;
        }
        else {
            $demandeRelance = false;
            $delaiRelance = null;
        }

        User::where('id_utilisateur', '=', $user->id_utilisateur)
            ->update([
                'demande_relance'   => $demandeRelance,
                'delai_relance'     => $delaiRelance
            ]);

        return redirect()->back()->with('message', 'Votre profil a bien été mis à jour.');
    }

}
