<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\RiwayatBahanMasuk;
use App\Bahan;
use App\BahanHarian;
use Validator;

class RiwayatBahanMasukController extends Controller
{
    //
    public function index(){
        $bahanmasuk = DB::table('riwayat_bahan_masuks')
                    ->join('bahans','bahans.id','=','riwayat_bahan_masuks.id_bahan')
                    ->select('riwayat_bahan_masuks.id','riwayat_bahan_masuks.jumlah as  jumlah', 'riwayat_bahan_masuks.harga as harga',
                    'riwayat_bahan_masuks.tanggal as  tanggal', 'bahans.nama_bahan as nama_bahan')
                    ->where('riwayat_bahan_masuks.isDeleted',0)
                    ->get();

        if(count($bahanmasuk) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahanmasuk
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $bahanmasuk = RiwayatBahanMasuk::find($id);

        if(!is_null($bahanmasuk))
            return response([
                'message' => 'Retrieve RiwayatBahanMasuk Success',
                'data' => $bahanmasuk
            ],200);

        return response([
            'message' => 'RiwayatBahanMasuk Not Found',
            'data' => null
        ],404);
    }

    //method untuk menambah 1 data bahan masuk baru (create)
    public function store(Request $request) {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'jumlah' => 'required',
            'harga' => 'required',
            'tanggal' => 'required',
            'id_bahan' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $bahanmasuk = RiwayatBahanMasuk::create($storeData); //menambah data barang masuk baru
        $bahan = Bahan::find($storeData['id_bahan']);
        $bahan->jumlah_bahan_sisa += $storeData['jumlah'];
        if($bahan->save()) {
            //insert ke log bahan
            $storeHarian['jumlah'] = $bahan->jumlah_bahan_sisa;
            $storeHarian['tanggal'] = $storeData['tanggal'];
            $storeHarian['id_bahan'] = $storeData['id_bahan'];

            $tempHarian = DB::table('bahan_harians')
                            ->where('tanggal', $storeHarian['tanggal'])
                            ->where('id_bahan', $storeHarian['id_bahan'])
                            ->first();
            
            if (is_null($tempHarian)) {
                $harian = BahanHarian::create($storeHarian);
            } else {
                DB::table('bahan_harians')
                    ->where('id', $tempHarian->id)
                    ->update(array('jumlah' => $bahan->jumlah_bahan_sisa));
            }
            return response([
                'message' => 'Input Data Bahan Masuk Berhasil',
                'data' => $bahanmasuk,
            ],200);
        } //return data bahan masuk yang telah di edit dalam bentuk json 
        return response([
            'message' => 'Input Data Bahan Masuk Gagal',
            'data' => null,
        ],400); //return message saat bahan masuk gagal di edit 
    }

}
