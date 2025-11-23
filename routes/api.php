<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api_key')->group(function () {
    Route::get('stats', [WebController::class, 'stats']);
    Route::get('linsek', [WebController::class, 'linsek']);
    Route::get('kecamatan', [WebController::class, 'kecamatan']);
    Route::get('kelurahan', [WebController::class, 'kelurahan']);
    Route::get('jenis-resiko', [WebController::class, 'jenisResiko']);
    Route::get('tahun', [WebController::class, 'tahun']);

    //KELURAHAN
    Route::get('data/all', [WebController::class, 'all']);
    Route::get('data/kerentanan', [WebController::class, 'kerentanan']);
    Route::get('data/keterpaparan', [WebController::class, 'keterpaparan']);
    Route::get('data/potensial-dampak', [WebController::class, 'potensialDampak']);
    Route::get('data/jumlah-kasus', [WebController::class, 'jumlahKasus']);
    
    Route::get('spasial/all', [WebController::class, 'spasialAll']);
    Route::get('spasial/kerentanan', [WebController::class, 'spasialKerentanan']);
    Route::get('spasial/keterpaparan', [WebController::class, 'spasialKeterpaparan']);
    Route::get('spasial/potensial-dampak', [WebController::class, 'spasialPotensialDampak']);
    Route::get('spasial/jumlah-kasus', [WebController::class, 'spasialJumlahKasus']);

    //RT
    Route::get('data/all-rt', [WebController::class, 'allRt']);
    Route::get('data/kerentanan-rt', [WebController::class, 'kerentananRt']);
    Route::get('data/keterpaparan-rt', [WebController::class, 'keterpaparanRt']);
    Route::get('data/potensial-dampak-rt', [WebController::class, 'potensialDampakRt']);
    Route::get('data/jumlah-kasus-rt', [WebController::class, 'jumlahKasusRt']);
    
    Route::get('spasial/all-rt', [WebController::class, 'spasialAllRt']);
    Route::get('spasial/kerentanan-rt', [WebController::class, 'spasialKerentananRt']);
    Route::get('spasial/keterpaparan-rt', [WebController::class, 'spasialKeterpaparanRt']);
    Route::get('spasial/potensial-dampak-rt', [WebController::class, 'spasialPotensialDampakRt']);
    Route::get('spasial/jumlah-kasus-rt', [WebController::class, 'spasialJumlahKasusRt']);
    
    Route::get('tabel-data', [WebController::class, 'tabelData']);
    Route::get('tabel-data-rt', [WebController::class, 'tabelDataRt']);
    
    Route::get('publikasi', [WebController::class, 'publikasi']);
    Route::get('publikasi/foto', [WebController::class, 'publikasiFoto']);
    Route::get('publikasi/video', [WebController::class, 'publikasiVideo']);
    
    Route::get('kategori-diskusi', [WebController::class, 'kategoriDiskusi']);
    Route::get('forum-diskusi', [WebController::class, 'forumDiskusi']);
    Route::get('forum-diskusi/{id}', [WebController::class, 'forumDiskusiDetail']);
    
    Route::get('kategori-berita', [WebController::class, 'kategoriBerita']);
    Route::get('berita', [WebController::class, 'berita']);
    Route::get('berita/{id}', [WebController::class, 'beritaDetail']);

});

