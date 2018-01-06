<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
     * Récupère les séances à venir de l'utilisateur courant
     *
     * @return mixed
     */
    public function getSeancesAVenir() {

        if($this->estClient()) {
            $reservations = ReservationInterne::where('etat_reservation', '=', 'reservee')
                ->where('id_utilisateur', '=', $this->id_utilisateur)
                ->join('seance','reservation_interne.id_seance','=','seance.id_seance')
                ->join('activite','seance.id_activite','=','activite.id_activite')
                ->orderBy('seance.date_seance', 'ASC')
                ->select()
                ->get();
        }
        elseif($this->estCoach()) {
            $reservations = Seance::select()
                ->join('activite','seance.id_activite','=','activite.id_activite')
                ->where('id_coach','=', $this->id_utilisateur)
                ->get();
        }

        return $reservations;
    }

    public function getSeancesPassees() {
        return ReservationInterneArchivage::where('id_utilisateur','=', $this->id_utilisateur)
            ->join('seance_archivage','reservation_interne_archivage.id_seance_archivage','=','seance_archivage.id_seance')
            ->join('activite','seance_archivage.id_activite','=','activite.id_activite')
            ->orderBy('seance_archivage.date_seance', 'DESC')
            ->select()
            ->get();
    }

    /**
     * Permet de savoir si l'utilisateur courant est un admin
     */
    public function estAdmin() {

        $idStatutAdmin = Statut::select('id_statut')
            ->where('nom_statut', '=', 'ROLE_ADMIN')
            ->first();

        return ($idStatutAdmin->id_statut == $this->id_statut) ? true : false;
    }

    /**
     * Permet de savoir si l'utilisateur courant est un employe
     */
    public function estEmploye() {
        $idStatutEmployee = Statut::select('id_statut')
            ->where('nom_statut', '=', 'ROLE_EMPLOYEE')
            ->first();

        return ($idStatutEmployee->id_statut == $this->id_statut) ? true : false;
    }

    /**
     * Permet de savoir si l'utilisateur courant est un coach
     */
    public function estCoach() {
        $idStatutCoach = Statut::select('id_statut')
            ->where('nom_statut', '=', 'ROLE_COACH')
            ->first();

        return ($idStatutCoach->id_statut == $this->id_statut) ? true : false;
    }

    /**
     * Permet de savoir si l'utilisateur courant est un client
     */
    public function estClient() {
        $idStatutClient = Statut::select('id_statut')
            ->where('nom_statut', '=', 'ROLE_CLIENT')
            ->first();

        return ($idStatutClient->id_statut == $this->id_statut) ? true : false;
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
     * Retourne l'utilisateur en fonction de son email
     *
     * @param $email
     * @return mixed
     */
    public static function getUserByEmail($email) {
        return Connexion::join('utilisateur', 'utilisateur.id_utilisateur', '=', 'connexion.id_utilisateur')
                ->where('email', '=', $email)
                ->first();
    }

}