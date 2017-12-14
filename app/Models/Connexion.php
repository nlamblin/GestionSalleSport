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
        'id_utilisateur', 'mail_utilisateur', 'password',
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
}
