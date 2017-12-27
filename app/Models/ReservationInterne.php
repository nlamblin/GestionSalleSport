<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationInterne extends Model
{
    protected $fillable = [
        'etat_res', 'id_utilisateur', 'id_seance'
    ];

    protected $table = 'reservation_interne';

    protected $primaryKey = 'id_reservation';
}
