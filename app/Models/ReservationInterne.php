<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationInterne extends Model
{
    protected $fillable = [
        'etat_reservation', 'id_utilisateur', 'id_seance'
    ];

    protected $table = 'reservation_interne';

    protected $primaryKey = 'id_reservation';

    public $timestamps = false;
}
