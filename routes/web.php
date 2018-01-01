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
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');

Route::get('logout', 'Auth\LoginController@logout');

Route::get('seances','SeancesController@index');

Route::get('listeSeances','SeancesController@seancesParActivites');

Route::get('formulaireActivite','ActiviteController@index');

Route::get('seancesPassees', 'MesSeancesController@seancesPassees');

Route::get('seancesVenir', 'MesSeancesController@seancesVenir');

Route::get('compte', 'CompteController@index');

Route::put('effectuerReservation', 'ReservationController@effectuerReservation');

Route::post('annulerReservation', 'ReservationController@annulerReservation');

Route::get('coachsDisponibles', 'SeancesController@getCoachsDisponibles');

Route::group(['prefix' => 'admin'], function () {
    Route::get('showCreationActivite', 'AdministrationController@showCreationActivite');
    Route::get('showCreationSeance', 'AdministrationController@showCreationSeance');
    Route::get('showAjoutEmploye', 'AdministrationController@showAjoutEmploye');
    Route::get('showAjoutCoach', 'AdministrationController@showAjoutCoach');

    Route::post('creerActivite', 'AdministrationController@creerActivite')->name('admin/creerActivite');
    Route::post('creerSeance', 'AdministrationController@creerSeance')->name('admin/creerSeance');
    Route::post('ajouterEmploye', 'AdministrationController@ajouterEmploye')->name('admin/ajouterEmploye');
    Route::post('ajouterCoach', 'AdministrationController@ajouterCoach')->name('admin/ajouterCoach');

});

Route::put('prendreCarteAbonnement', 'CompteController@prendreCarteAbonnement');

Route::get('showRecommandations','SeancesController@showRecommandations');

Route::get('recommandationsSeances/{idSeance}','SeancesController@getRecommandations');

Auth::routes();