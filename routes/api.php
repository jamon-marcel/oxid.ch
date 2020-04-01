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

  /**
   * Project routes
   */
  Route::get('projects/get', 'Api\ProjectController@get');
  Route::post('project/create', 'Api\ProjectController@store');
  Route::get('project/edit/{project}', 'Api\ProjectController@edit');
  Route::post('project/update/{project}', 'Api\ProjectController@update');
  Route::get('project/status/{project}', 'Api\ProjectController@status');
  Route::post('project/order', 'Api\ProjectController@order');
  Route::delete('project/destroy/{project}', 'Api\ProjectController@destroy');

  /**
   * ProjectImage routes
   */
  Route::get('project/image/status/{projectImage}', 'Api\ProjectImageController@status');
  Route::delete('project/image/destroy/{projectImage}', 'Api\ProjectImageController@destroy');

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


