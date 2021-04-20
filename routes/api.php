<?php

use Illuminate\Http\Request;

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
  Route::get('news/get', 'Api\NewsController@get');
  Route::post('news/create', 'Api\NewsController@store');
  Route::get('news/edit/{news}', 'Api\NewsController@edit');
  Route::post('news/update/{news}', 'Api\NewsController@update');
  Route::get('news/status/{news}', 'Api\NewsController@status');
  Route::post('news/order', 'Api\NewsController@order');
  Route::delete('news/destroy/{news}', 'Api\NewsController@destroy');

  /**
   * Home image routes
   */
  Route::get('home/images/get', 'Api\HomeImageController@get');
  Route::post('home/image/create', 'Api\HomeImageController@store');
  Route::get('home/image/edit/{image}', 'Api\HomeImageController@edit');
  Route::post('home/image/update/{image}', 'Api\HomeImageController@update');
  Route::get('home/image/status/{image}', 'Api\HomeImageController@status');
  Route::post('home/image/coords/{homeImage}', 'Api\HomeImageController@coords');
  Route::delete('home/image/destroy/{image}', 'Api\HomeImageController@destroy');

  /**
   * Upload routes
   */
  Route::post('media/upload','Api\MediaController@upload');

  /**
   * Settings routes
   */
  Route::get('settings/program', 'Api\SettingsController@program');
  Route::get('settings/state', 'Api\SettingsController@state');
  Route::get('settings/authors', 'Api\SettingsController@authors');
  Route::get('settings/discourseCategories', 'Api\SettingsController@discourseCategories');
  Route::get('settings/teamCategories', 'Api\SettingsController@teamCategories');

  /**
   * Project routes
   */
  Route::get('projects/get', 'Api\ProjectController@get');
  Route::get('project/get/{project}', 'Api\ProjectController@getOne');
  Route::post('project/create', 'Api\ProjectController@store');
  Route::get('project/edit/{project}', 'Api\ProjectController@edit');
  Route::post('project/update/{project}', 'Api\ProjectController@update');
  Route::get('project/status/{project}', 'Api\ProjectController@status');
  Route::post('project/order', 'Api\ProjectController@order');
  Route::delete('project/destroy/{project}', 'Api\ProjectController@destroy');

  /**
   * ProjectImage routes
   */
  Route::get('project/image/get/{projectId}', 'Api\ProjectImageController@get');
  Route::get('project/image/status/{id}', 'Api\ProjectImageController@status');
  Route::post('project/image/coords/{projectImage}', 'Api\ProjectImageController@coords');
  Route::delete('project/image/destroy/{projectImage}', 'Api\ProjectImageController@destroy');

  /**
   * ProjectDocument routes
   */
  Route::get('project/document/status/{id}', 'Api\ProjectDocumentController@status');
  Route::delete('project/document/destroy/{projectFile}', 'Api\ProjectDocumentController@destroy');

  /**
   * Project grid routes
   */
  Route::get('project/grids/{id}', 'Api\GridController@get');
  Route::post('project/grids/order', 'Api\GridController@order');
  Route::get('project/grid/store/{projectId}/{layoutId}', 'Api\GridController@store');
  Route::delete('project/grid/delete/{id}', 'Api\GridController@destroy');
  Route::get('project/grid/layouts', 'Api\GridLayoutController@get');
  Route::get('project/grid/images/{gridId}', 'Api\GridElementController@get');
  Route::post('project/grid/image/store', 'Api\GridElementController@store');
  Route::delete('project/grid/image/delete/{id}', 'Api\GridElementController@destroy');

  /**
   * Discourse routes
   */
  Route::get('discourses/get', 'Api\DiscourseController@get');
  Route::get('discourse/get/{discourse}', 'Api\DiscourseController@getOne');
  Route::post('discourse/create', 'Api\DiscourseController@store');
  Route::get('discourse/edit/{discourse}', 'Api\DiscourseController@edit');
  Route::post('discourse/update/{discourse}', 'Api\DiscourseController@update');
  Route::get('discourse/status/{discourse}', 'Api\DiscourseController@status');
  Route::post('discourse/order', 'Api\DiscourseController@order');
  Route::delete('discourse/destroy/{discourse}', 'Api\DiscourseController@destroy');

  /**
   * DiscourseImage routes
   */
  Route::get('discourse/image/get/{discourseId}', 'Api\DiscourseImageController@get');
  Route::get('discourse/image/status/{id}', 'Api\DiscourseImageController@status');
  Route::post('discourse/image/coords/{discourseImage}', 'Api\DiscourseImageController@coords');
  Route::post('discourse/image/order', 'Api\DiscourseImageController@order');
  Route::delete('discourse/image/destroy/{discourseImage}', 'Api\DiscourseImageController@destroy');

  /**
   * DiscourseDocument routes
   */
  Route::get('discourse/document/status/{id}', 'Api\DiscourseDocumentController@status');
  Route::delete('discourse/document/destroy/{discourseFile}', 'Api\DiscourseDocumentController@destroy');

  /**
   * Team routes
   */
  Route::get('teams/get', 'Api\TeamController@get');
  Route::get('team/get/{team}', 'Api\TeamController@getOne');
  Route::post('team/create', 'Api\TeamController@store');
  Route::get('team/edit/{team}', 'Api\TeamController@edit');
  Route::post('team/update/{team}', 'Api\TeamController@update');
  Route::get('team/status/{team}', 'Api\TeamController@status');
  Route::post('team/order', 'Api\TeamController@order');
  Route::delete('team/destroy/{team}', 'Api\TeamController@destroy');

  /**
   * TeamDocument routes
   */
  Route::get('team/document/status/{id}', 'Api\TeamDocumentController@status');
  Route::delete('team/document/destroy/{teamFile}', 'Api\TeamDocumentController@destroy');

  /**
   * TeamImage routes
   */
  Route::get('team/images/get', 'Api\TeamImageController@get');
  Route::post('team/image/create', 'Api\TeamImageController@store');
  Route::get('team/image/edit/{image}', 'Api\TeamImageController@edit');
  Route::post('team/image/update/{image}', 'Api\TeamImageController@update');
  Route::get('team/image/status/{image}', 'Api\TeamImageController@status');
  Route::post('team/image/coords/{teamImage}', 'Api\TeamImageController@coords');
  Route::post('team/image/order', 'Api\TeamImageController@order');
  Route::delete('team/image/destroy/{image}', 'Api\TeamImageController@destroy');

  /**
   * Job routes
   */
  Route::get('jobs/get', 'Api\JobController@get');
  Route::get('job/get/{job}', 'Api\JobController@getOne');
  Route::post('job/create', 'Api\JobController@store');
  Route::get('job/edit/{job}', 'Api\JobController@edit');
  Route::post('job/update/{job}', 'Api\JobController@update');
  Route::get('job/status/{job}', 'Api\JobController@status');
  Route::post('job/order', 'Api\JobController@order');
  Route::delete('job/destroy/{job}', 'Api\JobController@destroy');

  /**
   * JobDocument routes
   */
  Route::get('job/document/status/{id}', 'Api\JobDocumentController@status');
  Route::delete('job/document/destroy/{jobFile}', 'Api\jobDocumentController@destroy');

  /**
   * JobImage routes
   */
  Route::get('job/images/get', 'Api\JobImageController@get');
  Route::post('job/image/create', 'Api\JobImageController@store');
  Route::get('job/image/edit/{image}', 'Api\JobImageController@edit');
  Route::post('job/image/update/{image}', 'Api\JobImageController@update');
  Route::get('job/image/status/{image}', 'Api\JobImageController@status');
  Route::post('job/image/coords/{jobImage}', 'Api\JobImageController@coords');
  Route::post('job/image/order', 'Api\JobImageController@order');
  Route::delete('job/image/destroy/{image}', 'Api\JobImageController@destroy');


  /**
   * Profile routes
   */
  Route::get('profile/get', 'Api\ProfileController@get');
  Route::post('profile/create', 'Api\ProfileController@store');
  Route::get('profile/edit/{profile}', 'Api\ProfileController@edit');
  Route::post('profile/update/{profile}', 'Api\ProfileController@update');
  Route::get('profile/status/{profile}', 'Api\ProfileController@status');

  /**
   * ProfileImage routes
   */
  Route::get('profile/images/get', 'Api\ProfileImageController@get');
  Route::post('profile/image/create', 'Api\ProfileImageController@store');
  Route::get('profile/image/edit/{image}', 'Api\ProfileImageController@edit');
  Route::post('profile/image/update/{image}', 'Api\ProfileImageController@update');
  Route::get('profile/image/status/{image}', 'Api\ProfileImageController@status');
  Route::post('profile/image/coords/{profileImage}', 'Api\ProfileImageController@coords');
  Route::post('profile/image/order', 'Api\ProfileImageController@order');
  Route::delete('profile/image/destroy/{image}', 'Api\ProfileImageController@destroy');

  /**
   * Contact routes
   */
  Route::get('contact/get', 'Api\ContactController@get');
  Route::post('contact/create', 'Api\ContactController@store');
  Route::get('contact/edit/{contact}', 'Api\ContactController@edit');
  Route::post('contact/update/{contact}', 'Api\ContactController@update');

});

/**
 * Auth routes
 */

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
  Route::post('login', 'AuthController@login');
  Route::post('logout', 'AuthController@logout');
  Route::post('refresh', 'AuthController@refresh');
  Route::post('me', 'AuthController@me');
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


