<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Compte extends Controller
{

    public function index() {
        return view('compte');
    }

}
