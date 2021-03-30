<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Kartu;
use Validator;
class KartuController extends Controller
{
    //
    public function index(){
        $kartu = Kartu::where('isDeleted', 0)->get();

        if(count($kartu) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $kartu
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }
}
