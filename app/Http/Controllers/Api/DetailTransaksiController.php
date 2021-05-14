<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DetailTransaksi;
use App\RiwayatBahanKeluar;
use App\BahanHarian;
use App\Transaksi;
use App\Menu;
use App\Kartu;
use App\Bahan;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\DB;

class DetailTransaksiController extends Controller
{
    //
    public function showWaiter(){
        $detailtransaksi = DB::table('detail_transaksis')
                         ->join('menus','menus.id','=','detail_transaksis.id_menu')
                         ->select('detail_transaksis.*','menus.nama_menu')
                         ->where('status_chef', '=', 'Ready to Serve')
                         ->where('detail_transaksis.isDeleted',0)
                         ->get();

        if(count($detailtransaksi) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $detailtransaksi
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }
    public function showChef(){
        $detailtransaksi = DB::table('detail_transaksis')
                            ->join('menus','menus.id','=','detail_transaksis.id_menu')
                            ->select('detail_transaksis.*','menus.nama_menu')
                            ->where('status_chef', '=', 'Pending')
                            ->where('detail_transaksis.isDeleted',0)
                            ->get();

        if(count($detailtransaksi) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $detailtransaksi
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function showTransaksi(Request $request,$id){
        $detailtransaksi=$request->all;
        if('metode_pembayaran' == 'Non Tunai')
        { 
            $detailtransaksi = DB::table('detail_transaksis')
            ->join('transaksis','transaksis.id','=','detail_transaksis.id_transaksi')
            ->join('menus','menus.id','=','detail_transaksis.id_menu')
            ->join('kartus','kartus.nomor_kartu','=','transaksis.nomor_kartu')
            ->select('detail_transaksis.*','menus.nama_menu')
            ->where('transaksis.id','=',$id)
            ->get();
        }
        else{
            $detailtransaksi = DB::table('detail_transaksis')
            ->join('transaksis','transaksis.id','=','detail_transaksis.id_transaksi')
            ->join('menus','menus.id','=','detail_transaksis.id_menu')
            ->select('detail_transaksis.*','menus.nama_menu')
            ->where('transaksis.id','=',$id)
            ->get();
        }
        if(!is_null($detailtransaksi))
            return response([
                'message' => 'Retrieve Detail Transaksi Success',
                'data' => $detailtransaksi
            ],200);

        return response([
            'message' => 'Detail Transaksi Not Found',
            'data' => null
        ],404);
    }

    public function showFinish ($id){
            $detailtransaksi = DB::table('detail_transaksis')
            ->join('transaksis','transaksis.id','=','detail_transaksis.id_transaksi')
            ->join('menus','menus.id','=','detail_transaksis.id_menu')
            ->select(DB::raw('SUM(detail_transaksis.jumlah) as jumlah, SUM(detail_transaksis.subtotal,menus.nama_menu as nama_menu'))
            ->groupBy('menus.nama_menu')
            ->where('transaksis.id','=',$id)
            ->get();
            $total_bayar = DB::table('detail_transaksis')
            ->where('id_transaksi', $id)
            ->groupBy('id_transaksi')
            ->sum('subtotal');

        if(!is_null($detailtransaksi))
            return response([
                'message' => 'Retrieve Detail Transaksi Success',
                'data' => $detailtransaksi,
                'total' => $total_bayar
            ],200);

        return response([
            'message' => 'Detail Transaksi Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $tempData = array();
        
        $tempTime = Carbon::now()->format('Y-m-d');
        
        
        foreach($storeData['data'] as $item)
        {
            $tempMenu = Menu::where('id','=', $item['id_menu'])->first();
            $id_bahan = $tempMenu->id_bahan;
            $riwayatKeluar['id_bahan'] =  $id_bahan;

            $item['id_transaksi'] = $storeData['id_transaksi'];
            $item['subtotal'] = $item['jumlah'] * $tempMenu->harga;
            DetailTransaksi::create($item);                      
            $riwayatKeluar['tanggal'] = $tempTime;
            $riwayatKeluar['jumlah'] = $item['jumlah'] * $tempMenu->takaran_saji;
            $riwayatKeluar['status'] ='Keluar';

            RiwayatBahanKeluar::create($riwayatKeluar);
            
            $bahan = Bahan::find($id_bahan);
            $bahan->jumlah_bahan_sisa = $bahan->jumlah_bahan_sisa - ($riwayatKeluar['jumlah']);
            if($bahan->save()) {
                $bahanHarian['jumlah'] = $bahan->jumlah_bahan_sisa;
                $bahanHarian['tanggal'] = $riwayatKeluar['tanggal'];
                $bahanHarian['id_bahan'] = $id_bahan;

                $temp = BahanHarian::where('tanggal', $bahanHarian['tanggal'])
                            ->where('id_bahan',$bahanHarian['id_bahan'])
                            ->first();
                
                if (is_null($temp)) {
                    $riwHarian = BahanHarian::create($bahanHarian);
                } else {
                    $tempRiwayat = BahanHarian::where('id', $temp->id)->first();
                    $tempRiwayat->jumlah = $bahan->jumlah_bahan_sisa;
                    $tempRiwayat->save();
                }
            }
        }

        return response([
            'message' => 'Add Detail Transaksi Success',
            'data' => $storeData['data']
        ],200);
    }

    public function update(Request $request, $id){
        $updateData = $request->all();
        $detailtransaksi = DetailTransaksi::find($id);
        if(is_null($detailtransaksi)){
            return response([
                'message' => 'Detail Transaksi Not Found',
                'data' => null
            ],404);
        }
        $detailtransaksi->status_chef = $updateData['status_chef'];

        if($detailtransaksi->save()){
            return response([
                'message' => 'Update Status Success',
                'data' => $detailtransaksi,
            ],200);
        }
        return response([
            'message' => 'Update Status Failed',
            'data' => null,
        ],400);
    }
}
