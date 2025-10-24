@extends('layouts/layoutMaster')

@section('title', ' Forum Diskusi - Form')

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
@endsection

@section('content')
    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Detail Forum Diskusi</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="topik_id">Kategori</label>
                            <div class="col-sm-10">
                                {{ html()->select('topik_id', $topik->pluck('nama', 'id'), $forum->topik_id ?? old('topik_id'))
                                    ->class('form-control select2')
                                    ->placeholder('Masukkan Kategori')
                                    ->attributes(['disabled' => true]) }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kecamatan_id">Wilayah</label>
                            <div class="col-sm-10">
                                {{ html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), $forum->kecamatan_id ?? old('kecamatan_id'))
                                    ->class('form-control select2')
                                    ->placeholder('Masukkan Kecamatan')
                                    ->attributes(['disabled' => true]) }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="judul">Judul Diskusi</label>
                            <div class="col-sm-10">
                                {{ html()->text('judul', $forum->judul ?? old('judul'))
                                    ->class('form-control')
                                    ->placeholder('Masukkan Judul')
                                    ->attributes(['readonly' => true]) }}
                            </div>
                        </div>
                            
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="hasil">Hasil Diskusi</label>
                            <div class="col-sm-10">
                                {{ html()->textarea('hasil', $forum->hasil ?? old('hasil'))
                                    ->class('form-control')
                                    ->placeholder('Masukkan Hasil Diskusi')
                                    ->attributes(['readonly' => true]) }}
                            </div>
                        </div>
                            
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="rencana_tindak_lanjut">Rencana Tindak Lanjut</label>
                            <div class="col-sm-10">
                                {{ html()->textarea('rencana_tindak_lanjut', $forum->rencana_tindak_lanjut ?? old('rencana_tindak_lanjut'))
                                    ->class('form-control')
                                    ->placeholder('Masukkan Rencana Tindak Lanjut')
                                    ->attributes(['readonly' => true]) }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="foto">Foto</label>
                            <div class="col-sm-10">
                                @isset($forum->foto)
                                    <img src="{{ url('storage/' . $forum->foto) }}" 
                                        class="img-thumbnail preview-image mt-3" 
                                        style="height: 180px; width: auto; cursor:pointer;"
                                        alt="Foto" />
                                @else
                                    <p class="form-control-plaintext">Tidak ada foto</p>
                                @endisset
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="link_dokumentasi">Link Dokumentasi</label>
                            <div class="col-sm-10">
                                {{ html()->text('link_dokumentasi', $forum->link_dokumentasi ?? old('link_dokumentasi'))
                                    ->class('form-control')
                                    ->placeholder('Masukkan Link Dokumentasi')
                                    ->attributes(['readonly' => true]) }}
                            </div>
                        </div>

                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('forum.index') }}" class="btn btn-warning">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
