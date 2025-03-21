<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorksController;
use App\Http\Controllers\DiscourseController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/
// Home routes
Route::get('/', [HomeController::class, 'index'])->name('page.home');

// Project routes
Route::get('/projekte', [ProjectController::class, 'index'])->name('page.projects');
Route::get('/projekt/{project}/{slug?}', [ProjectController::class, 'show'])->name('page.project');

// Works routes
Route::get('/werkliste', [WorksController::class, 'index'])->name('page.works');
Route::get('/werkliste/autorenschaft/{isSearch?}', [WorksController::class, 'authors'])->name('page.works.authors');
Route::get('/werkliste/jahr', [WorksController::class, 'year'])->name('page.works.year');
Route::get('/werkliste/programm', [WorksController::class, 'program'])->name('page.works.program');
Route::get('/werkliste/status', [WorksController::class, 'state'])->name('page.works.state');

// Discourse routes
Route::get('/diskurs', [DiscourseController::class, 'index'])->name('page.discourse');
Route::get('/diskurs/recherche', [DiscourseController::class, 'research'])->name('page.discourse.research');
Route::get('/diskurs/veranstaltungen', [DiscourseController::class, 'events'])->name('page.discourse.events');
Route::get('/diskurs/publikationen', [DiscourseController::class, 'publications'])->name('page.discourse.publications');
Route::get('/diskurs/{discourse}/{slug?}', [DiscourseController::class, 'show'])->name('page.discourse.detail');

// Office
Route::get('/buero/team', [TeamController::class, 'index'])->name('page.office.team');
Route::get('/buero/profil', [ProfileController::class, 'index'])->name('page.office.profile');
Route::get('/buero/jobs', [JobController::class, 'index'])->name('page.office.jobs');

// Contact
Route::get('/kontakt', [ContactController::class, 'index'])->name('page.contact');

// History
Route::get('/geschichte', [HistoryController::class, 'index'])->name('page.history');

// Search
Route::get('/suche', [SearchController::class, 'index'])->name('page.search.index');
Route::get('/suche/{keyword?}', [SearchController::class, 'index'])->name('page.search.index');

// Admin routes
Route::view('admin', 'backend.app');
Route::get('admin/{any}', function () {
    return view('backend.app');
})->where('any', '.*');