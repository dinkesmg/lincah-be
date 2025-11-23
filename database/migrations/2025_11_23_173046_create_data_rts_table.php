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
        Schema::create('data_rts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('bulan');
            $table->string('tahun');
            $table->unsignedBigInteger('kecamatan_id');
            $table->unsignedBigInteger('kelurahan_id');
            $table->unsignedBigInteger('rw_id');
            $table->unsignedBigInteger('rt_id');
            $table->unsignedBigInteger('jenis_kasus_id');
            $table->integer('keterpaparan')->default(0);
            $table->integer('kerentanan')->default(0);
            $table->integer('potensial_dampak')->default(0);
            $table->integer('jumlah_kasus')->default(0);
            $table->string('image')->nullable();
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
        Schema::dropIfExists('data_rts');
    }
};
