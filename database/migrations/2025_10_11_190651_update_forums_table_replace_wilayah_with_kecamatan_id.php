<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan (up).
     */
    public function up(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            if (Schema::hasColumn('forums', 'wilayah')) {
                $table->dropColumn('wilayah');
            }

            $table->unsignedBigInteger('kecamatan_id')->after('topik_id')->nullable();
        });
    }

    /**
     * Balikkan perubahan (down).
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            if (Schema::hasColumn('forums', 'kecamatan_id')) {
                $table->dropColumn('kecamatan_id');
            }

            $table->string('wilayah')->after('topik_id');
        });
    }
};
