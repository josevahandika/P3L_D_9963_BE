<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
// Route::middleware[('auth:api')->get('/user', function (Request $request) {
//     // return $request->user();
//     Route::post('logout','Api\AuthController@logout');
// });

Route::post('login','Api\AuthController@login');
Route::group(['middleware'=>'auth:api'], function () {
    // return $request->user();
    Route::post('logout','Api\AuthController@logout');

    //Route untuk karyawan
    Route::post('karyawan','Api\UserController@store');
    Route::get('karyawan','Api\UserController@index');
    Route::get('karyawan/{id}','Api\UserController@show');
    Route::put('hapuskaryawan/{id}','Api\UserController@destroy');
    Route::put('karyawan/{id}','Api\UserController@update');

    //Route untuk meja
    Route::post('meja','Api\MejaController@store');
    Route::get('meja','Api\MejaController@index');
    Route::get('meja/{id}','Api\MejaController@show');
    Route::get('hapusmeja/{id}','Api\MejaController@destroy');
    Route::put('meja/{id}','Api\MejaController@update');

    //Route untuk bahan
    Route::post('bahan','Api\BahanController@store');
    Route::get('bahan','Api\BahanController@index');
    Route::get('bahan/{id}','Api\BahanController@show');
    Route::get('hapusbahan/{id}','Api\BahanController@destroy');
    Route::put('bahan/{id}','Api\BahanController@update');

    //Route untuk menu
    Route::post('menu','Api\MenuController@store');
    Route::get('menu','Api\MenuController@index');
    Route::get('menu/{id}','Api\MenuController@show');
    Route::get('hapusmenu/{id}','Api\MenuController@destroy');
    Route::put('menu/{id}','Api\MenuController@update');
});
