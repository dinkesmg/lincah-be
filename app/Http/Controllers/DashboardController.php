<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Models\Forum;
use App\Models\Data;
use App\Models\JenisKasus;
use App\Models\Kelurahan;
use App\Models\Foto;
use App\Models\Video;

class DashboardController extends Controller
{
    public function index()
    {
      $role = auth()->user()->getRole()->name;

      if ($role == 'SUPERADMIN') {
          $forum = Forum::count();
      } else {
          $forum = Forum::whereUserId(auth()->user()->id)->count();
      }
      
      $jenisKasusCount = JenisKasus::count();
      $wilayah = Kelurahan::count();
      $foto = Foto::count();
      $video = Video::count();
      $publikasi = $foto + $video;

      $tahun = session('tahun', date('Y'));

      $data = Data::select(
              'jenis_kasus_id',
              DB::raw('SUM(keterpaparan) as keterpaparan'),
              DB::raw('SUM(kerentanan) as kerentanan'),
              DB::raw('SUM(potensial_dampak) as potensial_dampak'),
              DB::raw('SUM(jumlah_kasus) as jumlah_kasus'),
              'bulan'
          )
          ->where('tahun', $tahun)
          ->groupBy('bulan', 'jenis_kasus_id')
          ->get();

      $jenisKasus = JenisKasus::pluck('nama', 'id');
      $bulan = range(1, 12);
        
      $namaBulan = [
          'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
          'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
      ];

      $series = [];

      foreach ($jenisKasus as $id => $nama) {
          $dataPerJenis = $data->where('jenis_kasus_id', $id);

          $values = [];
          foreach ($bulan as $b) {
              $record = $dataPerJenis->firstWhere('bulan', $b);
              $values[] = $record ? (int) $record->jumlah_kasus : 0;
          }

          $series[] = [
              'name' => $nama,
              'data' => $values,
          ];
      }

      $series = json_encode($series);
      $bulan = json_encode($namaBulan);
      $tahun = $tahun;

      return view('content.dashboard.dashboards-analytics', compact('role', 'forum', 'jenisKasusCount', 'wilayah', 'publikasi', 'series', 'bulan', 'tahun'));
    }
}
