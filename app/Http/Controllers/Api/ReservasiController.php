<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Reservasi;
use App\Customer;
use App\Meja;
use Validator;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    //
    public function index(){
        $reservasi = DB::table('reservasis')
                    ->join('customers','customers.id','=','reservasis.id_customer')
                    ->join('mejas','mejas.id','=','reservasis.id_meja')
                    ->join('users','users.id','=','reservasis.id_karyawan')
                    ->select('reservasis.id','reservasis.tanggal_reservasi as  tanggal_reservasi', 'reservasis.sesi_reservasi as sesi_reservasi',
                    'reservasis.status_reservasi as  status_reservasi', 'customers.nama_customer as nama_customer',  'customers.telepon as telepon', 'customers.email as email', 'mejas.nomor_meja as nomor_meja',
                    'users.name as nama_karyawan')
                    ->where('reservasis.isDeleted',0)
                    ->get();
        if(count($reservasi) > 0)
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

    public function showMejaReservasi(Request $request){
        $mejas = Meja::where('isDeleted', 0)->get();

        $tanggal_reservasi = $request->tanggal_reservasi;
        $sesi_reservasi = $request->sesi_reservasi;

        if(count($mejas) == 0)
            return response([
                'message' => 'Meja Not Found',
                'data' => null
            ],404);

        foreach($mejas as $meja)
        {
            $meja->status = "Kosong";
            $reservasi = Reservasi::where('isDeleted', 0)
                        ->where('tanggal_reservasi', $tanggal_reservasi)
                        ->where('sesi_reservasi', $sesi_reservasi)
                        ->where('id_meja', $meja->id)
            ->first();

            if(!is_null($reservasi)) {
                $meja->status = "Isi";
            }
        }
        return response([
            'message' => 'Retrieve Meja Success',
            'data' => $mejas,
        ],200);
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
            'telepon' => 'required',
            'email' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
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

        $reservasi = Reservasi::create($storeReservasi); //menambah data reservasi baru
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
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }


        // $storeCustomer['nama_customer'] = $storeData['nama_customer'];
        // $storeCustomer['telepon'] = $storeData['telepon'];
        // $storeCustomer['email'] = $storeData['email'];

        // // $customer = Customer::create($storeCustomer);
        // $tempCustomer = json_decode(json_encode($customer), true);

        $storeReservasi['tanggal_reservasi'] = $storeData['tanggal_reservasi'];
        $storeReservasi['sesi_reservasi'] = $storeData['sesi_reservasi'];
        $storeReservasi['id_customer'] = $storeData['id_customer'];
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
            $reservasi = Reservasi::find($id);
            if(is_null($reservasi)){
                return response([
                    'message' => 'Reservasi Not Found',
                    'data' => null
                ],404);
            }

            $updateData = $request->all();
            $validate = Validator::make($updateData, [
                'tanggal_reservasi' => 'required',
                'sesi_reservasi' => 'required',
                'id_meja' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

       $reservasi->tanggal_reservasi = $updateData['tanggal_reservasi'];
       $reservasi->sesi_reservasi = $updateData['sesi_reservasi'];
       $reservasi->id_meja = $updateData['id_meja'];

        if($reservasi->save()){
            return response([
                'message' => 'Update Reservasi Success',
                'data' => $reservasi,
            ],200);
        }
        return response([
            'message' => 'Update Reservasi Failed',
            'data' => null,
        ],400);
    }
}
