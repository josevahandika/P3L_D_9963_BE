<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Transaksi;
use Validator;

class TransaksiController extends Controller
{
    //
    public function index(){
        $transaksi = Transaksi::where('total_harga', '!=', 0)->get();

        if(count($transaksi) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $transaksi = Transaksi::find($id);

        if(!is_null($transaksi))
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ],200);

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
       $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_reservasi' => 'required',
        ]);

        $mytime = Carbon::now()->format('dmy');
        $tanggal = Carbon::now()->format('Y-m-d');

        $temp =   Transaksi::where('tanggal_transaksi', $tanggal)
                        ->get();

        $myCount = $temp->count() + 1; 
        $nota = 'AKB-'.$mytime.'-'.$myCount;
        
        $storeData['nomor_transaksi'] = $nota;
        $storeData['tanggal_transaksi'] = $mytime;
        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $transaksi = Transaksi::create($storeData);
        return response([
            'message' => 'Add Transaksi Success',
            'data' => $transaksi,
        ],200);

    }
}