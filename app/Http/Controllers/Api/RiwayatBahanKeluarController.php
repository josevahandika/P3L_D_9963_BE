<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\RiwayatBahanKeluar;
use App\Bahan;
use App\BahanHarian;
use Validator;

class RiwayatBahanKeluarController extends Controller
{
    //
    public function index(){
        $bahankeluar = DB::table('riwayat_bahan_keluars')
                    ->join('bahans','bahans.id','=','riwayat_bahan_keluars.id_bahan')
                    ->select('riwayat_bahan_keluars.id','riwayat_bahan_keluars.jumlah as  jumlah', 'riwayat_bahan_keluars.status as status',
                    'riwayat_bahan_keluars.tanggal as  tanggal', 'bahans.nama_bahan as nama_bahan')
                    ->where('riwayat_bahan_keluars.isDeleted',0)
                    ->get();

        if(count($bahankeluar) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahankeluar
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $bahankeluar = RiwayatBahanKeluar::find($id);

        if(!is_null($bahankeluar))
            return response([
                'message' => 'Retrieve RiwayatBahanKeluar Success',
                'data' => $bahankeluar
            ],200);

        return response([
            'message' => 'RiwayatBahanKeluar Not Found',
            'data' => null
        ],404);
    }

    //method untuk menambah 1 data bahan Keluar baru (create)
    public function store(Request $request) {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'jumlah' => 'required',
            'status' => 'required',
            'tanggal' => 'required',
            'id_bahan' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $bahankeluar = RiwayatBahanKeluar::create($storeData); //menambah data barang Keluar baru
        $bahan = Bahan::find($storeData['id_bahan']);
        $bahan->jumlah_bahan_sisa -= $storeData['jumlah'];
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
                'message' => 'Input Data Bahan Keluar Berhasil',
                'data' => $bahankeluar,
            ],200);
        } //return data bahan Keluar yang telah di edit dalam bentuk json 
        return response([
            'message' => 'Input Data Bahan Keluar Gagal',
            'data' => null,
        ],400); //return message saat bahan Keluar gagal di edit 
    }
}
