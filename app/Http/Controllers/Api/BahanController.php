<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Bahan;
use Validator;

class BahanController extends Controller
{
    //
    public function index(){
        $bahan = DB::table('bahans')
        ->join('menus','menus.id_bahan','=','bahans.id')
        ->select('bahans.*','menus.kategori')
        ->where('bahans.isDeleted','=',0)
        ->get();

        if(count($bahan) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahan
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }
    public function indexBahanHabis(){
        $bahan = Bahan::where('isDeleted', 0)
                    ->where('jumlah_bahan_sisa', '=', '0')->get();

        if(count($bahan) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahan
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }
    
    public function show($id){
        $bahan = Bahan::find($id);

        if(!is_null($bahan))
            return response([
                'message' => 'Retrieve Bahan Success',
                'data' => $bahan
            ],200);

        return response([
            'message' => 'Bahan Not Found',
            'data' => null
        ],404);
    }


    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_bahan' => 'required|unique:bahans',
            'unit' => 'required',
        ]);
        
        if ($validate->fails()) {
            $error = $validate->errors()->first();
            
            if ($error === 'The nama bahan has already been taken.') {
                $tempBahan = Bahan::where('nama_bahan', $storeData['nama_bahan'])->first();
                if($tempBahan->isDeleted === 1)
                {
                    $tempBahan->isDeleted = 0;
                    $tempBahan->unit = $storeData['unit'];
                    $tempBahan->jumlah_bahan_sisa = 0;
                    if($tempBahan->save()){
                        return response([
                            'message' => 'Insert Bahan Success!',
                            'data' => $tempBahan,
                        ],200);
                    }
                }
            }
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $bahan = Bahan::create($storeData); //menambah data product baru
        return response([
            'message' => 'Add Bahan Success',
            'data' => $bahan,
        ],200); //return data product baru dalam bentuk json
    }

    public function destroy($id){
        $bahan = Bahan::find($id);

        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Not Found',
                'data' => null
            ],404);
        }

      $bahan->isDeleted = 1;
      if($bahan->save()){
            return response([
                'message' => 'Delete Bahan Success',
                'data' => $bahan,
            ],200);
        }
      
        return response([
            'message' => 'Delete Bahan Failed',
            'data' => null,
        ],400);
    }

    public function update(Request $request, $id){
        $bahan = Bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_bahan' => [Rule::unique('bahans')->ignore($bahan),'required'],
            'unit' => 'required',
        ]);

        if($validate->fails()){
        $error = $validate->errors()->first();

        if($error == 'The nama bahan has already been taken.')
        {
            $tempBahan = Bahan::where('nama_bahan', $updateData['nama_bahan'])->first();
            if($tempBahan->isDeleted == 1){
                return response([
                    'message' => 'Silakan Input Ulang Nama Bahan',
                    'data' => null
                ],400);
            } else{
                return response([
                    'message' => 'Nama Bahan Sudah Ada',
                    'data' => null
                ],400);
            }
        }
            return response(['message' => $validate->errors()],400);
    }
       $bahan->nama_bahan = $updateData['nama_bahan'];
       $bahan->unit = $updateData['unit'];

        if($bahan->save()){
            return response([
                'message' => 'Update Bahan Success',
                'data' => $bahan,
            ],200);
        }
        return response([
            'message' => 'Update Bahan Failed',
            'data' => null,
        ],400);
    }
}
