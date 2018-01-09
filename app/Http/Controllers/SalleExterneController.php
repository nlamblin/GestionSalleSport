<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalleExterneController extends Controller
{

    public function showSeances()
    {
        return view('externe/seances');
    }
    /**
     * Affiche les séances à venir du client dans la salle externe
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSeancesVenir() {
        return view('externe/seancesVenir');
    }


    /**
     * Affiche les séances passées du client dans la salle externe
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSeancesPassees() {
        return view('externe/seancesPassees');
    }
}
