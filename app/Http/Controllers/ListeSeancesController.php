<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seance;
use App\Models\Activite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListeSeancesController extends Controller
{

    public function index()
    {
    	
    	$listeActivites = Activite::get();

    	$listeSeances = Seance::join('activite', 'activite.id_activite', '=', 'seance.id_activite')->where('places_restantes', '>' ,0)->get();

        return view('listeseances', [
            'listeActivites'      => $listeActivites,
            'listeSeances'		 => $listeSeances,
        ]);
    }
}

    
