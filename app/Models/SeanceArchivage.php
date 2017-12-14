<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SeanceArchivage extends Model
{
    protected $table = 'seance_archivage';

    protected $primaryKey = 'id_seance';

    public static function nbSeances() {
        return DB::select('SELECT nb_seances_archivees() as nb')[0]->nb;
    }
}
