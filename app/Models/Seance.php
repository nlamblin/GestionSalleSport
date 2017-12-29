<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    protected $table = 'seance';

    protected $primaryKey = 'id_seance';

    protected $fillable = [
        'id_coach'
    ];


    public $timestamps = false;
}
