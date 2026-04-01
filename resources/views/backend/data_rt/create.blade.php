@extends('layouts/layoutMaster')

@section('title', ' Data RT - Forms')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    @include('partials.success')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    const btnSubmit = form.querySelector('button[type="submit"]');
                    if (btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...`;
                    }
                });
            });
        });
    </script>
    
    <script>
    $(document).ready(function () {

        // ======== 1. Kecamatan → Kelurahan ========
        $('#kecamatan_id').on('change', function () {
            let kecID = $(this).val();

            $('#kelurahan_id').html('<option value="">Loading...</option>');
            $('#rw_id').html('<option value="">Pilih RW</option>');
            $('#rt_id').html('<option value="">Pilih RT</option>');

            if (kecID) {
                $.get('/get-kelurahan/' + kecID, function (res) {
                    $('#kelurahan_id').empty().append('<option value="">Pilih Kelurahan</option>');
                    $.each(res, (i, kel) => {
                        $('#kelurahan_id').append(`<option value="${kel.id}">${kel.nama}</option>`);
                    });
                });
            }
        });

        // ======== 2. Kelurahan → RW ========
        $('#kelurahan_id').on('change', function () {
            let kelID = $(this).val();

            $('#rw_id').html('<option value="">Loading...</option>');
            $('#rt_id').html('<option value="">Pilih RT</option>');

            if (kelID) {
                $.get('/get-rw/' + kelID, function (res) {
                    $('#rw_id').empty().append('<option value="">Pilih RW</option>');
                    $.each(res, (i, rw) => {
                        $('#rw_id').append(`<option value="${rw.id}">${rw.nama}</option>`);
                    });
                });
            }
        });

        // ======== 3. RW → RT ========
        $('#rw_id').on('change', function () {
            let rwID = $(this).val();

            $('#rt_id').html('<option value="">Loading...</option>');

            if (rwID) {
                $.get('/get-rt/' + rwID, function (res) {
                    $('#rt_id').empty().append('<option value="">Pilih RT</option>');
                    $.each(res, (i, rt) => {
                        $('#rt_id').append(`<option value="${rt.id}">${rt.nama}</option>`);
                    });
                });
            }
        });

    });
    </script>

@endsection

@section('content')

    @php
        $bulanOptions = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    @endphp

    <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Update Data {{ $jenisKasus->nama }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('monitoring-rt.store', [$jenisKasus->uuid]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($data)
                            @method('PUT')
                        @endisset
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="bulan">Bulan</label>
                            <div class="col-sm-10">
                                {{
                                    html()
                                        ->select('bulan', $bulanOptions, isset($data) ? $data->bulan : old('bulan', date('m')))
                                        ->class('form-control select2')
                                        ->placeholder('Pilih Bulan')
                                        ->required(true)
                                }}                               
                                @error('bulan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Kecamatan</label>
                            <div class="col-sm-10">
                                {{ html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), old('kecamatan_id', $rt->kecamatan_id ?? null))
                                    ->class('form-control select2')
                                    ->id('kecamatan_id')
                                    ->placeholder('Pilih Kecamatan')
                                    ->required() }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Kelurahan</label>
                            <div class="col-sm-10">
                                {{ html()->select('kelurahan_id', [], old('kelurahan_id', $rt->kelurahan_id ?? null))
                                    ->class('form-control select2')
                                    ->id('kelurahan_id')
                                    ->placeholder('Pilih Kelurahan')
                                    ->required() }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">RW</label>
                            <div class="col-sm-10">
                                {{ html()->select('rw_id', [], old('rw_id', $rt->rw_id ?? null))
                                    ->class('form-control select2')
                                    ->id('rw_id')
                                    ->placeholder('Pilih RW')
                                    ->required() }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">RT</label>
                            <div class="col-sm-10">
                                {{ html()->select('rt_id', [], old('rt_id', $rt->id ?? null))
                                    ->class('form-control select2')
                                    ->id('rt_id')
                                    ->placeholder('Pilih RT')
                                    ->required() }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="keterpaparan">Keterpaparan</label>
                            <div class="col-sm-10">
                                {{html()->number('keterpaparan', isset($data) ? $data->keterpaparan : @old('keterpaparan'))->class('form-control')->placeholder('Masukkan Keterpaparan')->required(true)}}
                                @error('keterpaparan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kerentanan">Kerentanan</label>
                            <div class="col-sm-10">
                                {{html()->number('kerentanan', isset($data) ? $data->kerentanan : @old('kerentanan'))->class('form-control')->placeholder('Masukkan Kerentanan')->required(true)}}
                                @error('kerentanan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="potensial_dampak">Potensial Dampak</label>
                            <div class="col-sm-10">
                                {{html()->number('potensial_dampak', isset($data) ? $data->potensial_dampak : @old('potensial_dampak'))->class('form-control')->placeholder('Masukkan Potensial Dampak')->required(true)}}
                                @error('potensial_dampak')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jumlah_kasus">Jumlah Kasus</label>
                            <div class="col-sm-10">
                                {{html()->number('jumlah_kasus', isset($data) ? $data->jumlah_kasus : @old('jumlah_kasus'))->class('form-control')->placeholder('Masukkan Jumlah Kasus')->required(true)}}
                                @error('jumlah_kasus')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="image">Peta</label>
                            <div class="col-sm-10">
                                <input type="file" 
                                    name="image" 
                                    id="image" 
                                    class="form-control" 
                                    accept="image/*">

                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                @if(isset($data) && $data->image)
                                    <div class="mt-2">
                                        <small class="text-muted">Gambar saat ini:</small><br>
                                        <img src="{{ asset('storage/'.$data->image) }}" 
                                            alt="Peta" 
                                            class="img-thumbnail mt-1" 
                                            style="width: 150px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('monitoring-rt.index', [$jenisKasus->uuid]) }}" class="btn btn-warning">Kembali</a>
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
