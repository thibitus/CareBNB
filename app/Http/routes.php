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
// API Routes
Route::group(['prefix' => 'api'], function()
{
	// Customer API Routes
	Route::get('get-available-days', 'APIController@GetAvailableDays');

	// Admin API Routes
	Route::get('get-all-appointments', 'AdminAPIController@GetAllAppointments');
	Route::get('get-appointment-info/{id}', 'AdminAPIController@GetAppointmentInfo');
	Route::get('get-all-availability', 'AdminAPIController@GetAllAvailability');
	Route::any('set-availability', 'AdminAPIController@SetAvailability');
	Route::any('delete-appointment', 'AdminAPIController@DeleteAppointment');
});

// Admin Routes
Route::group(['prefix' => 'admin'], function()
{
	Route::get('/', 'AdminController@index');
	// Appointment Routes
	Route::get('appointments', 'AdminController@appointments');

	// Availability Routes
	Route::get('availability', 'AdminController@availability');
	Route::post('add/availability', 'AdminController@addAvailability');

	// Configuration Routes
	Route::get('configuration', 'AdminController@configuration');
	Route::get('edit-hulpverlener/{hulpverlener_id}', 'AdminController@editHulpverlener');
	// Package Routes
	Route::get('packages', 'AdminController@packages');
	Route::get('edit-package/{package_id}', 'AdminController@editPackage');
	Route::post('update-package/{package_id}', array('as' => 'package.update'), 'AdminController@updatePackage');
});

Route::get('/', 'BookingController@getIndex');
Route::get('/overzicht', 'overzichtcontroller@index');
Route::get('/afspraken', 'overzichtcontroller@afspraken');
Route::controller('booking', 'BookingController');
Route::controller('admin', 'AdminController');
Route::auth();
Route::get('/home', 'overzichtcontroller@index');
