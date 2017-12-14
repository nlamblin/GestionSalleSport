<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Connexion extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_utilisateur', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = 'connexion';

    protected $primaryKey = 'id_connexion';

    public $timestamps = false;


    
    /*
     * POUR EVITER D'AJOUTER UNE COLONNE DANS LA BDD POUR LA DECONNEXION ON DEFINIT LES 3 METHODES SUIVANTES
     * getRememberToken()
     * setRememberToken()
     * setAttribute()
     */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        return null;
    }

    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberToken();
        if(!$isRememberTokenAttribute) parent::setAttribute($key, $value);
    }
}
