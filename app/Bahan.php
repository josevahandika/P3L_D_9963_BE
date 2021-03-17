<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bahan extends Model
{
    //
    protected $fillable = [
        'nama_bahan', 'unit', 'jumlah_bahan_sisa', 'isDeleted'
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
