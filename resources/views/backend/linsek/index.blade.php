@extends('layouts/layoutMaster')

@section('title', 'Linsek Tematik')

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
@endsection

@section('content')
    <!-- Header Section -->
    
    @include('partials.errors')
    
    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="ti ti-list me-2"></i>Data Linsek Tematik
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @can('linsek-create')
                        <a href="{{ route('linsek.create') }}" class="btn btn-primary">
                            <i class="ti ti-pencil me-1"></i>Update Linsek Tematik
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-5">

            {!! $linseks->content !!}
            
        </div>
        
    </div>
@endsection
