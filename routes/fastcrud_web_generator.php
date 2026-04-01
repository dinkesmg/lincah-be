<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Kelurahan;
use App\Models\RW;
use App\Models\RT;
// URL_CRUD_GENERATOR

use App\Http\Controllers\DataRtController;
use App\Http\Controllers\RWController;
use App\Http\Controllers\RTController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\KategoriBeritaController;
use App\Http\Controllers\LinsekController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\TopikController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\JenisKasusController;
use App\Http\Controllers\KelurahanController;
use App\Http\Controllers\KecamatanController;
// CRUD_GENERATOR

Route::resource('rw', RWController::class);
Route::resource('rt', RTController::class);
Route::resource('berita', BeritaController::class);
Route::resource('kategori_berita', KategoriBeritaController::class);
Route::resource('linsek', LinsekController::class)->only(['index', 'create', 'store']);
Route::resource('video', VideoController::class);
Route::resource('foto', FotoController::class);
Route::resource('forum', ForumController::class);
Route::resource('topik', TopikController::class);
Route::prefix('data/{jenis_kasus_uuid}')->group(function () {
    Route::resource('monitoring', DataController::class)->only(['index', 'create', 'store'])->parameters(['monitoring' => 'data']);
    Route::get('monitoring/import', [DataController::class, 'edit'])
        ->name('monitoring.import.form');
    Route::post('monitoring/import', [DataController::class, 'update'])
        ->name('monitoring.import.store');
        
    Route::resource('monitoring-rt', DataRtController::class)->only(['index', 'create', 'store'])->parameters(['monitoring-rt' => 'data']);
});

Route::resource('jenis_kasus', JenisKasusController::class);
Route::resource('kelurahan', KelurahanController::class);
Route::resource('kecamatan', KecamatanController::class);

Route::post('/set-year', function (Request $request) {
    session(['selected_year' => $request->year]);
    return response()->json(['success' => true]);
})->name('setYear');

Route::get('/get-kelurahan/{kecamatan_id}', function ($kecamatan_id) {
    $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)
                    ->select('id', 'nama')
                    ->orderBy('nama')
                    ->get();

    return response()->json($kelurahans);
})->name('getKelurahan');

Route::get('/get-rw/{kelurahan_id}', function ($kelurahan_id) {
    $rws = RW::where('kelurahan_id', $kelurahan_id)
                    ->select('id', 'nama')
                    ->orderBy('nama')
                    ->get();

    return response()->json($rws);
})->name('getRw');

Route::get('/get-rt/{rw_id}', function ($rw_id) {
    $rts = RT::where('rw_id', $rw_id)
                    ->select('id', 'nama')
                    ->orderBy('nama')
                    ->get();

    return response()->json($rts);
})->name('getRt');