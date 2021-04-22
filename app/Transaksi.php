<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaksi extends Model
{
    //
    protected $fillable = [
        'nomor_transaksi', 'metode_pembayaran', 'total_harga', 'tanggal_transaksi', 'kode_verifikasi', 'id_reservasi', 'id_karyawan', 'nomor_kartu', 'status', 'isDeleted',
    ];

    public function getCreatedAtAttribute() {
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    } // convert atribute created_at ke format Y-m-d H:i:s

    public function getUpdatedAtAttribute() {
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    } //convert atribute update_at ke format Y-m-d H:i:s
}
