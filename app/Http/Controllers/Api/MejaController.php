<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Meja;
use Validator;

class MejaController extends Controller
{
    //
    public function index(){
        $meja = Meja::where('isDeleted', 0)->get();

        if(count($meja) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $meja
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $meja = Meja::find($id);

        if(!is_null($meja))
            return response([
                'message' => 'Retrieve Meja Success',
                'data' => $meja
            ],200);

        return response([
            'message' => 'Meja Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $tempMeja = null;
        $validate = Validator::make($storeData, [
            'nomor_meja' => 'required|unique:mejas',
        ]);
        
        if ($validate->fails()) {
            $error = $validate->errors()->first();
            
            if ($error === 'The nomor meja has already been taken.') {
                $tempMeja = Meja::where('nomor_meja', $storeData['nomor_meja'])->first();
                if($tempMeja->isDeleted == 1)
                {
                    $tempMeja->isDeleted = 0;
                    if($tempMeja->save()){
                        return response([
                            'message' => 'Insert Meja Success!',
                            'data' => $tempMeja,
                        ],200);
                    }
                }
                else{
                    return response([
                        'message' => 'Meja Sudah Ada!',
                        'data' => $null,
                    ],404);
                }
            }
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }


        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $meja = Meja::create($storeData);
        return response([
            'message' => 'Add Meja Success',
            'data' => $meja,
        ],200);
    }

    public function destroy($id){
        $meja = Meja::find($id);

        if(is_null($meja)){
            return response([
                'message' => 'Meja Not Found',
                'data' => null
            ],404);
        }

      $meja->isDeleted = 1;
      if($meja->save()){
            return response([
                'message' => 'Delete Meja Success',
                'data' => $meja,
            ],200);
        }
      
        return response([
            'message' => 'Delete Meja Failed',
            'data' => null,
        ],400);
    }

    public function update(Request $request, $id){
        $meja = Meja::find($id);
        if(is_null($meja)){
            return response([
                'message' => 'Meja Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nomor_meja' => [Rule::unique('mejas')->ignore($meja),'required'],
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $meja->nomor_meja = $updateData['nomor_meja'];

        if($meja->save()){
            return response([
                'message' => 'Update Meja Success',
                'data' => $meja,
            ],200);
        }
        return response([
            'message' => 'Update Meja Failed',
            'data' => null,
        ],400);
    }
}
