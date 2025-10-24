<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Data;
use App\Models\Kecamatan;
use App\Models\JenisKasus;
use App\Repositories\DataRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Imports\DataImport;
use Maatwebsite\Excel\Facades\Excel;

class DataController extends Controller
{
    protected $dataRepository;

    public function __construct()
    {
        $this->dataRepository = new DataRepository();

        $this->middleware('permission:data-index|data-create|data-edit|data-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:data-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:data-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:data-show', ['only' => ['show']]);
        $this->middleware('permission:data-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request, $jenis_kasus_uuid)
    {
        $kecamatan = Kecamatan::get();
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();

        $tahun = session('tahun', date('Y'));

        $rekap = Data::selectRaw('
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
        return view('backend.data.index', compact('data', 'jenisKasus', 'kecamatan'));
    }

    public function create(Request $request, $jenis_kasus_uuid)
    {
        $kecamatan = Kecamatan::get();
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();

        return view('backend.data.create', compact('jenisKasus', 'kecamatan'));
    }

    public function store(Request $request, $jenis_kasus_uuid)
    {
        $validation = $this->dataRepository->validate();
        $data = $request->validate($validation['rules'], $validation['messages']);
        DB::beginTransaction();
        try {
            $this->dataRepository->create($data, $jenis_kasus_uuid);
            DB::commit();
            return redirect(route('monitoring.index', [$jenis_kasus_uuid]))->with('success', 'Data Berhasil Tersimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Request $request, $uuid, $jenis_kasus_uuid)
    {
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();
        
        $data = $this->dataRepository->findByUuid($uuid);
        return view('backend.data.create', compact('data', 'jenisKasus', 'kecamatan'));
    }

    public function edit(Request $request, $jenis_kasus_uuid)
    {
        $jenisKasus = JenisKasus::whereUuid($jenis_kasus_uuid)->firstOrFail();

        return view('backend.data.import', compact('jenisKasus'));
    }

    public function update(Request $request, $jenis_kasus_uuid)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $jenisKasus = JenisKasus::where('uuid', $jenis_kasus_uuid)->firstOrFail();

        Excel::import(new DataImport($jenisKasus->id), $request->file('file'));

        return redirect()
            ->route('monitoring.index', ['jenis_kasus_uuid' => $jenis_kasus_uuid])
            ->with('success', 'Data berhasil diimpor!');
    }
}
