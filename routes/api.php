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

    //Route untuk customer
    Route::post('customer','Api\CustomerController@store');
    Route::get('customer','Api\CustomerController@index');
    Route::get('customer/{id}','Api\CustomerController@show');
    Route::get('hapuscustomer/{id}','Api\CustomerController@destroy');
    Route::put('customer/{id}','Api\CustomerController@update');

    //Route untuk riwayat masuk
    Route::post('riwayatbahanmasuk','Api\RiwayatBahanMasukController@store');
    Route::get('riwayatbahanmasuk','Api\RiwayatBahanMasukController@index');
    Route::get('riwayatbahanmasuk/{id}','Api\RiwayatBahanMasukController@show');

    //Route untuk riwayat keluar
    Route::post('riwayatbahankeluar','Api\RiwayatBahanKeluarController@store');
    Route::get('riwayatbahankeluar','Api\RiwayatBahanKeluarController@index');
    Route::get('riwayatbahankeluar/{id}','Api\RiwayatBahanKeluarController@show');

    //Route untuk detail transaksi
    Route::post('detailtransaksi','Api\DetailTransaksiController@store');
    Route::put('detailtransaksi/{id}','Api\DetailTransaksiController@update');
    Route::get('detailtransaksiwaiter','Api\DetailTransaksiController@showWaiter');
    Route::get('detailtransaksichef','Api\DetailTransaksiController@showChef');

    //Route untuk reservasi
    Route::post('reservasiold','Api\ReservasiController@storeOldCust');
    Route::post('reservasinew','Api\ReservasiController@storeNewCust');
    Route::get('reservasi','Api\ReservasiController@index');
    Route::get('reservasi/{id}','Api\ReservasiController@show');
    Route::get('hapusreservasi/{id}','Api\ReservasiController@destroy');
    Route::put('reservasi/{id}','Api\ReservasiController@update');

    //Route untuk transaksi
    Route::post('transaksi','Api\TransaksiController@store');
    Route::get('transaksi','Api\TransaksiController@index');
    Route::get('transaksi/{id}','Api\TransaksiController@show');
    Route::put('transaksi/{id}','Api\TransaksiController@update');
});
