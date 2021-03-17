<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DetailTransaksi extends Model
{
    //
    protected $fillable = [
        'id_menu','jumlah','subtotal','status_chef','id_transaksi','isDeleted',
    ];

    public function getCreatedAtAttribute()
    {
        if(!is_null($this->attributes['created_at']))
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute()
    {
        if(!is_null($this->attributes['updated_at']))
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
    }
}
