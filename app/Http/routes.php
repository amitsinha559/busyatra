<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

// auth is not working , 'middleware' => 'auth'
Route::group(['prefix' => 'api/v1.0', 'middleware' => 'auth'], function(){
    Route::post('add/bus/owner', 'BusController@addBusOwner');
    Route::post('add/bus/details', 'BusController@addBusDetails');
    Route::post('add/bus/destination', 'BusController@addDroppingPointDetails');
    Route::post('add/bus/departure', 'BusController@addDepartureDetails');
    Route::get('get/bus/owner/{id}', 'BusController@getBusOwnerDetails');
    Route::get('get/bus/owners', 'BusController@getBusOwnersDetails');
    Route::get('get/bus/departure/{id}', 'BusController@getDepartureDetails');
    Route::get('get/bus/destination/{id}', 'BusController@getDroppingDetails');
    Route::post('update/bus/owner/{owner_id}', 'BusController@updateBusOwner');
    Route::post('update/bus/details/{bus_id}', 'BusController@updateBusDetails');
    Route::post('update/bus/departure/{departure_id}', 'BusController@updateDepartureDetails');
    Route::post('update/bus/destination/{dropping_id}', 'BusController@updateDroppingDetails');
    
    Route::get('get/places', 'SearchController@getPlaces');
    Route::get('get/places/{doj}/{dor}/{departure_id}/{departure_place}/{destination_id}/{destination}', 'SearchController@getBusListByPlaceAndDate');
});

Route::group(['prefix' => 'api/v1.0'], function(){
    
});