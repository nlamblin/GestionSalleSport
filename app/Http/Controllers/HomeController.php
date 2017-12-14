<?php

namespace App\Http\Controllers;

use App\Models\ReservationExterneArchivage;
use App\Models\ReservationInterneArchivage;
use App\Models\SeanceArchivage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nbSeancesArchivees = SeanceArchivage::count();
        $nbReservationsArchivees = ReservationExterneArchivage::count() + ReservationInterneArchivage::count();
        $nbCoach = User::getNbCoach();

        return view('home', [
            'nbSeancesArchivees'      => $nbSeancesArchivees,
            'nbReservationsArchivees' => $nbReservationsArchivees,
            'nbCoach'                 => $nbCoach
        ]);
    }
}