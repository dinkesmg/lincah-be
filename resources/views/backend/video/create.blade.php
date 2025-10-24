@extends('layouts/layoutMaster')

@section('title', ' Video - Forms')

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
                    <h5 class="mb-0">Video</h5>
                </div>
                <div class="card-body">
                    <form action="@isset($video) {{ route('video.update', $video->uuid) }} @endisset @empty($video) {{ route('video.store') }} @endempty"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($video)
                            @method('PUT')
                        @endisset
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="link">Link Youtube</label>
                            <div class="col-sm-10">
                                {{html()->text('link', isset($video) ? $video->link : @old('link'))->class('form-control')->placeholder('Masukkan Link Video')->required(true)}}
                                @error('link')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('video.index') }}" class="btn btn-warning">Kembali</a>
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
