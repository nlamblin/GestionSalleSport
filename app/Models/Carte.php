<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carte extends Model
{
    protected $table = 'carte';

    protected $primaryKey = 'id_carte';

    protected $fillable = [
        'id_carte', 'seances_dispo', 'active', 'id_utilisateur'
    ];

    public $timestamps = false;

}
