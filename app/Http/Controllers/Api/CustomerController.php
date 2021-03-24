<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Customer;
use Validator;

class CustomerController extends Controller
{
    //
    public function index(){
        $customer = Customer::where('isDeleted', 0)->get();

        if(count($customer) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $customer
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $customer = Customer::find($id);

        if(!is_null($customer))
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $customer
            ],200);

        return response([
            'message' => 'Customer Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_customer' => 'required',
            'telepon' => 'required',
            'email' => 'required',
        ]);
        
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input        
        }
        $customer = Customer::create($storeData);
            return response([  
                'message' => 'Insert Customer Success!',
                'data' => $customer,
            ],200);
    }

    public function destroy($id){
        $customer = Customer::find($id);

        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ],404);
        }

      $customer->isDeleted = 1;
      if($customer->save()){
            return response([
                'message' => 'Delete Customer Success',
                'data' => $customer,
            ],200);
        }
      
        return response([
            'message' => 'Delete customer Failed',
            'data' => null,
        ],400);
    }

    public function update(Request $request, $id){
        $customer = Customer::find($id);
        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_customer' => 'required',
            'telepon' => 'required',
            'email' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $customer->nama_customer = $updateData['nama_customer'];
        $customer->telepon = $updateData['telepon'];
        $customer->email = $updateData['email'];

        if($customer->save()){
            return response([
                'message' => 'Update Customer Success',
                'data' => $customer,
            ],200);
        }
        return response([
            'message' => 'Update Customer Failed',
            'data' => null,
        ],400);
    }
}
