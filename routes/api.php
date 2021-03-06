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
Route::get('menu','Api\MenuController@index');
Route::post('detailtransaksi','Api\DetailTransaksiController@store');
Route::group(['middleware'=>'auth:api'], function () {
    // return $request->user();
    Route::post('logout','Api\AuthController@logout');

    //Route untuk karyawan
    Route::post('karyawan','Api\UserController@store');
    Route::get('karyawan','Api\UserController@index');
    Route::get('karyawan/{id}','Api\UserController@show');
    Route::put('hapuskaryawan/{id}','Api\UserController@destroy');
    Route::put('restorekaryawan/{id}','Api\UserController@restore');
    Route::put('karyawan/{id}','Api\UserController@update');

    //Route untuk meja
    Route::post('meja','Api\MejaController@store');
    Route::get('meja','Api\MejaController@index');
    Route::get('mejaKosong','Api\MejaController@indexMejaKosong');
    Route::get('meja/{id}','Api\MejaController@show');
    Route::put('hapusmeja/{id}','Api\MejaController@destroy');
    Route::put('meja/{id}','Api\MejaController@update');

    //Route untuk bahan
    Route::post('bahan','Api\BahanController@store');
    Route::get('bahan','Api\BahanController@index');
    Route::get('bahanKosong','Api\BahanController@indexBahanHabis');
    Route::get('bahan/{id}','Api\BahanController@show');
    Route::put('hapusbahan/{id}','Api\BahanController@destroy');
    Route::put('bahan/{id}','Api\BahanController@update');

    //Route untuk menu
    Route::post('menu','Api\MenuController@store');
    Route::get('menu/{id}','Api\MenuController@show');
    Route::put('hapusmenu/{id}','Api\MenuController@destroy');
    Route::put('menu/{id}','Api\MenuController@update');

    //Route untuk customer
    Route::post('customer','Api\CustomerController@store');
    Route::get('customer','Api\CustomerController@index');
    Route::get('customer/{id}','Api\CustomerController@show');
    Route::put('hapuscustomer/{id}','Api\CustomerController@destroy');
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
    Route::put('detailtransaksi/{id}','Api\DetailTransaksiController@update');
    Route::get('detailtransaksiwaiter','Api\DetailTransaksiController@showWaiter');
    Route::get('detailtransaksichef','Api\DetailTransaksiController@showChef');
    Route::get('detailtransaksi/{id}','Api\DetailTransaksiController@showTransaksi');
    Route::get('detailtransaksifinish/{id}','Api\DetailTransaksiController@showFinish');
    Route::get('getAllItem/{id}','Api\DetailTransaksiController@getAllItem');

    //Route untuk reservasi
    Route::post('reservasiold','Api\ReservasiController@storeOldCust');
    Route::post('reservasinew','Api\ReservasiController@storeNewCust');
    Route::get('reservasi','Api\ReservasiController@index');
    Route::get('reservasi/{id}','Api\ReservasiController@show');
    Route::post('mejareservasi','Api\ReservasiController@showMejaReservasi');
    Route::put('hapusreservasi/{id}','Api\ReservasiController@destroy');
    Route::put('reservasi/{id}','Api\ReservasiController@update');

    //Route untuk transaksi
    Route::post('transaksi','Api\TransaksiController@store');
    Route::get('transaksi','Api\TransaksiController@index');
    Route::get('transaksihistory','Api\TransaksiController@indexHistory');
    Route::get('transaksi/{id}','Api\TransaksiController@show');
    Route::put('transaksiBayar/{id}','Api\TransaksiController@update');
    Route::get('generatePDF/{id}','Api\TransaksiController@generatePDF');
    Route::get('finishMobile/{id}','Api\TransaksiController@finishMobile');
    
 //Route untuk kartu
 Route::get('kartu','Api\KartuController@index');

 //Route untuk bahan harian
 Route::post('bahanharian','Api\BahanHarianController@store');
 Route::get('bahanharian','Api\BahanHarianController@index');
 Route::get('bahanharian/{id}','Api\BahanHarianController@show');

 //Route untuk laporan
 Route::get('menuLaporan','Api\LaporanController@namaMenu');
 Route::get('laporanStok/{bulan}/{idKaryawan}/{id_menu}','Api\LaporanController@laporanStok');
 Route::get('laporanStokBulananCustom/{bulan}/{idKaryawan}/{nama_menu}','Api\LaporanController@laporanStokBulananCustom');
 Route::get('laporanStokCustomAll/{tanggal_awal}/{tanggal_akhir}/{idKaryawan}','Api\LaporanController@laporanStokCustomAll');
 Route::get('laporanStokCustomItem/{tanggal_awal}/{tanggal_akhir}/{idKaryawan}/{nama_menu}','Api\LaporanController@laporanStokCustomItem');
 Route::get('laporanPenjualanItemMenuBulanan/{bulan}/{idKaryawan}','Api\LaporanController@laporanPenjualanItemMenuBulanan');
 Route::get('laporanPenjualanItemMenuTahunan/{tahun}/{idKaryawan}','Api\LaporanController@laporanPenjualanItemMenuTahunan');
 Route::get('laporanPendapatan/{idKaryawan}/{tahun_awal}/{tahun_akhir}','Api\LaporanController@pendapatanLap');
 Route::get('laporanPengeluaran/{idKaryawan}/{tahun_awal}/{tahun_akhir}','Api\LaporanController@pengeluaranLap');
});
