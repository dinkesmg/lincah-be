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
                    <h5 class="mb-0">Forum Diskusi</h5>
                </div>
                <div class="card-body">
                    <form action="@isset($forum) {{ route('forum.update', $forum->uuid) }} @endisset @empty($forum) {{ route('forum.store') }} @endempty"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($forum)
                            @method('PUT')
                        @endisset
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="topik_id">Kategori</label>
                            <div class="col-sm-10">
                                {{html()->select('topik_id', $topik->pluck('nama', 'id'), isset($forum) ? $forum->topik_id : @old('topik_id'))->class('form-control select2')->placeholder('Masukkan Kategori')->required(true)}}
                                @error('topik_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        @if (auth()->user()->getRole()->name != 'PUSKESMAS')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kecamatan_id">Wilayah</label>
                            <div class="col-sm-10">
                                {{html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), isset($forum) ? $forum->kecamatan_id : @old('kecamatan_id'))->class('form-control select2')->placeholder('Masukkan Kecamatan')->required(true)}}
                                @error('kecamatan_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="tanggal">Tanggal</label>
                            <div class="col-sm-10">
                                {{html()->date('tanggal', isset($forum) ? $forum->tanggal : @old('tanggal', date('Y-m-d')))->class('form-control')->placeholder('Masukkan Tanggal')->required(true)}}
                                @error('tanggal')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="judul">Judul Diskusi</label>
                            <div class="col-sm-10">
                                {{html()->text('judul', isset($forum) ? $forum->judul : @old('judul'))->class('form-control')->placeholder('Masukkan Judul')->required(true)}}
                                @error('judul')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                            
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="hasil">Hasil Diskusi</label>
                            <div class="col-sm-10">
                                {{html()->textarea('hasil', isset($forum) ? $forum->hasil : @old('hasil'))->class('form-control')->placeholder('Masukkan Hasil Diskusi')->required(true)}}
                                @error('hasil')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                            
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="rencana_tindak_lanjut">Rencana Tindak Lanjut</label>
                            <div class="col-sm-10">
                                {{html()->textarea('rencana_tindak_lanjut', isset($forum) ? $forum->rencana_tindak_lanjut : @old('rencana_tindak_lanjut'))->class('form-control')->placeholder('Masukkan Rencana Tindak Lanjut')->required(true)}}
                                @error('rencana_tindak_lanjut')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="foto">Foto{!! !isset($forum) ? '<span class="text-danger">*</span>' : '' !!}</label>
                            <div class="col-sm-10">
                                {{ html()->file('foto')->class('form-control')->id('formFile')->required(isset($forum) ? false : true) }}
                                @error('foto')
                                    <br>
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                @isset($forum->foto_url)
                                    <img src="{{ url('storage/' . $forum->foto) }}" 
                                        class="img-thumbnail preview-image mt-3" 
                                        style="height: 180px; width: auto; cursor:pointer;"
                                        alt="Foto" />
                                @endisset
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="link_dokumentasi">Link Dokumentasi</label>
                            <div class="col-sm-10">
                                {{html()->text('link_dokumentasi', isset($forum) ? $forum->link_dokumentasi : @old('link_dokumentasi'))->class('form-control')->placeholder('Masukkan Link Dokumentasi Jika Ada')->required(false)}}
                                @error('link_dokumentasi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('forum.index') }}" class="btn btn-warning">Kembali</a>
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
