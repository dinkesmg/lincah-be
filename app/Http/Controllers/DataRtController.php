<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DataRt;
use App\Models\Kecamatan;
use App\Models\JenisKasus;
use App\Repositories\DataRtRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DataRtController extends Controller
{
    protected $data_rtRepository;

    public function __construct()
    {
        $this->data_rtRepository = new DataRtRepository();

        $this->middleware('permission:data_rt-index|data_rt-create|data_rt-edit|data_rt-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:data_rt-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:data_rt-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:data_rt-show', ['only' => ['show']]);
        $this->middleware('permission:data_rt-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request, $jenis_kasus_uuid)
    {
        $kecamatan = Kecamatan::get();
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();

        $tahun = session('tahun', date('Y'));

        $rekap = DataRt::selectRaw('
                CAST(bulan AS UNSIGNED) as bulan,
                SUM(keterpaparan) as keterpaparan,
                SUM(kerentanan) as kerentanan,
                SUM(potensial_dampak) as potensial_dampak,
                SUM(jumlah_kasus) as jumlah_kasus
            ')
            ->byUserRole()
            ->where('jenis_kasus_id', $jenisKasus->id)
            ->where('tahun', $tahun);

        if ($request->filled('kecamatan_id')) {
            $rekap->where('kecamatan_id', $request->kecamatan_id);
        }

        if ($request->filled('kelurahan_id')) {
            $rekap->where('kelurahan_id', $request->kelurahan_id);
        }

        $rekap = $rekap
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $bulanData = $rekap[$i] ?? null;
            $data[] = [
                'bulan'             => $i,
                'keterpaparan'      => $bulanData->keterpaparan ?? 0,
                'kerentanan'        => $bulanData->kerentanan ?? 0,
                'potensial_dampak'  => $bulanData->potensial_dampak ?? 0,
                'jumlah_kasus'      => $bulanData->jumlah_kasus ?? 0,
            ];
        }
        
        $request->flash();
        return view('backend.data_rt.index', compact('data', 'jenisKasus', 'kecamatan'));
    }

    public function create(Request $request, $jenis_kasus_uuid)
    {
        $kecamatan = Kecamatan::get();
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();

        return view('backend.data_rt.create', compact('jenisKasus', 'kecamatan'));
    }

    public function store(Request $request, $jenis_kasus_uuid)
    {
        $validation = $this->data_rtRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->data_rtRepository->create($data, $jenis_kasus_uuid);
            DB::commit();
            return redirect(route('monitoring-rt.index', [$jenis_kasus_uuid]))->with('success', 'Data Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Request $request, $uuid, $jenis_kasus_uuid)
    {
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();
        
        $data = $this->data_rtRepository->findByUuid($uuid);
        return view('backend.data_rt.create', compact('data', 'jenisKasus', 'kecamatan'));
    }

    public function edit(Request $request, $jenis_kasus_uuid)
    {
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();

        return view('backend.data_rt.import', compact('jenisKasus'));
    }
}
