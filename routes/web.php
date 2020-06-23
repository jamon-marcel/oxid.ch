<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

// Home routes
Route::get('/', 'HomeController@index')->name('page.home');

// Project routes
Route::get('/projekte', 'ProjectController@index')->name('page.projects');
Route::get('/projekt/{project}/{slug?}', 'ProjectController@show')->name('page.project');

// Works routes
Route::get('/werkliste', 'WorksController@index')->name('page.works');
Route::get('/werkliste/autorenschaft', 'WorksController@authors')->name('page.works.authors');
Route::get('/werkliste/jahr', 'WorksController@year')->name('page.works.year');
Route::get('/werkliste/programm', 'WorksController@program')->name('page.works.program');
Route::get('/werkliste/status', 'WorksController@state')->name('page.works.state');

// Discourse routes
Route::get('/diskurs', 'DiscourseController@index')->name('page.discourse');
Route::get('/diskurs/recherche', 'DiscourseController@research')->name('page.discourse.research');
Route::get('/diskurs/veranstaltungen', 'DiscourseController@events')->name('page.discourse.events');
Route::get('/diskurs/publikationen', 'DiscourseController@publications')->name('page.discourse.publications');
Route::get('/diskurs/{discourse}/{slug?}', 'DiscourseController@show')->name('page.discourse.detail');

// Office
Route::get('/buero/team', 'TeamController@index')->name('page.office.team');
Route::get('/buero/profil', 'ProfileController@index')->name('page.office.profile');
Route::get('/buero/jobs', 'JobController@index')->name('page.office.jobs');

// Contact
Route::get('/kontakt', 'ContactController@index')->name('page.contact');

// History
Route::get('/geschichte', 'HistoryController@index')->name('page.history');

Route::get('/suche', 'SearchController@index')->name('page.search.index');
Route::post('/suche/{keyword?}', 'SearchController@index')->name('page.search.index');

// Url based images
Route::get('/img/{template}/{filename}/{maxW?}/{maxH?}/{coords?}', 'ImageController@getResponse');

// Data
Route::get('/import', 'DataController@index');



// Busu
Route::get('/busu', function () {
	return view('frontend.busu');
});

// Admin routes
Route::view('admin', 'backend.app');
Route::get('admin/{any}', function () {
	return view('backend.app');
})->where('any', '.*');
