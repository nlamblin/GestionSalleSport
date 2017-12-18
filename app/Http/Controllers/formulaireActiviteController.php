<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class formulaireActiviteController extends Controller
{
    public function index()
    {
        return view('formulaireActivite');
    }

    protected function create(array $data)
    {
        Activite::create([
            'nom_activite'    => $data['nom-activite']
        ]);
    }
}

