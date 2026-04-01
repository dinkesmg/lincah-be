@extends('layouts/layoutMaster')

@section('title', ' RW - Forms')

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
    $(document).ready(function() {

        $('#kecamatan_id').on('change', function() {
            let kecamatanID = $(this).val();

            $('#kelurahan_id').html('<option value="">Loading...</option>');

            let url = "{{ route('getKelurahan', ['kecamatan_id' => '__ID__']) }}";
            url = url.replace('__ID__', kecamatanID);

            if (kecamatanID) {
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(res) {
                        $('#kelurahan_id').empty();
                        $('#kelurahan_id').append('<option value="">Pilih Kelurahan</option>');
                        
                        $.each(res, function(index, kel) {
                            $('#kelurahan_id').append(
                                '<option value="'+ kel.id +'">'+ kel.nama +'</option>'
                            );
                        });
                    }
                });
            } else {
                $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>');
            }
        });

    });
    </script>
    @if(isset($rw))
    <script>
    $(document).ready(function () {

        let kecID = "{{ $rw->kecamatan_id }}";
        let kelID = "{{ $rw->kelurahan_id }}";

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
                    <h5 class="mb-0">Tambah RW</h5>
                </div>
                <div class="card-body">
                    <form action="@isset($rw) {{ route('rw.update', $rw->uuid) }} @endisset @empty($rw) {{ route('rw.store') }} @endempty"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($rw)
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
                                {{ html()->select('kelurahan_id', [], old('kelurahan_id', $rw->kelurahan_id ?? null))
                                    ->class('form-control select2')
                                    ->placeholder('Pilih Kelurahan')
                                    ->id('kelurahan_id')
                                    ->required() }}
                                @error('kelurahan_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama">Nama</label>
                            <div class="col-sm-10">
                                {{html()->text('nama', isset($rw) ? $rw->nama : @old('nama'))->class('form-control')->placeholder('Masukkan Nama')->required(true)}}
                                @error('nama')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('rw.index') }}" class="btn btn-warning">Kembali</a>
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
