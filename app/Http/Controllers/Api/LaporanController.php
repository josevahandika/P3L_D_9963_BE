<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\RiwayatBahanMasuk;
use App\User;
use PDF;
class LaporanController extends Controller
{
    //
    public function namaMenu(){
        $menu = DB::table('menus')
                    ->select('menus.nama_menu')
                    ->where('menus.isDeleted',0)
                    ->get();

        $menuArray = [];
        array_push($menuArray,'All');

        foreach($menu as $item)
        {
            array_push($menuArray,$item->nama_menu);
        }
        if(count($menuArray) > 0)
        return response([
            'message' => 'Retrieve All Success',
            'data' => $menuArray
        ],200);
    }

    public function laporanStok($bulan,$idKaryawan,$id_menu){
        $menu = DB::table('menus')
                    ->select('menus.id_bahan','menus.nama_menu','bahans.unit','menus.kategori')
                    ->join('bahans','bahans.id','=','menus.id_bahan')
                    ->where('menus.isDeleted',0)
                    ->get();
                    
                    $tempId = User::find($idKaryawan);
                    $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                    $dataLain['nama_karyawan'] = $tempId->name;
                    $dataLain['periode'] = Carbon::parse($bulan)->format('F Y');
                    $dataLain['inputan_menu'] = $id_menu;
                    $tempBulan = explode("-",$bulan);                 
                    foreach($menu as $item)
                    {
                        $item->incomingStock = DB::table('riwayat_bahan_masuks')
                        ->where('id_bahan','=',$item->id_bahan)
                        ->whereMonth('tanggal','=',$tempBulan[1])
                        ->whereYear('tanggal','=',$tempBulan[0])
                        ->sum('jumlah');
                        $item->wasteStock = DB::table('riwayat_bahan_keluars')
                        ->where('id_bahan','=',$item->id_bahan)
                        ->whereMonth('tanggal','=',$tempBulan[1])
                        ->whereYear('tanggal','=',$tempBulan[0])
                        ->where('status','=','Buang')
                        ->sum('jumlah');

                        $temp = DB::table('bahan_harians')
                        ->where('id_bahan','=',$item->id_bahan)
                        ->whereMonth('tanggal','=',$tempBulan[1])
                        ->whereYear('tanggal','=',$tempBulan[0])
                        ->orderBy('tanggal','desc')
                        ->select('jumlah')
                        ->first();     
                                  
                        if(is_null($temp))
                        {
                            $temp = DB::table('bahan_harians')
                                                ->where('id_bahan','=',$item->id_bahan)
                                                ->whereMonth('tanggal','<',$tempBulan[1])
                                                ->whereYear('tanggal','<',$tempBulan[0])
                                                ->orderBy('tanggal','desc')
                                                ->select('jumlah')
                                                ->first(); 
                            if(is_null($temp))
                            {
                                $item->remainingStock = 0;
                            }     
                            else{                                
                                $tempRemaining = json_decode($temp->jumlah,true);
                                $item->remainingStock = $tempRemaining;
                            }               
                        }
                        else{
                            $tempRemaining = json_decode($temp->jumlah,true);
                            $item->remainingStock = $tempRemaining;
                        }
                    }    
            // return response([
            //     'message' => 'Retrieve All Success',
            //     'data' => $menu,
            //     'dataLain' => $dataLain
            // ],200); 
            $pdf = PDF::loadview('laporanStok',['menu'=>$menu,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();          
    }
}
