<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::view('admin', 'backend.app');
Route::get('admin/{any}', function () {
	return view('backend.app');
})->where('any', '.*');
