<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Menu;
use Validator;

class MenuController extends Controller
{
    //
    public function index(){
        $menu = DB::table('menus')
                    ->join('bahans','bahans.id','=','menus.id_bahan')
                    ->select('menus.id','menus.nama_menu as  nama_menu', 'menus.takaran_saji as takaran_saji','menus.harga as harga',
                    'menus.kategori as  kategori', 'menus.unit as unit','menus.deskripsi as deskripsi','bahans.id as id_bahan','bahans.nama_bahan as nama_bahan')
                    ->where('menus.isDeleted',0)
                    ->get();

        if(count($menu) > 0)
            return response([
                'message' => 'Retrieve All Success',
                'data' => $menu
            ],200);

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $menu = Menu::find($id);

        if(!is_null($menu))
            return response([
                'message' => 'Retrieve Menu Success',
                'data' => $menu
            ],200);

        return response([
            'message' => 'Menu Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $tempMenu = null;
        $validate = Validator::make($storeData, [
            'nama_menu' => 'required|unique:menus',
            'takaran_saji' => 'required',
            'harga' => 'required',
            'kategori' => 'required',
            'unit' => 'required',
            'deskripsi' => 'required',
            'id_bahan' => 'required',
        ]);
        
        if ($validate->fails()) {
            $error = $validate->errors()->first();
            
            if ($error === 'The nama menu has already been taken.') {
                $tempMenu = Menu::where('nama_menu', $storeData['nama_menu'])->first();
                if($tempMenu->isDeleted === 1)
                {
                    $tempMenu->isDeleted = 0;
                    $tempMenu->takaran_saji = $storeData['takaran_saji'];
                    $tempMenu->harga = $storeData['harga'];
                    $tempMenu->kategori = $storeData['kategori'];
                    $tempMenu->unit = $storeData['unit'];
                    $tempMenu->deskripsi = $storeData['deskripsi'];
                    $tempMenu->id_bahan = $storeData['id_bahan'];
                    if($tempMenu->save()){
                        return response([
                            'message' => 'Insert Menu Success!',
                            'data' => $tempMenu,
                        ],200);
                    }
                }
            }
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }


        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $menu = Menu::create($storeData);
        return response([
            'message' => 'Add Menu Success',
            'data' => $menu,
        ],200);
    }

    public function destroy($id){
        $menu = Menu::find($id);

        if(is_null($menu)){
            return response([
                'message' => 'Menu Not Found',
                'data' => null
            ],404);
        }

      $menu->isDeleted = 1;
      if($menu->save()){
            return response([
                'message' => 'Delete Menu Success',
                'data' => $menu,
            ],200);
        }
      
        return response([
            'message' => 'Delete menu Failed',
            'data' => null,
        ],400);
    }

    public function update(Request $request, $id){
        $menu = Menu::find($id);
        if(is_null($menu)){
            return response([
                'message' => 'Menu Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_menu' => [Rule::unique('menus')->ignore($menu),'required'],
            'takaran_saji' => 'required',
            'harga' => 'required',
            'kategori' => 'required',
            'unit' => 'required',
            'deskripsi' => 'required',
            'id_bahan' => 'required',
        ]);

        if($validate->fails()){
            $error = $validate->errors()->first();
    
            if($error == 'The nama menu has already been taken.')
            {
                $tempMenu = Menu::where('nama_menu', $updateData['nama_menu'])->first();
                if($tempMenu->isDeleted == 1){
                    return response([
                        'message' => 'Silakan Input Ulang Nama Menu',
                        'data' => null
                    ],400);
                } else{
                    return response([
                        'message' => 'Nama Menu Sudah Ada',
                        'data' => null
                    ],400);
                }
            }
                return response(['message' => $validate->errors()],400);
        }

        $menu->nama_menu = $updateData['nama_menu'];
        $menu->takaran_saji = $updateData['takaran_saji'];
        $menu->harga = $updateData['harga'];
        $menu->kategori = $updateData['kategori'];
        $menu->unit = $updateData['unit'];
        $menu->deskripsi = $updateData['deskripsi'];
        $menu->id_bahan = $updateData['id_bahan'];

        if($menu->save()){
            return response([
                'message' => 'Update Menu Success',
                'data' => $menu,
            ],200);
        }
        return response([
            'message' => 'Update Menu Failed',
            'data' => null,
        ],400);
    }
}
