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

Route::group(['middleware' => 'api'], function () {
    Route::post('auth/login', '\App\Backend\Http\Controllers\AuthController@login');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('auth/user', '\App\Backend\Http\Controllers\AuthController@user');
        Route::post('auth/logout', '\App\Backend\Http\Controllers\AuthController@logout');
    });
});


Route::get(
    'rules/import', 
    '\App\HotRoll\Http\Controllers\RulesImportController@importRules'
);

Route::post(
    'outputs/shift', 
    '\App\HotRoll\Http\Controllers\ProductionOutputController@calculateShiftOutput'
);

Route::get(
    'outputs/shift', 
    '\App\HotRoll\Http\Controllers\ProductionOutputController@calculateShiftOutput'
);