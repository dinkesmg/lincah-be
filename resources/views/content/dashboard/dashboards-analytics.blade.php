@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-advance.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var options = {
      series: {!! $series !!},
      chart: {
        height: 350,
        type: 'line',
        dropShadow: {
          enabled: true,
          color: '#000',
          top: 18,
          left: 7,
          blur: 10,
          opacity: 0.2
        },
        zoom: { enabled: false },
        toolbar: { show: false }
      },
      colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'],
      dataLabels: { enabled: true },
      stroke: { curve: 'smooth' },
      title: {
        text: 'Jumlah per Jenis Kasus ({{ $tahun }})',
        align: 'left'
      },
      grid: {
        borderColor: '#e7e7e7',
        row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 }
      },
      markers: { size: 3 },
      xaxis: {
        categories: {!! $bulan !!},
        title: { text: 'Bulan' }
      },
      yaxis: {
        title: { text: 'Jumlah Kasus' },
        min: 0
      },
      legend: {
        position: 'top',
        horizontalAlign: 'right',
        floating: true,
        offsetY: -25,
        offsetX: -5
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
  });
</script>
@endsection

@section('content')

<div class="row g-6">
    <div class="col-xl-5">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-7">
            <div class="card-body text-nowrap">
              <h5 class="card-title mb-0">Selamat Datang!</h5>
              <h4 class="text-primary mb-1">{{ auth()->user()->name ?? '-' }}</h4>
            </div>
          </div>
          <div class="col-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img src="{{ url('assets/img/illustrations/rocket.png') }}" height="140" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-7 col-md-12">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title mb-0">Data</h5>
        </div>
        <div class="card-body d-flex align-items-end">
          <div class="w-100">
            <div class="row gy-3">
                
                @if ($role == 'SUPERADMIN')

                <div class="col-md-3 col-6">
                    <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-success me-4 p-2">
                        <i class="ti ti-chart-pie-2 fs-4"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $jenisKasusCount }}</h5>
                        <small>Jenis Kasus</small>
                    </div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-info me-4 p-2">
                        <i class="ti ti-users fs-4"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $wilayah }}</h5>
                        <small>Wilayah</small>
                    </div>
                    </div>
                </div>

                @endif

                <div class="col-md-3 col-6">
                    <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-danger me-4 p-2">
                        <i class="ti ti-messages fs-4"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $forum }}</h5>
                        <small>Forum Diskusi</small>
                    </div>
                    </div>
                </div>
                
                @if ($role == 'SUPERADMIN')

                <div class="col-md-3 col-6">
                    <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-warning me-4 p-2">
                        <i class="ti ti-news fs-4"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $publikasi }}</h5>
                        <small>Publikasi</small>
                    </div>
                    </div>
                </div>

                @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-12 mt-3">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title mb-0">Statistik</h5>
        </div>
        <div class="card-body d-flex align-items-end">
            <div class="w-100">
                <div id="chart"></div>
            </div>
        </div>
      </div>
    </div>

  </div>

@endsection