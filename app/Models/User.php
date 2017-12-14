<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomUtilisateur', ''
    ];

    protected $table = 'utilisateur';

    protected $primaryKey = 'idutilisateur';

    public $timestamps = false;

    /**
     * Get email of the current user
     *
     * @return user email
     */
    public function getEmail() {
        return DB::select('SELECT mailUtilisateur FROM Connexion WHERE idUtilisateur = ?', [$this->id]);
    }
}