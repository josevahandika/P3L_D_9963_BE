<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Bahan;
use App\BahanHarian;
use Validator;

class BahanHarianController extends Controller
{
    //
    public function index(){
        $bahanharian = DB::table('bahan_harians')
                    ->join('bahans','bahans.id','=','bahan_harians.id_bahan')
                    ->select('bahan_harians.id', 'bahan_harians.tanggal as  tanggal', 'bahan_harians.jumlah as  jumlah', 'bahans.nama_bahan as nama_bahan')
                    ->get();

        if(count($bahanharian) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahanharian
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $bahanharian = BahanHarian::find($id);

        if(!is_null($bahanharian))
            return response([
                'message' => 'Retrieve BahanHarian Success',
                'data' => $bahanharian
            ],200);

        return response([
            'message' => 'BahanHarian Not Found',
            'data' => null
        ],404);
    }

    //method untuk menambah 1 data bahan Keluar baru (create)
    public function store(Request $request) {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_bahan' => 'required',
            'tanggal' => 'required',
            'jumlah' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $bahanharian = BahanHarian::create($storeData); //menambah data barang Keluar baru
        $bahan = Bahan::find($storeData['id_bahan']);
        $bahan->jumlah_bahan_sisa -= $storeData['jumlah'];
        if($bahan->save()) {
            //insert ke log bahan
            $storeHarian['id_bahan'] = $storeData['id_bahan'];
            $storeHarian['tanggal'] = $storeData['tanggal'];
            $storeHarian['jumlah'] = $bahan->jumlah_bahan_sisa;

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
                'message' => 'Input Data Bahan Harian Berhasil',
                'data' => $bahanharian,
            ],200);
        } //return data bahan Harian yang telah di edit dalam bentuk json 
        return response([
            'message' => 'Input Data Bahan Harian Gagal',
            'data' => null,
        ],400); //return message saat bahan Harian gagal di edit 
    }
}
