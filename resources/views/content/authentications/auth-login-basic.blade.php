@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('vendor-style')
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <a href="{{ url('') }}" class="app-brand auth-cover-brand">
            <span class="app-brand-logo demo">
                @include('_partials.macros', ['height' => '100%'])
            </span>
            <span class="app-brand-text demo text-heading fw-bold">DINKES KOTA SEMARANG</span>
        </a>
        <div class="authentication-inner row m-0">
            <div class="d-none d-xl-flex col-xl-8 p-0">
                <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                    <img src="{{ url('assets/img/login-cover.png') }}" alt="auth-login-cover" class="my-5 auth-illustration" />
                    <img src="{{ url('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-login-cover" class="platform-bg"/>
                </div>
            </div>
            <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1 pt-2">Selamat Datang di LINCAH</h4>
                    <p class="mb-6">Silahkan Masuk Menggunakan Akun Anda</p>
                    
                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            {{ html()->label()->text('Username')->class('form-label') }}
                            {{ html()->email('email')->id('email')->class('form-control')->placeholder('Masukkan Email')->value(@old('email')) }}
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                {{ html()->label()->text('Password')->class('form-label') }}
                            </div>
                            <div class="input-group input-group-merge">
                                {{ html()->password('password')->id('password')->placeholder('Masukkan Password')->class('form-control')->required() }}
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me">
                                <label class="form-check-label" for="remember-me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button style="submit" class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
