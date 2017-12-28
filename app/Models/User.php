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

        $validiteAbo = User::select('abonnement.date_fin_abo')
            ->join('abonnement', 'utilisateur.id_utilisateur', '=', 'abonnement.id_utilisateur')
            ->where('utilisateur.id_utilisateur', '=', $this->id_utilisateur)
            ->where('abonnement.date_fin_abo', '>', date('Y-m-d', time()))
            ->get();

        if(sizeof($validiteAbo) > 0) {
            return true;
        }
        else {
            $validiteCarte = User::select('carte.active')
                ->join('carte', 'utilisateur.id_utilisateur', '=', 'carte.id_utilisateur')
                ->where('utilisateur.id_utilisateur', '=', $this->id_utilisateur)
                ->where('carte.active', '=', true)
                ->get();

            if(sizeof($validiteCarte) > 0) {
                return true;
            }
        }

        return false;
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

}