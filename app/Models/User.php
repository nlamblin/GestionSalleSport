<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom_utilisateur', 'prenom_utilisateur', 'date_naiss_utilisateur', 'id_statut','demande_relance','delai_relance'
    ];

    protected $table = 'utilisateur';

    protected $primaryKey = 'id_utilisateur';

    public $timestamps = false;

    /**
     * Récupère l'email de l'utilisateur courant
     *
     * @return user email
     */
    public function getEmail() {
        return Connexion::select('email')
            ->where('id_utilisateur', $this->id_utilisateur)
            ->get()[0];
    }

    /**
     * Informe si l'utilisateur est valide ou non
     *
     * @return mixed
     */
    public function estValide() {
        $validite = User::select('carte.active', 'abonnement.date_fin_abo', 'utilisateur.actif')
            ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('utilisateur.id_utilisateur', '=', $this->id_utilisateur)
            ->get()[0];

        if($validite->active && $validite->date_fin_abo > date('Y-m-d', time()) && $validite->actif) {
            $res = true;
        }
        else {
            $res = false;
        }

        return $res;
    }

    /**
     * Retourne l'instance de l'utilisateur connecté
     *
     * @param $idUtilisateur
     * @return mixed
     */
    public static function getUser($idUtilisateur) {
        return User::find($idUtilisateur);
    }

    /**
     * Compte le nombre de coach
     *
     * @return le nombre de coach
     */
    public static function getNbCoach() {
        return User::where('id_statut', 2)
            ->where('actif', true)
            ->count();
    }

    /**
     * Donne tous les utilisateurs valides (carte active ou abonnement valide)
     *
     * @return liste d'utilisateur valides
     */
    public static function getUtilisateursValides() {
        /*
         * CETTE REQUETE NE FONCTIONNE PAS (normalement) !!!
         */
        return User::select('email', 'id_utilisateur', 'nom', 'prenom')
            ->join('connexion', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
            ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('carte.active', true)
            ->where('abonnement.date_fin_abo', '>', date('Y-m-d', time()))
            ->get();
    }

}