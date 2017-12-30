<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    protected $table = 'seance';

    protected $primaryKey = 'id_seance';

    protected $fillable = [
        'id_coach','type_seance','capacite_seance','places_restantes','niveau_seance','avec_coach','date_seance','heure_seance','id_activite'
    ];

    public $timestamps = false;
}
