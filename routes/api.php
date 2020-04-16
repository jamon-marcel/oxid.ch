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
  Route::delete('news/destroy/{news}', 'Api\NewsController@destroy');

  /**
   * Home image routes
   */
  Route::get('images/get', 'Api\HomeImageController@get');
  Route::post('image/create', 'Api\HomeImageController@store');
  Route::get('image/edit/{image}', 'Api\HomeImageController@edit');
  Route::post('image/update/{image}', 'Api\HomeImageController@update');
  Route::get('image/status/{image}', 'Api\HomeImageController@status');
  Route::delete('image/destroy/{image}', 'Api\HomeImageController@destroy');

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


