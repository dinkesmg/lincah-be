<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->char('bulan', 2);
            $table->char('tahun', 4);
            $table->unsignedBigInteger('kecamatan_id');
            $table->unsignedBigInteger('kelurahan_id');
            $table->unsignedBigInteger('jenis_kasus_id');
            $table->integer('keterpaparan');
            $table->integer('kerentanan');
            $table->integer('potensial_dampak');
            $table->integer('jumlah_kasus');
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
        Schema::dropIfExists('data');
    }
};
