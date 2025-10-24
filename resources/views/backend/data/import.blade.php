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
    
@endsection

@section('content')

    <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Import Data {{ $jenisKasus->nama }}</h5>
                    
                    <a href="{{ url('assets/xlsx/template.xlsx') }}" class="btn btn-info">
                        <i class="ti ti-download me-1"></i>Download Template
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('monitoring.import.store', [$jenisKasus->uuid]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                            @error('file')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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
