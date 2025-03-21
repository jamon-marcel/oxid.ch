<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\HomeImageController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectImageController;
use App\Http\Controllers\Api\ProjectDocumentController;
use App\Http\Controllers\Api\GridController;
use App\Http\Controllers\Api\GridLayoutController;
use App\Http\Controllers\Api\GridElementController;
use App\Http\Controllers\Api\DiscourseController;
use App\Http\Controllers\Api\DiscourseImageController;
use App\Http\Controllers\Api\DiscourseDocumentController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamDocumentController;
use App\Http\Controllers\Api\TeamImageController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\JobDocumentController;
use App\Http\Controllers\Api\JobImageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileImageController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

/**
 * Api routes
 */

Route::middleware('auth:api')->group(function() {
    
  /**
   * News routes
   */
  Route::get('news/get', [NewsController::class, 'get']);
  Route::post('news/create', [NewsController::class, 'store']);
  Route::get('news/edit/{news}', [NewsController::class, 'edit']);
  Route::post('news/update/{news}', [NewsController::class, 'update']);
  Route::get('news/status/{news}', [NewsController::class, 'status']);
  Route::post('news/order', [NewsController::class, 'order']);
  Route::delete('news/destroy/{news}', [NewsController::class, 'destroy']);

  /**
   * Home image routes
   */
  Route::get('home/images/get', [HomeImageController::class, 'get']);
  Route::post('home/image/create', [HomeImageController::class, 'store']);
  Route::get('home/image/edit/{image}', [HomeImageController::class, 'edit']);
  Route::post('home/image/update/{image}', [HomeImageController::class, 'update']);
  Route::get('home/image/status/{image}', [HomeImageController::class, 'status']);
  Route::post('home/image/coords/{homeImage}', [HomeImageController::class, 'coords']);
  Route::delete('home/image/destroy/{image}', [HomeImageController::class, 'destroy']);

  /**
   * Upload routes
   */
  Route::post('media/upload', [MediaController::class, 'upload']);

  /**
   * Settings routes
   */
  Route::get('settings/program', [SettingsController::class, 'program']);
  Route::get('settings/state', [SettingsController::class, 'state']);
  Route::get('settings/authors', [SettingsController::class, 'authors']);
  Route::get('settings/discourseCategories', [SettingsController::class, 'discourseCategories']);
  Route::get('settings/teamCategories', [SettingsController::class, 'teamCategories']);

  /**
   * Project routes
   */
  Route::get('projects/get', [ProjectController::class, 'get']);
  Route::get('project/get/{project}', [ProjectController::class, 'getOne']);
  Route::post('project/create', [ProjectController::class, 'store']);
  Route::get('project/edit/{project}', [ProjectController::class, 'edit']);
  Route::post('project/update/{project}', [ProjectController::class, 'update']);
  Route::get('project/status/{project}', [ProjectController::class, 'status']);
  Route::post('project/order', [ProjectController::class, 'order']);
  Route::delete('project/destroy/{project}', [ProjectController::class, 'destroy']);

  /**
   * ProjectImage routes
   */
  Route::get('project/image/get/{projectId}', [ProjectImageController::class, 'get']);
  Route::get('project/image/status/{id}', [ProjectImageController::class, 'status']);
  Route::post('project/image/coords/{projectImage}', [ProjectImageController::class, 'coords']);
  Route::delete('project/image/destroy/{projectImage}', [ProjectImageController::class, 'destroy']);

  /**
   * ProjectDocument routes
   */
  Route::get('project/document/status/{id}', [ProjectDocumentController::class, 'status']);
  Route::delete('project/document/destroy/{projectFile}', [ProjectDocumentController::class, 'destroy']);

  /**
   * Project grid routes
   */
  Route::get('project/grids/{id}', [GridController::class, 'get']);
  Route::post('project/grids/order', [GridController::class, 'order']);
  Route::get('project/grid/store/{projectId}/{layoutId}', [GridController::class, 'store']);
  Route::delete('project/grid/delete/{id}', [GridController::class, 'destroy']);
  Route::get('project/grid/layouts', [GridLayoutController::class, 'get']);
  Route::get('project/grid/images/{gridId}', [GridElementController::class, 'get']);
  Route::post('project/grid/image/store', [GridElementController::class, 'store']);
  Route::delete('project/grid/image/delete/{id}', [GridElementController::class, 'destroy']);

  /**
   * Discourse routes
   */
  Route::get('discourses/get', [DiscourseController::class, 'get']);
  Route::get('discourse/get/{discourse}', [DiscourseController::class, 'getOne']);
  Route::post('discourse/create', [DiscourseController::class, 'store']);
  Route::get('discourse/edit/{discourse}', [DiscourseController::class, 'edit']);
  Route::post('discourse/update/{discourse}', [DiscourseController::class, 'update']);
  Route::get('discourse/status/{discourse}', [DiscourseController::class, 'status']);
  Route::post('discourse/order', [DiscourseController::class, 'order']);
  Route::delete('discourse/destroy/{discourse}', [DiscourseController::class, 'destroy']);

  /**
   * DiscourseImage routes
   */
  Route::get('discourse/image/get/{discourseId}', [DiscourseImageController::class, 'get']);
  Route::get('discourse/image/status/{id}', [DiscourseImageController::class, 'status']);
  Route::post('discourse/image/coords/{discourseImage}', [DiscourseImageController::class, 'coords']);
  Route::post('discourse/image/order', [DiscourseImageController::class, 'order']);
  Route::delete('discourse/image/destroy/{discourseImage}', [DiscourseImageController::class, 'destroy']);

  /**
   * DiscourseDocument routes
   */
  Route::get('discourse/document/status/{id}', [DiscourseDocumentController::class, 'status']);
  Route::delete('discourse/document/destroy/{discourseFile}', [DiscourseDocumentController::class, 'destroy']);

  /**
   * Team routes
   */
  Route::get('teams/get', [TeamController::class, 'get']);
  Route::get('team/get/{team}', [TeamController::class, 'getOne']);
  Route::post('team/create', [TeamController::class, 'store']);
  Route::get('team/edit/{team}', [TeamController::class, 'edit']);
  Route::post('team/update/{team}', [TeamController::class, 'update']);
  Route::get('team/status/{team}', [TeamController::class, 'status']);
  Route::post('team/order', [TeamController::class, 'order']);
  Route::delete('team/destroy/{team}', [TeamController::class, 'destroy']);

  /**
   * TeamDocument routes
   */
  Route::get('team/document/status/{id}', [TeamDocumentController::class, 'status']);
  Route::delete('team/document/destroy/{teamFile}', [TeamDocumentController::class, 'destroy']);

  /**
   * TeamImage routes
   */
  Route::get('team/images/get', [TeamImageController::class, 'get']);
  Route::post('team/image/create', [TeamImageController::class, 'store']);
  Route::get('team/image/edit/{image}', [TeamImageController::class, 'edit']);
  Route::post('team/image/update/{image}', [TeamImageController::class, 'update']);
  Route::get('team/image/status/{image}', [TeamImageController::class, 'status']);
  Route::post('team/image/coords/{teamImage}', [TeamImageController::class, 'coords']);
  Route::post('team/image/order', [TeamImageController::class, 'order']);
  Route::delete('team/image/destroy/{image}', [TeamImageController::class, 'destroy']);

  /**
   * Job routes
   */
  Route::get('jobs/get', [JobController::class, 'get']);
  Route::get('job/get/{job}', [JobController::class, 'getOne']);
  Route::post('job/create', [JobController::class, 'store']);
  Route::get('job/edit/{job}', [JobController::class, 'edit']);
  Route::post('job/update/{job}', [JobController::class, 'update']);
  Route::get('job/status/{job}', [JobController::class, 'status']);
  Route::post('job/order', [JobController::class, 'order']);
  Route::delete('job/destroy/{job}', [JobController::class, 'destroy']);

  /**
   * JobDocument routes
   */
  Route::get('job/document/status/{id}', [JobDocumentController::class, 'status']);
  Route::delete('job/document/destroy/{jobFile}', [JobDocumentController::class, 'destroy']);

  /**
   * JobImage routes
   */
  Route::get('job/images/get', [JobImageController::class, 'get']);
  Route::post('job/image/create', [JobImageController::class, 'store']);
  Route::get('job/image/edit/{image}', [JobImageController::class, 'edit']);
  Route::post('job/image/update/{image}', [JobImageController::class, 'update']);
  Route::get('job/image/status/{image}', [JobImageController::class, 'status']);
  Route::post('job/image/coords/{jobImage}', [JobImageController::class, 'coords']);
  Route::post('job/image/order', [JobImageController::class, 'order']);
  Route::delete('job/image/destroy/{image}', [JobImageController::class, 'destroy']);


  /**
   * Profile routes
   */
  Route::get('profile/get', [ProfileController::class, 'get']);
  Route::post('profile/create', [ProfileController::class, 'store']);
  Route::get('profile/edit/{profile}', [ProfileController::class, 'edit']);
  Route::post('profile/update/{profile}', [ProfileController::class, 'update']);
  Route::get('profile/status/{profile}', [ProfileController::class, 'status']);

  /**
   * ProfileImage routes
   */
  Route::get('profile/images/get', [ProfileImageController::class, 'get']);
  Route::post('profile/image/create', [ProfileImageController::class, 'store']);
  Route::get('profile/image/edit/{image}', [ProfileImageController::class, 'edit']);
  Route::post('profile/image/update/{image}', [ProfileImageController::class, 'update']);
  Route::get('profile/image/status/{image}', [ProfileImageController::class, 'status']);
  Route::post('profile/image/coords/{profileImage}', [ProfileImageController::class, 'coords']);
  Route::post('profile/image/order', [ProfileImageController::class, 'order']);
  Route::delete('profile/image/destroy/{image}', [ProfileImageController::class, 'destroy']);

  /**
   * Contact routes
   */
  Route::get('contact/get', [ContactController::class, 'get']);
  Route::post('contact/create', [ContactController::class, 'store']);
  Route::get('contact/edit/{contact}', [ContactController::class, 'edit']);
  Route::post('contact/update/{contact}', [ContactController::class, 'update']);

});

/**
 * Auth routes
 */

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout']);
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::post('me', [AuthController::class, 'me']);
});


/**
 * Fallback if no route is defined
 */

Route::fallback(function(){
  return response()->json(
    ['message' => 'Page Not Found. If error persists, contact m@marceli.to'],
    404
  );
});