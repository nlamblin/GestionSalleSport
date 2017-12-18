<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom_activite'
    ];


    protected $table = 'activite';

    protected $primaryKey = 'id_activite';
}
