<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');

// ROUTES POUR TOUT UTILISATEURS CONNECTES
Route::group(['middleware' => 'auth'], function() {
    Route::get('compte', 'CompteController@index');
    Route::get('logout', 'Auth\LoginController@logout');

    Route::post('miseAJourCompte', 'CompteController@mettreAJour')->name('miseAJourCompte');
});

// ROUTES POUR LES CLIENTS
Route::group(['prefix' => 'client', 'middleware' => 'client'], function() {
    Route::get('seancesPassees', 'MesSeancesController@seancesPassees');
    Route::get('seancesVenir', 'MesSeancesController@seancesVenir');
    Route::get('recommandationsSeances/{idSeance}','SeancesController@getRecommandations');
    Route::get('seances','SeancesController@index');
    Route::get('listeSeances','SeancesController@seancesParActivites');
    Route::get('coachsDisponibles', 'SeancesController@getCoachsDisponibles');
    Route::get('utilisateursValidesEtNonInscrit', 'SeancesController@getUtilisateursValidesEtNonInscrit');

    Route::post('annulerReservation', 'ReservationController@annulerReservation');

    Route::put('prendreCarteAbonnement', 'CompteController@prendreCarteAbonnement')->name('client/prendreCarteAbonnement');
    Route::put('effectuerReservation', 'ReservationController@effectuerReservation');

    Route::get('seancesExterne','SalleExterneController@showSeances');
    Route::get('seancesVenirExterne','SalleExterneController@showSeancesVenir');
    Route::get('seancesPasseesExterne','SalleExterneController@showSeancesPassees');

    Route::put('effectuerReservationSalleExterne', 'SalleExterneController@effectuerReservation');
});

// ROUTE POUR LES COACHS
Route::group(['prefix' => 'coach', 'middleware' => 'coach'], function () {
    Route::get('seancesVenir', 'MesSeancesController@seancesVenir');
});

// ROUTE POUR LES ADMINS ET EMPLOYES
Route::group(['prefix' => 'admin'], function() {
    // ROUTES POUR LES ADMINS ET EMPLOYES
    Route::group(['middleware' => 'employe'], function() {
        Route::get('listeSeances','SeancesController@seancesParActivites');
        Route::get('showCreationSeance', 'AdministrationController@showCreationSeance');
        Route::get('showAjoutCoach', 'AdministrationController@showAjoutCoach');
        Route::get('showReservationClient', 'AdministrationController@showReservationClient');
        Route::get('showAnnulationClient', 'AdministrationController@showAnnulationClient');
        Route::get('listeSeancesReservationClient','AdministrationController@affichageSeances');
        Route::get('seanceVenirClient','AdministrationController@seancesVenirClient');
        
        Route::post('creerSeance', 'AdministrationController@creerSeance')->name('admin/creerSeance');
        Route::post('ajouterCoach', 'AdministrationController@ajouterCoach')->name('admin/ajouterCoach');
        Route::post('annulerReservation', 'ReservationController@annulerReservation');

        Route::put('effectuerReservation', 'ReservationController@effectuerReservation');
        Route::get('planningCoach','AdministrationController@showPlanningCoach');
        Route::get('seanceVenirCoach','AdministrationController@seancesVenirCoach');
        Route::get('planningGeneral','AdministrationController@showPlanningGeneral');

    });

    // ROUTE POUR LES ADMINS EXCLUSIVEMENT
    Route::group(['middleware' => 'admin'], function() {
        Route::get('showCreationActivite', 'AdministrationController@showCreationActivite');
        Route::get('showAjoutEmploye', 'AdministrationController@showAjoutEmploye');
        Route::get('archivage', 'AdministrationController@showArchivage');

        Route::post('creerActivite', 'AdministrationController@creerActivite')->name('admin/creerActivite');
        Route::post('ajouterEmploye', 'AdministrationController@ajouterEmploye')->name('admin/ajouterEmploye');
        Route::post('archiver', 'AdministrationController@archiver')->name('admin/archiver');
    });
});

Auth::routes();