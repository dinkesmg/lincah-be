<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use App\Http\Resources\KecamatanResource;
use App\Http\Resources\KelurahanResource;
use App\Http\Resources\JenisKasusResource;
use App\Http\Resources\DataSpasialResource;
use App\Http\Resources\DataStatistikResource;
use App\Http\Resources\TabelDataResource;
use App\Http\Resources\TabelDataRtResource;
use App\Http\Resources\PublikasiResource;
use App\Http\Resources\KategoriDiskusiResource;
use App\Http\Resources\ForumResource;
use App\Http\Resources\ForumDetailResource;
use App\Http\Resources\KategoriBeritaResource;
use App\Http\Resources\BeritaResource;
use App\Http\Resources\BeritaDetailResource;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\RT;
use App\Models\JenisKasus;
use App\Models\User;
use App\Models\Linsek;
use App\Models\Data;
use App\Models\DataRt;
use App\Models\Foto;
use App\Models\Video;
use App\Models\Topik;
use App\Models\Forum;
use App\Models\KategoriBerita;
use App\Models\Berita;

class WebController extends Controller
{
    public function stats(Request $request)
    {
        $kelurahan = Kelurahan::count();
        $data = JenisKasus::count();
        $opd = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'SUPERADMIN');
        })->count();

        return ResponseFormatter::success([
            'integrasi' => $data,
            'wilayah' => $kelurahan,
            'opd' => $opd,
        ], 'Get Data Berhasil');
    }
    
    public function linsek(Request $request)
    {
        $linsek = Linsek::first();

        return ResponseFormatter::success([
            'content' => $linsek->content ?? '',
        ], 'Get Data Berhasil');
    }
    
    public function kecamatan(Request $request)
    {
        $kecamatan = Kecamatan::get();

        return ResponseFormatter::success([
            'kecamatan' => KecamatanResource::collection($kecamatan),
        ], 'Get Data Berhasil');
    }
    
    public function kelurahan(Request $request)
    {
        $kelurahan = Kelurahan::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->get();

        return ResponseFormatter::success([
            'kelurahan' => KelurahanResource::collection($kelurahan),
        ], 'Get Data Berhasil');
    }
    
    public function jenisResiko(Request $request)
    {
        $jenis_kasus = JenisKasus::get();

        return ResponseFormatter::success([
            'jenis_resiko' => JenisKasusResource::collection($jenis_kasus),
        ], 'Get Data Berhasil');
    }

    public function tahun(Request $request)
    {
        $currentYear = session('selected_year', date('Y'));
        $startYear = 2025;
        $years = [];

        $maxYear = max($currentYear, $startYear);

        for ($year = $maxYear; $year >= max($maxYear - 4, $startYear); $year--) {
            $years[] = ['nama' => $year];
        }

        return ResponseFormatter::success([
            'tahun' => $years,
        ], 'Get Data Berhasil');
    }
    
    public function spasialAll(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $kelurahanList = Kelurahan::query()
            ->when($request->kecamatan_id, fn($q) => $q->where('kecamatan_id', $request->kecamatan_id))
            ->when($request->kelurahan_id, fn($q) => $q->where('id', $request->kelurahan_id))
            ->get();

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, fn($q) => $q->where('kecamatan_id', $request->kecamatan_id))
            ->when($request->kelurahan_id, fn($q) => $q->where('kelurahan_id', $request->kelurahan_id))
            ->when($request->jenis_resiko_id, fn($q) => $q->where('jenis_kasus_id', $request->jenis_resiko_id))
            ->where('tahun', $tahun)
            ->select(
                'kelurahan_id',
                DB::raw('SUM(kerentanan) as total_kerentanan'),
                DB::raw('SUM(keterpaparan) as total_keterpaparan'),
                DB::raw('SUM(potensial_dampak) as total_potensial_dampak'),
                DB::raw('COUNT(*) as jumlah_kasus')
            )
            ->groupBy('kelurahan_id')
            ->get()
            ->keyBy('kelurahan_id');

        $jenisKasus = $request->jenis_resiko_id
            ? JenisKasus::find($request->jenis_resiko_id)
            : null;

        $result = $kelurahanList->map(function ($kelurahan) use ($dataKasus, $tahun, $jenisKasus) {
            $data = $dataKasus[$kelurahan->id] ?? null;

            return (object) [
                'id' => $kelurahan->id,
                'nama_kelurahan' => $kelurahan->nama,
                'nama_kecamatan' => $kelurahan->kecamatan->nama ?? null,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'kerentanan' => (int) ($data->total_kerentanan ?? 0),
                'keterpaparan' => (int) ($data->total_keterpaparan ?? 0),
                'potensial_dampak' => (int) ($data->total_potensial_dampak ?? 0),
                'jumlah_kasus' => (int) ($data->jumlah_kasus ?? 0),
            ];
        });

        return ResponseFormatter::success([
            'spasial' => DataSpasialResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function spasialJumlahKasus(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $kelurahanList = Kelurahan::when($request->kecamatan_id, function ($q) use ($request) {
            $q->where('kecamatan_id', $request->kecamatan_id);
        })->when($request->kelurahan_id, function ($q) use ($request) {
            $q->where('id', $request->kelurahan_id);
        })->get();

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('kelurahan_id', DB::raw('SUM(jumlah_kasus) as total_kasus'))
            ->groupBy('kelurahan_id')
            ->pluck('total_kasus', 'kelurahan_id');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $result = $kelurahanList->map(function ($kelurahan) use ($dataKasus, $tahun, $jenisKasus) {
            $jumlahKasus = $dataKasus[$kelurahan->id] ?? 0;
            return (object) [
                'id' => $kelurahan->id,
                'kecamatan' => $kelurahan->kecamatan,
                'kelurahan' => $kelurahan,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'spasial' => DataSpasialResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function spasialKerentanan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $kelurahanList = Kelurahan::when($request->kecamatan_id, function ($q) use ($request) {
            $q->where('kecamatan_id', $request->kecamatan_id);
        })->when($request->kelurahan_id, function ($q) use ($request) {
            $q->where('id', $request->kelurahan_id);
        })->get();

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('kelurahan_id', DB::raw('SUM(kerentanan) as total_kasus'))
            ->groupBy('kelurahan_id')
            ->pluck('total_kasus', 'kelurahan_id');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $result = $kelurahanList->map(function ($kelurahan) use ($dataKasus, $tahun, $jenisKasus) {
            $jumlahKasus = $dataKasus[$kelurahan->id] ?? 0;
            return (object) [
                'id' => $kelurahan->id,
                'kecamatan' => $kelurahan->kecamatan,
                'kelurahan' => $kelurahan,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'spasial' => DataSpasialResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function spasialKeterpaparan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $kelurahanList = Kelurahan::when($request->kecamatan_id, function ($q) use ($request) {
            $q->where('kecamatan_id', $request->kecamatan_id);
        })->when($request->kelurahan_id, function ($q) use ($request) {
            $q->where('id', $request->kelurahan_id);
        })->get();

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('kelurahan_id', DB::raw('SUM(keterpaparan) as total_kasus'))
            ->groupBy('kelurahan_id')
            ->pluck('total_kasus', 'kelurahan_id');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $result = $kelurahanList->map(function ($kelurahan) use ($dataKasus, $tahun, $jenisKasus) {
            $jumlahKasus = $dataKasus[$kelurahan->id] ?? 0;
            return (object) [
                'id' => $kelurahan->id,
                'kecamatan' => $kelurahan->kecamatan,
                'kelurahan' => $kelurahan,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'spasial' => DataSpasialResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function spasialPotensialDampak(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $kelurahanList = Kelurahan::when($request->kecamatan_id, function ($q) use ($request) {
            $q->where('kecamatan_id', $request->kecamatan_id);
        })->when($request->kelurahan_id, function ($q) use ($request) {
            $q->where('id', $request->kelurahan_id);
        })->get();

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('kelurahan_id', DB::raw('SUM(potensial_dampak) as total_kasus'))
            ->groupBy('kelurahan_id')
            ->pluck('total_kasus', 'kelurahan_id');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $result = $kelurahanList->map(function ($kelurahan) use ($dataKasus, $tahun, $jenisKasus) {
            $jumlahKasus = $dataKasus[$kelurahan->id] ?? 0;
            return (object) [
                'id' => $kelurahan->id,
                'kecamatan' => $kelurahan->kecamatan,
                'kelurahan' => $kelurahan,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'spasial' => DataSpasialResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function all(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $jenisKasus = $request->jenis_resiko_id
            ? JenisKasus::find($request->jenis_resiko_id)
            : null;

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, fn($q) => $q->where('kecamatan_id', $request->kecamatan_id))
            ->when($request->kelurahan_id, fn($q) => $q->where('kelurahan_id', $request->kelurahan_id))
            ->when($request->jenis_resiko_id, fn($q) => $q->where('jenis_kasus_id', $request->jenis_resiko_id))
            ->where('tahun', $tahun)
            ->select(
                'bulan',
                DB::raw('SUM(kerentanan) as total_kerentanan'),
                DB::raw('SUM(keterpaparan) as total_keterpaparan'),
                DB::raw('SUM(potensial_dampak) as total_potensial_dampak'),
                DB::raw('COUNT(*) as jumlah_kasus')
            )
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $result = collect(range(1, 12))->map(function ($bulan) use ($dataKasus, $tahun, $jenisKasus) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)
                ->locale('id')
                ->translatedFormat('F');

            $data = $dataKasus[$bulan] ?? null;

            return (object) [
                'bulan' => $bulan,
                'nama_bulan' => ucfirst($namaBulan),
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'kerentanan' => (int) ($data->total_kerentanan ?? 0),
                'keterpaparan' => (int) ($data->total_keterpaparan ?? 0),
                'potensial_dampak' => (int) ($data->total_potensial_dampak ?? 0),
                'jumlah_kasus' => (int) ($data->jumlah_kasus ?? 0),
            ];
        });

        return ResponseFormatter::success([
            'stats' => DataStatistikResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function jumlahKasus(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(jumlah_kasus) as total_kasus'))
            ->groupBy('bulan')
            ->pluck('total_kasus', 'bulan');

        $result = collect(range(1, 12))->map(function ($bulan) use ($dataKasus, $tahun, $jenisKasus) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)
                ->locale('id')
                ->translatedFormat('F');

            $jumlahKasus = $dataKasus[$bulan] ?? 0;

            return (object) [
                'bulan' => $bulan,
                'nama_bulan' => ucfirst($namaBulan),
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'stats' => DataStatistikResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function kerentanan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(kerentanan) as total_kasus'))
            ->groupBy('bulan')
            ->pluck('total_kasus', 'bulan');

        $result = collect(range(1, 12))->map(function ($bulan) use ($dataKasus, $tahun, $jenisKasus) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)
                ->locale('id')
                ->translatedFormat('F');

            $jumlahKasus = $dataKasus[$bulan] ?? 0;

            return (object) [
                'bulan' => $bulan,
                'nama_bulan' => ucfirst($namaBulan),
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'stats' => DataStatistikResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function keterpaparan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(keterpaparan) as total_kasus'))
            ->groupBy('bulan')
            ->pluck('total_kasus', 'bulan');

        $result = collect(range(1, 12))->map(function ($bulan) use ($dataKasus, $tahun, $jenisKasus) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)
                ->locale('id')
                ->translatedFormat('F');

            $jumlahKasus = $dataKasus[$bulan] ?? 0;

            return (object) [
                'bulan' => $bulan,
                'nama_bulan' => ucfirst($namaBulan),
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'stats' => DataStatistikResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function potensialDampak(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $jenisKasus = null;
        if ($request->jenis_resiko_id) {
            $jenisKasus = JenisKasus::find($request->jenis_resiko_id);
        }

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->kelurahan_id, function ($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(potensial_dampak) as total_kasus'))
            ->groupBy('bulan')
            ->pluck('total_kasus', 'bulan');

        $result = collect(range(1, 12))->map(function ($bulan) use ($dataKasus, $tahun, $jenisKasus) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)
                ->locale('id')
                ->translatedFormat('F');

            $jumlahKasus = $dataKasus[$bulan] ?? 0;

            return (object) [
                'bulan' => $bulan,
                'nama_bulan' => ucfirst($namaBulan),
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => (int) $jumlahKasus ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'stats' => DataStatistikResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function tabelData(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $kelurahanList = Kelurahan::when($request->kecamatan_id, function ($q) use ($request) {
            $q->where('kecamatan_id', $request->kecamatan_id);
        })->when($request->kelurahan_id, function ($q) use ($request) {
            $q->where('id', $request->kelurahan_id);
        })->get();

        $dataKasus = Data::query()
            ->when($request->kecamatan_id, function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            })
            ->when($request->jenis_resiko_id, function ($q) use ($request) {
                $q->where('jenis_kasus_id', $request->jenis_resiko_id);
            })
            ->where('tahun', $tahun)
            ->select(
                'kelurahan_id',
                DB::raw('SUM(jumlah_kasus) as total_kasus'),
                DB::raw('SUM(keterpaparan) as total_keterpaparan'),
                DB::raw('SUM(kerentanan) as total_kerentanan'),
                DB::raw('SUM(potensial_dampak) as total_potensial_dampak')
            )
            ->groupBy('kelurahan_id')
            ->get()
            ->keyBy('kelurahan_id');

        $jenisKasus = $request->jenis_resiko_id
            ? JenisKasus::find($request->jenis_resiko_id)
            : null;

        $result = $kelurahanList->map(function ($kelurahan) use ($dataKasus, $tahun, $jenisKasus) {
            $kasus = $dataKasus[$kelurahan->id] ?? null;

            return (object) [
                'id' => $kelurahan->id,
                'kecamatan' => $kelurahan->kecamatan,
                'kelurahan' => $kelurahan,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => $kasus->total_kasus ?? 0,
                'keterpaparan' => $kasus->total_keterpaparan ?? 0,
                'kerentanan' => $kasus->total_kerentanan ?? 0,
                'potensial_dampak' => $kasus->total_potensial_dampak ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'spasial' => TabelDataResource::collection($result),
        ], 'Get Data Berhasil');
    }
    
    public function publikasi(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $foto = Foto::select('id', 'file', 'created_at')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'tipe' => 'foto',
                    'foto' => $item->file,
                    'link' => url('storage/' . $item->file),
                    'created_at' => $item->created_at,
                ];
            });

        $video = Video::select('id', 'link', 'created_at')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'tipe' => 'video',
                    'foto' => null,
                    'link' => $item->link,
                    'created_at' => $item->created_at,
                ];
            });

        $merged = $foto->merge($video)->sortByDesc('created_at')->values();

        $total = $merged->count();
        $items = $merged->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => url()->current()]
        );

        return ResponseFormatter::success([
            'publikasi' => PublikasiResource::collection($paginator->items()),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ], 'Get Data Berhasil');
    }
    
    public function publikasiFoto(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $fotos = Foto::select('id', 'file', 'created_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $fotos->getCollection()->transform(function ($item) {
            return (object) [
                'id' => $item->id,
                'tipe' => 'foto',
                'foto' => $item->file,
                'link' => url('storage/' . $item->file),
                'created_at' => $item->created_at,
            ];
        });

        return ResponseFormatter::success([
            'publikasi' => PublikasiResource::collection($fotos->items()),
            'pagination' => [
                'current_page' => $fotos->currentPage(),
                'per_page' => $fotos->perPage(),
                'total' => $fotos->total(),
                'last_page' => $fotos->lastPage(),
            ],
        ], 'Get Data Berhasil');
    }
    
    public function publikasiVideo(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $videos = Video::select('id', 'link', 'created_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $videos->getCollection()->transform(function ($item) {
            return (object) [
                'id' => $item->id,
                'tipe' => 'video',
                'foto' => null,
                'link' => $item->link,
                'created_at' => $item->created_at,
            ];
        });

        return ResponseFormatter::success([
            'publikasi' => PublikasiResource::collection($videos->items()),
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
                'last_page' => $videos->lastPage(),
            ],
        ], 'Get Data Berhasil');
    }
    
    public function kategoriDiskusi(Request $request)
    {
        $topik = Topik::get();

        return ResponseFormatter::success([
            'kategori' => KategoriDiskusiResource::collection($topik),
        ], 'Get Data Berhasil');
    }
    
    public function forumDiskusi(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $forums = Forum::orderByDesc('created_at')
            ->paginate($perPage);

        return ResponseFormatter::success([
            'forum' => ForumResource::collection($forums->items()),
            'pagination' => [
                'current_page' => $forums->currentPage(),
                'per_page' => $forums->perPage(),
                'total' => $forums->total(),
                'last_page' => $forums->lastPage(),
            ],
        ], 'Get Data Berhasil');
    }
    
    public function forumDiskusiDetail(Request $request, $forumId)
    {
        $forum = Forum::findOrFail($forumId);

        return ResponseFormatter::success([
            'forum' => ForumDetailResource::make($forum),
        ], 'Get Data Berhasil');
    }
    
    public function kategoriBerita(Request $request)
    {
        $topik = KategoriBerita::get();

        return ResponseFormatter::success([
            'kategori' => KategoriBeritaResource::collection($topik),
        ], 'Get Data Berhasil');
    }
    
    public function berita(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $beritas = Berita::orderByDesc('created_at')
            ->paginate($perPage);

        return ResponseFormatter::success([
            'berita' => BeritaResource::collection($beritas->items()),
            'pagination' => [
                'current_page' => $beritas->currentPage(),
                'per_page' => $beritas->perPage(),
                'total' => $beritas->total(),
                'last_page' => $beritas->lastPage(),
            ],
        ], 'Get Data Berhasil');
    }
    
    public function beritaDetail(Request $request, $forumId)
    {
        $berita = Berita::findOrFail($forumId);

        return ResponseFormatter::success([
            'berita' => BeritaDetailResource::make($berita),
        ], 'Get Data Berhasil');
    }

    //DATA RT
    public function tabelDataRt(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $rtList = RT::when($request->kecamatan_id, fn($q) =>
                $q->where('kecamatan_id', $request->kecamatan_id)
            )
            ->when($request->kelurahan_id, fn($q) =>
                $q->where('kelurahan_id', $request->kelurahan_id)
            )
            ->when($request->rw_id, fn($q) =>
                $q->where('rw_id', $request->rw_id)
            )
            ->when($request->rt_id, fn($q) =>
                $q->where('id', $request->rt_id)
            )
            ->get();

        $dataKasus = DataRt::query()
            ->when($request->kecamatan_id, fn($q) =>
                $q->where('kecamatan_id', $request->kecamatan_id)
            )
            ->when($request->kelurahan_id, fn($q) =>
                $q->where('kelurahan_id', $request->kelurahan_id)
            )
            ->when($request->rw_id, fn($q) =>
                $q->where('rw_id', $request->rw_id)
            )
            ->when($request->rt_id, fn($q) =>
                $q->where('rt_id', $request->rt_id)
            )
            ->when($request->jenis_resiko_id, fn($q) =>
                $q->where('jenis_kasus_id', $request->jenis_resiko_id)
            )
            ->where('tahun', $tahun)
            ->select(
                'rt_id',
                DB::raw('SUM(jumlah_kasus) as total_kasus'),
                DB::raw('SUM(keterpaparan) as total_keterpaparan'),
                DB::raw('SUM(kerentanan) as total_kerentanan'),
                DB::raw('SUM(potensial_dampak) as total_potensial_dampak')
            )
            ->groupBy('rt_id')
            ->get()
            ->keyBy('rt_id');

        $jenisKasus = $request->jenis_resiko_id
            ? JenisKasus::find($request->jenis_resiko_id)
            : null;

        $result = $rtList->map(function ($rt) use ($dataKasus, $tahun, $jenisKasus) {
            $kasus = $dataKasus[$rt->id] ?? null;

            return (object) [
                'id' => $rt->id,
                'kecamatan' => $rt->kecamatan,
                'kelurahan' => $rt->kelurahan,
                'rw' => $rt->rw,
                'rt' => $rt,
                'tahun' => (int) $tahun,
                'jenisKasus' => $jenisKasus->nama ?? 'Semua Jenis Resiko',
                'jumlah_kasus' => $kasus->total_kasus ?? 0,
                'keterpaparan' => $kasus->total_keterpaparan ?? 0,
                'kerentanan' => $kasus->total_kerentanan ?? 0,
                'potensial_dampak' => $kasus->total_potensial_dampak ?? 0,
            ];
        });

        return ResponseFormatter::success([
            'spasial' => TabelDataRtResource::collection($result),
        ], 'Get Data Berhasil');
    }
}
