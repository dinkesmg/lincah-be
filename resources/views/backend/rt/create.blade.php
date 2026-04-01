@extends('layouts/layoutMaster')

@section('title', ' RT - Forms')

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

        // ===============================
        // Change Kecamatan → load Kelurahan
        // ===============================
        $('#kecamatan_id').on('change', function () {
            let kecID = $(this).val();

            // reset kelurahan & rw
            $('#kelurahan_id').html('<option value="">Loading...</option>');
            $('#rw_id').html('<option value="">Pilih RW</option>');

            if (kecID) {
                $.ajax({
                    url: "/get-kelurahan/" + kecID,
                    type: "GET",
                    success: function (res) {
                        $('#kelurahan_id').empty();
                        $('#kelurahan_id').append('<option value="">Pilih Kelurahan</option>');

                        $.each(res, function (i, kel) {
                            $('#kelurahan_id').append('<option value="'+ kel.id +'">'+ kel.nama +'</option>');
                        });
                    }
                });
            } else {
                $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>');
            }
        });


        // ===============================
        // Change Kelurahan → load RW
        // ===============================
        $('#kelurahan_id').on('change', function () {
            let kelID = $(this).val();

            $('#rw_id').html('<option value="">Loading...</option>');

            if (kelID) {
                $.ajax({
                    url: "/get-rw/" + kelID,
                    type: "GET",
                    success: function (res) {
                        $('#rw_id').empty();
                        $('#rw_id').append('<option value="">Pilih RW</option>');

                        $.each(res, function (i, rw) {
                            $('#rw_id').append('<option value="'+ rw.id +'">'+ rw.nama +'</option>');
                        });
                    }
                });
            } else {
                $('#rw_id').html('<option value="">Pilih RW</option>');
            }
        });

    });
    </script>
    @if(isset($rt))
    <script>
    $(document).ready(function () {

        let kecID = "{{ $rt->kecamatan_id }}";
        let kelID = "{{ $rt->kelurahan_id }}";
        let rwID  = "{{ $rt->rw_id }}";

        // Load kelurahan dari kecamatan (preselect)
        $.ajax({
            url: "/get-kelurahan/" + kecID,
            type: "GET",
            success: function (res) {
                $('#kelurahan_id').empty();
                $.each(res, function (i, kel) {
                    $('#kelurahan_id').append('<option value="'+ kel.id +'">'+ kel.nama +'</option>');
                });
                $('#kelurahan_id').val(kelID).trigger('change');
            }
        });

        // Load RW dari kelurahan (preselect)
        $.ajax({
            url: "/get-rw/" + kelID,
            type: "GET",
            success: function (res) {
                $('#rw_id').empty();
                $.each(res, function (i, rw) {
                    $('#rw_id').append('<option value="'+ rw.id +'">'+ rw.nama +'</option>');
                });
                $('#rw_id').val(rwID);
            }
        });

    });
    </script>
    @endif
@endsection

@section('content')

    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Tambah RT</h5>
                </div>
                <div class="card-body">
                    <form action="@isset($rt) {{ route('rt.update', $rt->uuid) }} @endisset @empty($rt) {{ route('rt.store') }} @endempty"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($rt)
                            @method('PUT')
                        @endisset
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kecamatan_id">Kecamatan</label>
                            <div class="col-sm-10">
                                {{ html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), old('kecamatan_id', $rw->kecamatan_id ?? null))
                                    ->class('form-control select2')
                                    ->placeholder('Pilih Kecamatan')
                                    ->id('kecamatan_id')
                                    ->required() }}
                                @error('kecamatan_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kelurahan_id">Kelurahan</label>
                            <div class="col-sm-10">
                                {{ html()->select('kelurahan_id', [], old('kelurahan_id', $rt->kelurahan_id ?? null))
                                    ->class('form-control select2')
                                    ->id('kelurahan_id')
                                    ->placeholder('Pilih Kelurahan')
                                    ->required() }}
                                @error('kelurahan_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="rw_id">RW</label>
                            <div class="col-sm-10">
                                {{ html()->select('rw_id', [], old('rw_id', $rt->rw_id ?? null))
                                    ->class('form-control select2')
                                    ->id('rw_id')
                                    ->placeholder('Pilih RW')
                                    ->required() }}
                                @error('rw_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama">Nama</label>
                            <div class="col-sm-10">
                                {{html()->text('nama', isset($rt) ? $rt->nama : @old('nama'))->class('form-control')->placeholder('Masukkan Nama')->required(true)}}
                                @error('nama')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('rt.index') }}" class="btn btn-warning">Kembali</a>
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
