<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
    public function laporanStokBulananCustom($bulan,$idKaryawan,$nama_menu){
        $tanggal_awal = Carbon::parse($bulan)->startOfMonth()->toDateString();
        $tempTanggalAwalFormat = Carbon::parse($tanggal_awal)->format('d F Y');
        $tempTanggalCek = Carbon::parse($tanggal_awal)->format('Y-m-d');
        $tanggal_akhir = Carbon::parse($bulan)->endOfMonth()->toDateString();
        $rangeBulan = CarbonPeriod::create($tanggal_awal, $tanggal_akhir);

        $dataFinal = array();

        $tempRemain = 0;

        foreach($rangeBulan as $item)
        {
            $tempArray['tanggal'] = $item->format('d F Y');
            $tempTanggal = $item->format('Y-m-d');
            $menu = DB::table('menus')
                    ->select('menus.id_bahan','menus.nama_menu','bahans.unit','menus.kategori')
                    ->join('bahans','bahans.id','=','menus.id_bahan')
                    ->where('menus.nama_menu','=',$nama_menu)
                    ->first();
            $tempArray['unit'] = $menu->unit;
            
            $tempArray['incomingStock'] = DB::table('riwayat_bahan_masuks')
                                        ->where('id_bahan','=',$menu->id_bahan)
                                        ->whereDate('tanggal','=',$tempTanggal)
                                        ->sum('jumlah');
            $tempArray['wasteStock'] = DB::table('riwayat_bahan_keluars')
                                        ->where('id_bahan','=',$menu->id_bahan)
                                        ->where('status','=','Buang')
                                        ->whereDate('tanggal','=',$tempTanggal)
                                        ->sum('jumlah');
            $temp = DB::table('bahan_harians')
                ->where('id_bahan','=',$menu->id_bahan)
                ->whereDate('tanggal','=',$tempTanggal)
                ->orderBy('tanggal','desc')
                ->select('jumlah')
                ->first();  
            if(is_null($temp)){
                if($tempArray['tanggal']==$tempTanggalAwalFormat){

                    $tempData = DB::table('bahan_harians')
                    ->where('id_bahan','=',$menu->id_bahan)
                    ->whereDate('tanggal','<',$tempTanggalCek)
                    ->orderBy('tanggal','desc')
                    ->select('jumlah')
                    ->first();
                    if(is_null($tempData))
                    {
                        $tempArray['remainingStock'] = 0;
                    }  
                }else{
                    $tempArray['remainingStock'] = $tempRemain;
                }   
            }else{
                $tempRemain = json_decode($temp->jumlah,true);
                $tempArray['remainingStock'] = $tempRemain;
            }
                array_push($dataFinal, $tempArray);
            }
                    $tempId = User::find($idKaryawan);
                    $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                    $dataLain['nama_karyawan'] = $tempId->name;
                    $dataLain['periode'] = Carbon::parse($bulan)->format('F Y');
                    $dataLain['inputan_menu'] = $nama_menu;
            // return response([
            //     'message' => 'Retrieve All Success',
            //     'data' => $dataFinal,
            //     'dataLain' => $dataLain
            // ],200); 
            $pdf = PDF::loadview('laporanStokCustomBahan',['menu'=>$dataFinal,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();          
    }
    public function laporanStokCustomAll($tanggal_awal,$tanggal_akhir,$idKaryawan){
        $menu = DB::table('menus')
                    ->select('menus.id_bahan','menus.nama_menu','bahans.unit','menus.kategori')
                    ->join('bahans','bahans.id','=','menus.id_bahan')
                    ->where('menus.isDeleted',0)
                    ->get();
                    
                    $tempId = User::find($idKaryawan);
                    $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                    $dataLain['nama_karyawan'] = $tempId->name;
                    $dataLain['periode'] = 'Custom' . '(' . Carbon::parse($tanggal_awal)->format('d M Y').' s/d '.Carbon::parse($tanggal_akhir)->format('d M Y').')' ;
                    $dataLain['inputan_menu'] = 'All';
                    $tempTanggalAwal = explode("-",$tanggal_awal);                 
                    $tempTanggalAkhir = explode("-",$tanggal_akhir); 

                    foreach($menu as $item)
                    {
                        $item->incomingStock = DB::table('riwayat_bahan_masuks')
                        ->where('id_bahan','=',$item->id_bahan)
                        ->whereBetween('tanggal',[$tanggal_awal, $tanggal_akhir])
                        ->sum('jumlah');
                        $item->wasteStock = DB::table('riwayat_bahan_keluars')
                        ->where('id_bahan','=',$item->id_bahan)
                        ->whereBetween('tanggal',[$tanggal_awal, $tanggal_akhir])
                        ->where('status','=','Buang')
                        ->sum('jumlah');

                        $temp = DB::table('bahan_harians')
                        ->where('id_bahan','=',$item->id_bahan)
                        ->whereBetween('tanggal',[$tanggal_awal, $tanggal_akhir])
                        ->orderBy('tanggal','desc')
                        ->select('jumlah')
                        ->first();     
                                  
                        if(is_null($temp))
                        {
                            $temp = DB::table('bahan_harians')
                                                ->where('id_bahan','=',$item->id_bahan)
                                                ->whereBetween('tanggal',[$tanggal_awal, $tanggal_akhir])
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

    public function laporanStokCustomItem($tanggal_awal,$tanggal_akhir,$idKaryawan,$nama_menu){
        $tanggal_awal = Carbon::parse($tanggal_awal)->toDateString();
        $tempTanggalAwalFormat = Carbon::parse($tanggal_awal)->format('d F Y');
        $tempTanggalCek = Carbon::parse($tanggal_awal)->format('Y-m-d');
        $tanggal_akhir = Carbon::parse($tanggal_akhir)->toDateString();
        $rangeBulan = CarbonPeriod::create($tanggal_awal, $tanggal_akhir);

        $dataFinal = array();

        $tempRemain = 0;

        foreach($rangeBulan as $item)
        {
            $tempArray['tanggal'] = $item->format('d F Y');
            $tempTanggal = $item->format('Y-m-d');
            $menu = DB::table('menus')
                    ->select('menus.id_bahan','menus.nama_menu','bahans.unit','menus.kategori')
                    ->join('bahans','bahans.id','=','menus.id_bahan')
                    ->where('menus.nama_menu','=',$nama_menu)
                    ->first();
            $tempArray['unit'] = $menu->unit;
            
            $tempArray['incomingStock'] = DB::table('riwayat_bahan_masuks')
                                        ->where('id_bahan','=',$menu->id_bahan)
                                        ->whereDate('tanggal','=',$tempTanggal)
                                        ->sum('jumlah');
            $tempArray['wasteStock'] = DB::table('riwayat_bahan_keluars')
                                        ->where('id_bahan','=',$menu->id_bahan)
                                        ->where('status','=','Buang')
                                        ->whereDate('tanggal','=',$tempTanggal)
                                        ->sum('jumlah');
            $temp = DB::table('bahan_harians')
                ->where('id_bahan','=',$menu->id_bahan)
                ->whereDate('tanggal','=',$tempTanggal)
                ->orderBy('tanggal','desc')
                ->select('jumlah')
                ->first();  
            if(is_null($temp)){
                if($tempArray['tanggal']==$tempTanggalAwalFormat){

                    $tempData = DB::table('bahan_harians')
                    ->where('id_bahan','=',$menu->id_bahan)
                    ->whereDate('tanggal','<',$tempTanggalCek)
                    ->orderBy('tanggal','desc')
                    ->select('jumlah')
                    ->first();
                    if(is_null($tempData))
                    {
                        $tempArray['remainingStock'] = 0;
                    }  
                }else{
                    $tempArray['remainingStock'] = $tempRemain;
                }   
            }else{
                $tempRemain = json_decode($temp->jumlah,true);
                $tempArray['remainingStock'] = $tempRemain;
            }
                array_push($dataFinal, $tempArray);
            }
                    $tempId = User::find($idKaryawan);
                    $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                    $dataLain['nama_karyawan'] = $tempId->name;
                    $dataLain['periode'] = 'Custom' . '(' . Carbon::parse($tanggal_awal)->format('d M Y').' s/d '.Carbon::parse($tanggal_akhir)->format('d M Y').')' ;
                    $dataLain['inputan_menu'] = $nama_menu;
            // return response([
            //     'message' => 'Retrieve All Success',
            //     'data' => $dataFinal,
            //     'dataLain' => $dataLain
            // ],200); 
            $pdf = PDF::loadview('laporanStokCustomBahan',['menu'=>$dataFinal,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();          
    }
    public function pendapatanLap($idKaryawan, $tahunAwal, $tahunAkhir)
    {
        if($tahunAkhir == "Kosong")
        {
            $bulan = array();
            for($i = 1;$i<=12;$i++)
            {
                $tanggal = $tahunAwal."-".$i;
                $temp['bulan'] = Carbon::parse($tanggal)->format('F');

                $makanan = DB::table('detail_transaksis')
                    ->join('transaksis','transaksis.id', '=', 'detail_transaksis.id_transaksi')
                    ->join('menus','menus.id', '=', 'detail_transaksis.id_menu')
                    ->where('detail_transaksis.isDeleted',0)
                    ->where('menus.kategori',"Makanan Utama")
                    ->whereYear('transaksis.tanggal_transaksi', '=', $tahunAwal)
                    ->whereMonth('transaksis.tanggal_transaksi', '=', $i)
                    ->groupBy('menus.kategori')
                    ->sum('detail_transaksis.subtotal');

                $minuman = DB::table('detail_transaksis')
                    ->join('transaksis','transaksis.id', '=', 'detail_transaksis.id_transaksi')
                    ->join('menus','menus.id', '=', 'detail_transaksis.id_menu')
                    ->where('detail_transaksis.isDeleted',0)
                    ->where('menus.kategori',"Minuman")
                    ->whereYear('transaksis.tanggal_transaksi', '=', $tahunAwal)
                    ->whereMonth('transaksis.tanggal_transaksi', '=', $i)
                    ->groupBy('menus.kategori')
                    ->sum('detail_transaksis.subtotal');

                $sideDish = DB::table('detail_transaksis')
                    ->join('transaksis','transaksis.id', '=', 'detail_transaksis.id_transaksi')
                    ->join('menus','menus.id', '=', 'detail_transaksis.id_menu')
                    ->where('detail_transaksis.isDeleted',0)
                    ->where('menus.kategori',"Side Dish")
                    ->whereYear('transaksis.tanggal_transaksi', '=', $tahunAwal)
                    ->whereMonth('transaksis.tanggal_transaksi', '=', $i)
                    ->groupBy('menus.kategori')
                    ->sum('detail_transaksis.subtotal');

                $temp['MakananUtama']= $makanan;
                $temp['SideDish']=$sideDish;
                $temp['Minuman']=$minuman;
                $temp['TotalPendapatan'] =  $makanan + $minuman + $sideDish;

                array_push($bulan,$temp);
                $tempId = User::find($idKaryawan);
                $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                $dataLain['nama_karyawan'] = $tempId->name;
                $dataLain['status'] = "Bulanan";
                $dataLain['tahun'] = $tahunAwal;
            }

            // return response([
            //     'message' => 'Bulan',
            //     'data' => $bulan,
            //     'dataLain' => $dataLain
            // ],200);
            $pdf = PDF::loadview('laporanPendapatan',['menu'=>$bulan,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();
        }else{
            $bulan = array();
            $tempTahunAwal = (int)$tahunAwal;
            $tempTahunAkhir = (int)$tahunAkhir;

            for($i = $tempTahunAwal;$i<=$tempTahunAkhir;$i++)
            {
                $temp['bulan'] = $i;

                $makanan = DB::table('detail_transaksis')
                    ->join('transaksis','transaksis.id', '=', 'detail_transaksis.id_transaksi')
                    ->join('menus','menus.id', '=', 'detail_transaksis.id_menu')
                    ->where('detail_transaksis.isDeleted',0)
                    ->where('menus.kategori',"Makanan Utama")
                    ->whereYear('transaksis.tanggal_transaksi', '=', $i)
                    ->groupBy('menus.kategori')
                    ->sum('detail_transaksis.subtotal');

                $minuman = DB::table('detail_transaksis')
                    ->join('transaksis','transaksis.id', '=', 'detail_transaksis.id_transaksi')
                    ->join('menus','menus.id', '=', 'detail_transaksis.id_menu')
                    ->where('detail_transaksis.isDeleted',0)
                    ->where('menus.kategori',"Minuman")
                    ->whereYear('transaksis.tanggal_transaksi', '=', $i)
                    ->groupBy('menus.kategori')
                    ->sum('detail_transaksis.subtotal');

                $sideDish = DB::table('detail_transaksis')
                    ->join('transaksis','transaksis.id', '=', 'detail_transaksis.id_transaksi')
                    ->join('menus','menus.id', '=', 'detail_transaksis.id_menu')
                    ->where('detail_transaksis.isDeleted',0)
                    ->where('menus.kategori',"Side Dish")
                    ->whereYear('transaksis.tanggal_transaksi', '=', $i)
                    ->groupBy('menus.kategori')
                    ->sum('detail_transaksis.subtotal');

                $temp['MakananUtama']= $makanan;
                $temp['SideDish']=$sideDish;
                $temp['Minuman']=$minuman;
                $temp['TotalPendapatan'] =  $makanan + $minuman + $sideDish;

                array_push($bulan,$temp);
                $tempId = User::find($idKaryawan);
                $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                $dataLain['nama_karyawan'] = $tempId->name;
                $dataLain['status'] = "Tahunan";
                $dataLain['periode'] = Carbon::parse($tahunAwal)->format('Y').' s/d '.Carbon::parse($tahunAkhir)->format('Y') ;
            }
            // return response([
            //     'message' => 'Bulan',
            //     'data' => $bulan,
            //     'dataLain' => $dataLain
            // ],200);
            $pdf = PDF::loadview('laporanPendapatan',['menu'=>$bulan,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();
        }
    }
    public function pengeluaranLap($idKaryawan, $tahunAwal, $tahunAkhir)
    {
        if($tahunAkhir == "Kosong")
        {
            $bulan = array();
            for($i = 1;$i<=12;$i++)
            {
                $tanggal = $tahunAwal."-".$i;
                $temp['bulan'] = Carbon::parse($tanggal)->format('F');

                $makanan = DB::table('riwayat_bahan_masuks')
                            ->join('bahans','bahans.id', '=', 'riwayat_bahan_masuks.id_bahan')
                            ->where('riwayat_bahan_masuks.isDeleted',0)
                            ->whereIn('bahans.id',function($haha){
                                $haha->select('id_bahan') 
                                ->from('menus')
                                ->where('kategori','Makanan Utama');
                                }
                            )
                            ->whereYear('riwayat_bahan_masuks.tanggal', '=', $tahunAwal)
                            ->whereMonth('riwayat_bahan_masuks.tanggal', '=', $i)
                            ->sum('riwayat_bahan_masuks.harga');

                $minuman = DB::table('riwayat_bahan_masuks')
                            ->join('bahans','bahans.id', '=', 'riwayat_bahan_masuks.id_bahan')
                            ->where('riwayat_bahan_masuks.isDeleted',0)
                            ->whereIn('bahans.id',function($haha){
                                $haha->select('id_bahan') 
                                ->from('menus')
                                ->where('kategori','Minuman');
                                }
                            )
                            ->whereYear('riwayat_bahan_masuks.tanggal', '=', $tahunAwal)
                            ->whereMonth('riwayat_bahan_masuks.tanggal', '=', $i)
                            ->sum('riwayat_bahan_masuks.harga');

                $sideDish = DB::table('riwayat_bahan_masuks')
                            ->join('bahans','bahans.id', '=', 'riwayat_bahan_masuks.id_bahan')
                            ->where('riwayat_bahan_masuks.isDeleted',0)
                            ->whereIn('bahans.id',function($haha){
                                $haha->select('id_bahan') 
                                ->from('menus')
                                ->where('kategori','Side Dish');
                                }
                            )
                            ->whereYear('riwayat_bahan_masuks.tanggal', '=', $tahunAwal)
                            ->whereMonth('riwayat_bahan_masuks.tanggal', '=', $i)
                            ->sum('riwayat_bahan_masuks.harga');

                $temp['MakananUtama']= $makanan;
                $temp['SideDish']=$sideDish;
                $temp['Minuman']=$minuman;
                $temp['TotalPendapatan'] =  $makanan + $minuman + $sideDish;

                array_push($bulan,$temp);
                $tempId = User::find($idKaryawan);
                $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                $dataLain['nama_karyawan'] = $tempId->name;
                $dataLain['status'] = "Bulanan";
                $dataLain['tahun'] = $tahunAwal;
            }

            // return response([
            //     'message' => 'Sekut',
            //     'data' => $bulan,
            //     'dataLain' => $dataLain
            // ],200);
            $pdf = PDF::loadview('laporanPengeluaran',['menu'=>$bulan,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();
        }else{
            $bulan = array();
            $tempTahunAwal = (int)$tahunAwal;
            $tempTahunAkhir = (int)$tahunAkhir;

            for($i = $tempTahunAwal;$i<=$tempTahunAkhir;$i++)
            {
                $temp['bulan'] = $i;

                
                $makanan = DB::table('riwayat_bahan_masuks')
                            ->join('bahans','bahans.id', '=', 'riwayat_bahan_masuks.id_bahan')
                            ->where('riwayat_bahan_masuks.isDeleted',0)
                            ->whereIn('bahans.id',function($haha){
                                $haha->select('id_bahan') 
                                ->from('menus')
                                ->where('kategori','Makanan Utama');
                                }
                            )
                            ->whereYear('riwayat_bahan_masuks.tanggal', '=', $i)
                            ->sum('riwayat_bahan_masuks.harga');

                $minuman = DB::table('riwayat_bahan_masuks')
                            ->join('bahans','bahans.id', '=', 'riwayat_bahan_masuks.id_bahan')
                            ->where('riwayat_bahan_masuks.isDeleted',0)
                            ->whereIn('bahans.id',function($haha){
                                $haha->select('id_bahan') 
                                ->from('menus')
                                ->where('kategori','Minuman');
                                }
                            )
                            ->whereYear('riwayat_bahan_masuks.tanggal', '=', $i)
                            ->sum('riwayat_bahan_masuks.harga');

                $sideDish = DB::table('riwayat_bahan_masuks')
                            ->join('bahans','bahans.id', '=', 'riwayat_bahan_masuks.id_bahan')
                            ->where('riwayat_bahan_masuks.isDeleted',0)
                            ->whereIn('bahans.id',function($haha){
                                $haha->select('id_bahan') 
                                ->from('menus')
                                ->where('kategori','Side Dish');
                                }
                            )
                            ->whereYear('riwayat_bahan_masuks.tanggal', '=', $i)
                            ->sum('riwayat_bahan_masuks.harga');

                $temp['MakananUtama']= $makanan;
                $temp['SideDish']=$sideDish;
                $temp['Minuman']=$minuman;
                $temp['TotalPendapatan'] =  $makanan + $minuman + $sideDish;

                array_push($bulan,$temp);
                $tempId = User::find($idKaryawan);
                    $dataLain['printed'] = 'Printed '.Carbon::now()->format('M d, Y H:i:s a');
                    $dataLain['nama_karyawan'] = $tempId->name;
                $dataLain['status'] = "Tahunan";
                $dataLain['periode'] = Carbon::parse($tahunAwal)->format('Y').' s/d '.Carbon::parse($tahunAkhir)->format('Y') ;
            }
            // return response([
            //     'message' => 'Bulan',
            //     'data' => $bulan,
            //     'dataLain' => $dataLain
            // ],200);
            $pdf = PDF::loadview('laporanPengeluaran',['menu'=>$bulan,'dataLain'=>$dataLain]);
            $pdf->setPaper('a4' , 'portrait');
            return $pdf->output();
        }
    }
}
