@extends('layouts/layoutMaster')

@section('title', 'Data List - Pages')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    @include('partials.success')
    
    <script>
        $(document).ready(function () {
            let kelurahanSelect = $('#kelurahan_id');
            let kecamatanSelect = $('#kecamatan_id');

            function loadKelurahan(kecamatanId, selectedKelurahan = null) {
                kelurahanSelect.empty().append('<option value="">Memuat...</option>');

                if (kecamatanId) {
                    $.ajax({
                        url: "{{ url('get-kelurahan') }}/" + kecamatanId,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            kelurahanSelect.empty().append('<option value="">Pilih Kelurahan</option>');
                            $.each(data, function (key, kel) {
                                let selected = (selectedKelurahan == kel.id) ? 'selected' : '';
                                kelurahanSelect.append('<option value="' + kel.id + '" ' + selected + '>' + kel.nama + '</option>');
                            });
                        },
                        error: function () {
                            kelurahanSelect.empty().append('<option value="">Gagal memuat data</option>');
                        }
                    });
                } else {
                    kelurahanSelect.empty().append('<option value="">Pilih Kecamatan Terlebih Dahulu</option>');
                }
            }

            kecamatanSelect.on('change', function () {
                let kecamatanId = $(this).val();
                loadKelurahan(kecamatanId);
            });

            let selectedKecamatan = kecamatanSelect.val();
            let selectedKelurahan = kelurahanSelect.data('selected') || "{{ old('kelurahan_id', request()->kelurahan_id) }}";

            if (selectedKecamatan) {
                loadKelurahan(selectedKecamatan, selectedKelurahan);
            }
        });
    </script>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold py-3 mb-2">
                        <i class="ti ti-file-text me-2"></i>{{ $jenisKasus->nama }}
                    </h4>
                    <p class="text-muted mb-0">Potensial Dampak {{ $jenisKasus->nama }}</p>
                </div>
                <div class="d-flex gap-2">
                    @can('data-create')
                        <a href="{{ route('monitoring.create', [$jenisKasus->uuid]) }}" class="btn btn-info">
                            <i class="ti ti-refresh me-1"></i>Update Data
                        </a>
                    @endcan
                    @can('data-edit')
                        <a href="{{ route('monitoring.import.form', [$jenisKasus->uuid]) }}" class="btn btn-warning">
                            <i class="ti ti-upload me-1"></i>Import Data
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <i class="ti ti-search me-2"></i>
                <h5 class="card-title mb-0">Filter Pencarian</h5>
            </div>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        {{ html()->label('Kecamatan')->class('form-label') }}
                        {{ html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), old('kecamatan_id', request()->kecamatan_id))->class('form-control select2')->placeholder('Pilih Kecamatan')->id('kecamatan_id') }}
                    </div>

                    <div class="col-md-6 mb-3">
                        {{ html()->label('Kelurahan')->class('form-label') }}
                        {{ html()->select('kelurahan_id', [], old('kelurahan_id'))->class('form-control select2')->placeholder('Pilih Kelurahan')->id('kelurahan_id') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-search me-1"></i>Cari Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('partials.errors')
    
    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="ti ti-list me-2"></i>Potensial Dampak {{ $jenisKasus->nama }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Bulan</th>
                            <th>Keterpaparan</th>
                            <th>Kerentanan</th>
                            <th>Potensial Dampak</th>
                            <th>Jumlah Kasus</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($data as $i => $row)
                            <tr>
                                <td class="text-center fw-semibold">{{ $i + 1 }}</td>
                                <td>{{ \Carbon\Carbon::create()->month($row['bulan'])->translatedFormat('F') }}</td>
                                <td>{{ $row['keterpaparan'] }}</td>
                                <td>{{ $row['kerentanan'] }}</td>
                                <td>{{ $row['potensial_dampak'] }}</td>
                                <td>{{ $row['jumlah_kasus'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
@endsection
