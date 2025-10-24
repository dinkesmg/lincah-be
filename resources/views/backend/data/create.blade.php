@extends('layouts/layoutMaster')

@section('title', ' Data - Forms')

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
            let selectedKelurahan = kelurahanSelect.data('selected') || "{{ old('kelurahan_id', (isset($data) ? $data->kelurahan_id : null)) }}";

            if (selectedKecamatan) {
                loadKelurahan(selectedKecamatan, selectedKelurahan);
            }
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
                    <form action="{{ route('monitoring.store', [$jenisKasus->uuid]) }}"
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
                            <label class="col-sm-2 col-form-label" for="kecamatan_id">Kecamatan</label>
                            <div class="col-sm-10">
                                {{html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), isset($data) ? $data->kecamatan_id : @old('kecamatan_id'))->class('form-control select2')->placeholder('Masukkan Kecamatan')->required(true)}}
                                @error('kecamatan_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kelurahan_id">Kelurahan</label>
                            <div class="col-sm-10">
                                {{html()->select('kelurahan_id', '', isset($data) ? $data->kelurahan_id : @old('kelurahan_id'))->class('form-control select2')->placeholder('Masukkan Kelurahan')->required(true)}}
                                @error('kelurahan_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('monitoring.index', [$jenisKasus->uuid]) }}" class="btn btn-warning">Kembali</a>
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
