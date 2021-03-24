<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;

class UserController extends Controller
{
    //
    public function index(){
        $user = User::all();

        if(count($user) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $user
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $user = User::find($id);

        if(!is_null($user))
            return response([
                'message' => 'Retrieve Karyawan Success',
                'data' => $user
            ],200);

        return response([
            'message' => 'Karyawan Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'name' => 'required',
            'jabatan' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|max:16',
            'telepon' => 'required',
            'tanggal_bergabung' => 'required',
            'jenis_kelamin' => 'required',

        ]);

            if ($validate->fails()) {
                $error = $validate->errors()->first();
    
                if ($error === 'The email has already been taken.') {
                    return response(['message' => "Email Telah Digunakan!"],400); //return error email sudah ada
                }
    
                return response(['message' => $validate->errors()], 400); //return error invalid input
            }

        $storeData['password'] = bcrypt($request->password);

        $user = User::create($storeData);
        return response([
            'message' => 'Add Karyawan Success',
            'data' => $user,
        ],200);
    }

    public function destroy($id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message' => 'Karyawan Not Found',
                'data' => null
            ],404);
        }

        $user->status = 'Resign';
        if($user->save()){
            return response([
                'message' => 'Delete Karyawan Success',
                'data' => $user,
            ],200);
        }
        return response([
            'message' => 'Delete Karyawan Failed',
            'data' => null,
        ],400);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if(is_null($user)){
            return response([
                'message' => 'Karyawan Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'name' => 'required',
            'jabatan' => 'required',
            'status' => 'required',
            'telepon' => 'required',
            'tanggal_bergabung' => 'required',
            'jenis_kelamin' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $user->name = $updateData['name'];
        $user->jabatan = $updateData['jabatan'];
        $user->status = $updateData['status'];
        $user->telepon = $updateData['telepon'];
        $user->tanggal_bergabung = $updateData['tanggal_bergabung'];
        $user->jenis_kelamin = $updateData['jenis_kelamin'];

        if($user->save()){
            return response([
                'message' => 'Update Karyawan Success',
                'data' => $user,
            ],200);
        }
        return response([
            'message' => 'Update Karyawan Failed',
            'data' => null,
        ],400);
    }
}
