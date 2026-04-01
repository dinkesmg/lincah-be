@extends('layouts/layoutMaster')

@section('title', 'Data RT List - Pages')

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

            let kecamatanSelect  = $('#kecamatan_id');
            let kelurahanSelect  = $('#kelurahan_id');
            let rwSelect         = $('#rw_id');
            let rtSelect         = $('#rt_id');

            function loadKelurahan(kecamatanId, selectedKelurahan = null) {
                kelurahanSelect.empty().append('<option value="">Memuat...</option>');
                rwSelect.empty().append('<option value="">Pilih RW</option>');
                rtSelect.empty().append('<option value="">Pilih RT</option>');

                if (!kecamatanId) {
                    kelurahanSelect.empty().append('<option value="">Pilih Kecamatan Terlebih Dahulu</option>');
                    return;
                }

                $.get('/get-kelurahan/' + kecamatanId, function(data) {
                    kelurahanSelect.empty().append('<option value="">Pilih Kelurahan</option>');
                    $.each(data, function(i, kel) {
                        kelurahanSelect.append(`<option value="${kel.id}" ${selectedKelurahan == kel.id ? 'selected':''}>${kel.nama}</option>`);
                    });
                });
            }

            function loadRW(kelurahanId, selectedRW = null) {
                rwSelect.empty().append('<option value="">Memuat...</option>');
                rtSelect.empty().append('<option value="">Pilih RT</option>');

                if (!kelurahanId) {
                    rwSelect.empty().append('<option value="">Pilih Kelurahan Terlebih Dahulu</option>');
                    return;
                }

                $.get('/get-rw/' + kelurahanId, function(data) {
                    rwSelect.empty().append('<option value="">Pilih RW</option>');
                    $.each(data, function(i, rw) {
                        rwSelect.append(`<option value="${rw.id}" ${selectedRW == rw.id ? 'selected':''}>${rw.nama}</option>`);
                    });
                });
            }

            function loadRT(rwId, selectedRT = null) {
                rtSelect.empty().append('<option value="">Memuat...</option>');

                if (!rwId) {
                    rtSelect.empty().append('<option value="">Pilih RW Terlebih Dahulu</option>');
                    return;
                }

                $.get('/get-rt/' + rwId, function(data) {
                    rtSelect.empty().append('<option value="">Pilih RT</option>');
                    $.each(data, function(i, rt) {
                        rtSelect.append(`<option value="${rt.id}" ${selectedRT == rt.id ? 'selected':''}>${rt.nama}</option>`);
                    });
                });
            }

            kecamatanSelect.on('change', function() {
                loadKelurahan($(this).val());
            });

            kelurahanSelect.on('change', function() {
                loadRW($(this).val());
            });

            rwSelect.on('change', function() {
                loadRT($(this).val());
            });

            let selectedKecamatan = "{{ old('kecamatan_id', request()->kecamatan_id) }}";
            let selectedKelurahan = "{{ old('kelurahan_id', request()->kelurahan_id) }}";
            let selectedRW        = "{{ old('rw_id', request()->rw_id) }}";
            let selectedRT        = "{{ old('rt_id', request()->rt_id) }}";

            if (selectedKecamatan) {
                loadKelurahan(selectedKecamatan, selectedKelurahan);

                if (selectedKelurahan) {
                    loadRW(selectedKelurahan, selectedRW);

                    if (selectedRW) {
                        loadRT(selectedRW, selectedRT);
                    }
                }
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
                    @can('data_rt-create')
                        <a href="{{ route('monitoring-rt.create', [$jenisKasus->uuid]) }}" class="btn btn-info">
                            <i class="ti ti-refresh me-1"></i>Update Data
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

                    <div class="col-md-6 mb-3">
                        {{ html()->label('RW')->class('form-label') }}
                        {{ html()->select('rw_id', [], old('rw_id'))->class('form-control select2')->placeholder('Pilih RW')->id('rw_id') }}
                    </div>

                    <div class="col-md-6 mb-3">
                        {{ html()->label('RT')->class('form-label') }}
                        {{ html()->select('rt_id', [], old('rt_id'))->class('form-control select2')->placeholder('Pilih RT')->id('rt_id') }}
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
