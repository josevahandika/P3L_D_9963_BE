<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Reservasi;
use Validator;

class ReservasiController extends Controller
{
    //
    public function index(){
        $reservasi = Reservasi::where('isDeleted', 0)->get();

        if(count($customer) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $reservasi
            ],200);
        
        
        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $reservasi = Reservasi::find($id);

        if(!is_null($reservasi))
            return response([
                'message' => 'Retrieve Reservasi Success',
                'data' => $reservasi
            ],200);

        return response([
            'message' => 'Reservasi Not Found',
            'data' => null
        ],404);
    }

    //method untuk menambah 1 data reservasi dengan customer baru (create)
    public function storeNewCust(Request $request) {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'tanggal_reservasi' => 'required',
            'sesi_reservasi' => 'required',
            'id_meja' => 'required',
            'id_karyawan' => 'required',
            'nama_customer' => 'required',
            'telepon' => 'required|unique:customers',
            'email' => 'required|unique:customers',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            $error = $validate->errors();
            $errorTelepon = $validate->errors()->first('telepon');
            $errorEmail = $validate->errors()->first('email');
            
            if ($errorTelepon === 'The telepon has already been taken.' ) {                
                return response(['message' => "Nomor Telepon sudah terdaftar!"],400); //return error no telpon sudah terdaftar
            }

            if ($errorEmail === 'The email has already been taken.') {
                return response(['message' => "Email sudah terdaftar!"],400); //return error email sudah terdaftar
            }

            return response(['message' => $validate->errors()], 400); //return error invalid input
        }


        $storeCustomer['nama_customer'] = $storeData['nama_customer'];
        $storeCustomer['telepon'] = $storeData['telepon'];
        $storeCustomer['email'] = $storeData['email'];

        $customer = Customer::create($storeCustomer);
        $tempCustomer = json_decode(json_encode($customer), true);

        $storeReservasi['tanggal_reservasi'] = $storeData['tanggal_reservasi'];
        $storeReservasi['sesi_reservasi'] = $storeData['sesi_reservasi'];
        $storeReservasi['id_customer'] = $tempCustomer['id'];
        $storeReservasi['id_meja'] = $storeData['id_meja'];
        $storeReservasi['id_karyawan'] = $storeData['id_karyawan'];

        $reservasi = Reservasi::create($storeReservasi); //menambah data meja baru
        return response([
            'message' => 'Add Reservasi Success',
            'data' => $reservasi,
        ], 200); //return data reservasi baru dalam bentuk json    
    }

    //method untuk menambah 1 data reservasi dengan customer baru (create)
    public function storeOldCust(Request $request) {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'tanggal_reservasi' => 'required',
            'sesi_reservasi' => 'required',
            'id_meja' => 'required',
            'id_karyawan' => 'required',
            'id_customer' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            $error = $validate->errors();
            $errorTelepon = $validate->errors()->first('telepon');
            $errorEmail = $validate->errors()->first('email');
            
            if ($errorTelepon === 'The telepon has already been taken.' ) {                
                return response(['message' => "Nomor Telepon sudah terdaftar!"],400); //return error no telpon sudah terdaftar
            }

            if ($errorEmail === 'The email has already been taken.') {
                return response(['message' => "Email sudah terdaftar!"],400); //return error email sudah terdaftar
            }

            return response(['message' => $validate->errors()], 400); //return error invalid input
        }


        // $storeCustomer['nama_customer'] = $storeData['nama_customer'];
        // $storeCustomer['telepon'] = $storeData['telepon'];
        // $storeCustomer['email'] = $storeData['email'];

        $customer = Customer::create($storeCustomer);
        $tempCustomer = json_decode(json_encode($customer), true);

        $storeReservasi['tanggal_reservasi'] = $storeData['tanggal_reservasi'];
        $storeReservasi['sesi_reservasi'] = $storeData['sesi_reservasi'];
        $storeReservasi['id_customer'] = $tempCustomer['id'];
        $storeReservasi['id_meja'] = $storeData['id_meja'];
        $storeReservasi['id_karyawan'] = $storeData['id_karyawan'];

        $reservasi = Reservasi::create($storeReservasi); //menambah data meja baru
        return response([
            'message' => 'Add Reservasi Success',
            'data' => $reservasi,
        ], 200); //return data reservasi baru dalam bentuk json    
    }
    
    public function destroy($id){
        $reservasi = Reservasi::find($id);

        if(is_null($reservasi)){
            return response([
                'message' => 'Reservasi Not Found',
                'data' => null
            ],404);
        }

      $reservasi->isDeleted = 1;
      if($reservasi->save()){
            return response([
                'message' => 'Delete Reservasi Success',
                'data' => $reservasi,
            ],200);
        }
      
        return response([
            'message' => 'Delete Reservasi Failed',
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

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

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
