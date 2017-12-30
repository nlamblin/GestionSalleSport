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

Route::put('effectuerReservation', 'ReservationController@effectuerReservation');

Route::get('seancesPassees', 'MesSeancesController@seancesPassees');

Route::get('seancesVenir', 'MesSeancesController@seancesVenir');

Route::get('compte', 'Compte@index');

Route::post('annulerReservation', 'ReservationController@annulerReservation');

Route::get('administration', 'AdministrationController@index');

Route::post('creerActivite', 'AdministrationController@creerActivite')->name('creerActivite');

Route::post('creerSeance', 'AdministrationController@creerSeance')->name('creerSeance');

Auth::routes();