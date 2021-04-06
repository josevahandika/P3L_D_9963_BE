<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;

class AuthController extends Controller
{
    //
    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData,[
            // 'name' => 'required',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:6|max:16'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'],401);

            
        $user = Auth::user();
        // if (!$user->verified) {
        //     return response([
        //         'message' => 'Belum Verifikasi Email',
        //     ],400);
        // }
        if($user->status=='Resign')
        {
            return response([
                'message' =>'Akun telah resign',
                'data' => null,
            ],401);
        }

        $token = $user->createToken('Authentication Token')->accessToken; //generate token
        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ],200);

    }

    public function logout()
    { 
        if (Auth::check()) {
        Auth::user()->AauthAccessToken()->delete();

        return response([
            'message' => 'Logout Success',
            'data' => null,
        ]);
        }
    }
}
