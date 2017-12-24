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
        return DB::table('connexion')
            ->where('id_utilisateur', $this->id_utilisateur)
            ->get();
    }

    /**
     * Compte le nombre de coach
     *
     * @return le nombre de coach
     */
    public static function getNbCoach() {
        return DB::table('utilisateur')
            ->where('id_statut', 2)
            ->where('actif', true)
            ->count();
    }

    /**
     * Donne tous les utilisateurs valides (carte active ou abonnement valide)
     *
     * @return liste d'utilsiateur valides
     */
    public static function getUtilisateursValides() {
        /*
         * CETTE REQUETE NE FONCTIONNE PAS (normalement) !!!
         */
        return DB::table('utilisateur')
            ->select('email', 'id_utilisateur', 'nom', 'prenom')
            ->join('connexion', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
            ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('carte.active', true)
            ->where('abonnement.datefinabo', '>', date('Y-m-d', time()))
            ->get();
    }

}