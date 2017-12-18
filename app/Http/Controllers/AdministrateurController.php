<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdministrateurController extends Controller
{
    public function index()
    {
        return view('formulaireActivite');
    }

    
    public function creerActivite(Request $request){

        Activite::create([
            'nom_activite'    => $data['nom-activite']
        ]);

        return redirect()->action('formulaireActiviteController@index');
    }
}

