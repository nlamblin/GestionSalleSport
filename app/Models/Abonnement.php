<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    protected $table = 'abonnement';

    protected $primaryKey = 'id_abonnement';

     protected $fillable = [
        'id_abonnement', 'type_abo', 'date_fin_abo', 'id_utilisateur'
    ];

    public $timestamps = false;
}
