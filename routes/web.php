<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

// Page routes
Route::get('/', 'HomeController@index')->name('page.home');
Route::get('/team', 'TeamController@index')->name('page.team');

// Admin routes
Route::view('admin', 'backend.app');
Route::get('admin/{any}', function () {
	return view('backend.app');
})->where('any', '.*');
