<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Transaksi;
use App\Reservasi;
use App\Kartu;
use App\Meja;
use Validator;

class TransaksiController extends Controller
{
    //
    public function index(){
        $transaksi = DB::table('transaksis')
                    ->join('reservasis','reservasis.id','=','transaksis.id_reservasi')
                    ->join('users','users.id', '=', 'reservasis.id_karyawan')
                    ->join('customers','customers.id', '=', 'reservasis.id_meja')
                    ->join('mejas','mejas.id', '=', 'reservasis.id_meja')
                    ->select('transaksis.*','users.name','mejas.nomor_meja','customers.nama_customer')
                    ->where('transaksis.metode_pembayaran',null)
                    ->get();

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

    public function indexHistory(){
        $transaksi = DB::table('transaksis')
                    ->join('reservasis','reservasis.id','=','transaksis.id_reservasi')
                    ->join('users','users.id', '=', 'reservasis.id_karyawan')
                    ->join('customers','customers.id', '=', 'reservasis.id_meja')
                    ->join('mejas','mejas.id', '=', 'reservasis.id_meja')
                    ->select(DB::raw('transaksis.*, (transaksis.total_harga*5/100) as pajakservice, (transaksis.total_harga*10/100) as pajaktax,
                    (transaksis.total_harga*115/100) as total, users.name,mejas.nomor_meja,customers.nama_customer'))
                    ->where('transaksis.metode_pembayaran','!=',null)
                    ->get();

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
       $meja = Meja::where('nomor_meja', '=', $storeData['nomor_meja']) ->first();
       
       if($meja->status=='Isi')
       {
           return response([
            'message' => 'Meja Sudah Terisi',
            'data' => null
           ],404);
       }

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
        $storeData['tanggal_transaksi'] = $tanggal;
        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $reservasi = DB::table('reservasis')
                    ->join('customers','customers.id','=','reservasis.id_customer')
                    ->join('mejas','mejas.id','=','reservasis.id_meja')
                    ->join('users','users.id','=','reservasis.id_karyawan')
                    ->where('reservasis.id',$storeData['id_reservasi'])
                    ->select('reservasis.*', 'customers.nama_customer as nama_customer', 'mejas.nomor_meja as nomor_meja',
                    'users.name as nama_karyawan')
                    ->first();

        $transaksi = Transaksi::create($storeData);
        $qrcode['id_transaksi']=$transaksi->id;
        $qrcode['nama_customer']=$reservasi->nama_customer;
        $qrcode['nomor_meja']=$reservasi->nomor_meja;
        $qrcode['tanggal'] = Carbon::now()->format('dmy');
        $qrcode['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
        $dateandtime=Carbon::now();
        $qrcode['waktu'] = $dateandtime->format('Hi');
        $qrcode['nama_karyawan'] = $reservasi->nama_karyawan;

        $reservasi = Reservasi::find($storeData['id_reservasi']);
        $reservasi->status_reservasi = "Finished";
        $meja->status = "Isi";
        $reservasi->save();
        $meja->save();

        return response([
            'message' => 'Add Transaksi Success',
            'data' => $qrcode,
        ],200);

    }

    public function update(Request $request, $id){
        
        $transaksi = Transaksi::find($id);
        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi Tidak Ditemukan',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        if($updateData['metode_pembayaran']==='Non Tunai')
        {
            $transaksi->nomor_kartu = $updateData['nomor_kartu'];
            $transaksi->kode_verifikasi = $updateData['kode_verifikasi'];
            $insertKartu['nomor_kartu'] = $updateData['nomor_kartu'];
            $insertKartu['jenis_kartu'] = $updateData['jenis_kartu'];
            $insertKartu['tanggal_kadaluarsa'] = $updateData['tanggal_kadaluarsa'];
            if($insertKartu['jenis_kartu']==='Kredit')
            {
                $insertKartu['nama_pemilik'] = $updateData['nama_pemilik'];
            }

            $checkKartu = Kartu::where('nomor_kartu', $updateData['nomor_kartu'])->first();

            if(is_null($checkKartu))
            {
                Kartu::create($insertKartu);
            }
        }

        $total_bayar = DB::table('detail_transaksis')
                                ->where('id_transaksi', $id)
                                ->groupBy('id_transaksi')
                                ->sum('subtotal');

        $transaksi->total_harga = $total_bayar;
        $transaksi->metode_pembayaran = $updateData['metode_pembayaran'];
        $transaksi->id_karyawan = $updateData['id_karyawan'];

        if($transaksi->save()){
            return response([
                'message' => 'Update data transaksi success',
                'data' => $transaksi,
            ],200);
        }
        return response([
            'message' => 'Update transaksi failed',
            'data' => null,
        ],400);
    }
}