@extends('layouts/layoutMaster')

@section('title', ' Berita - Forms')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    @include('partials.success')
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200,
                placeholder: 'Masukkan konten di sini...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['fontsize', 'color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['codeview']]
                ]
            });
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Berita</h5>
                </div>
                <div class="card-body">
                    <form action="@isset($berita) {{ route('berita.update', $berita->uuid) }} @endisset @empty($berita) {{ route('berita.store') }} @endempty"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($berita)
                            @method('PUT')
                        @endisset
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="judul">Judul</label>
                            <div class="col-sm-10">
                                {{html()->text('judul', isset($berita) ? $berita->judul : @old('judul'))->class('form-control')->placeholder('Masukkan Judul')->required(true)}}
                                @error('judul')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                            
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="deskripsi">Deskripsi</label>
                            <div class="col-sm-10">
                                {{html()->textarea('deskripsi', isset($berita) ? $berita->deskripsi : @old('deskripsi'))->class('form-control summernote')->placeholder('Masukkan Deskripsi')->required(true)}}
                                @error('deskripsi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="gambar">Gambar</label>
                            <div class="col-sm-10">
                                {{ html()->file('gambar')->class('form-control')->id('formFile')->required(isset($berita) ? false : true) }}
                                @error('gambar')
                                    <br>
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                @isset($berita)
                                    <a href="{{ $berita->gambar_url }}" target="_blank">Buka File</a>
                                @endisset
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="kategori_berita_id">Kategori Berita</label>
                            <div class="col-sm-10">
                                {{html()->select('kategori_berita_id', $kategori->pluck('nama', 'id'), isset($forum) ? $forum->kategori_berita_id : @old('kategori_berita_id'))->class('form-control select2')->placeholder('Masukkan Kategori Berita')->required(true)}}
                                @error('kategori_berita_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="tanggal">Tanggal</label>
                            <div class="col-sm-10">
                                {{html()->date('tanggal', isset($berita) ? $berita->tanggal : @old('tanggal'))->class('form-control')->placeholder('Masukkan Tanggal')->required(true)}}
                                @error('tanggal')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('berita.index') }}" class="btn btn-warning">Kembali</a>
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
