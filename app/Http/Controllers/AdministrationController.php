<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdministrationController extends Controller
{
    public function index() {
        return view('administration');
    }

    public function creerActivite(){
    	
    }
}
