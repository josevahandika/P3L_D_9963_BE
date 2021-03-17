<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi');
            $table->string('metode_pembayaran');
            $table->double('total_harga');
            $table->date('tanggal_transaksi');
            $table->string('kode_verifikasi');
            $table->integer('id_reservasi');
            $table->integer('id_karyawan');
            $table->string('nomor_kartu');
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
