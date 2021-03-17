<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_reservasi');
            $table->string('sesi_reservasi');
            $table->string('status_reservasi');
            $table->integer('id_customer');
            $table->integer('id_meja');
            $table->integer('id_karyawan');
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
        Schema::dropIfExists('reservasis');
    }
}
