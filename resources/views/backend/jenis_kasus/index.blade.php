@extends('layouts/layoutMaster')

@section('title', 'Jenis Kasus List - Pages')

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
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold py-3 mb-2">
                        <i class="ti ti-file-text me-2"></i>Jenis Kasus
                    </h4>
                    <p class="text-muted mb-0">Daftar Jenis Kasus Potensial Dampak</p>
                </div>
                <div class="d-flex gap-2">
                    @can('jenis_kasus-create')
                        <a href="{{ route('jenis_kasus.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Jenis Kasus
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <i class="ti ti-search me-2"></i>
                <h5 class="card-title mb-0">Filter Pencarian</h5>
            </div>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row g-3">
                    <div class="col-md-4 mb-3">
                        {{ html()->label('Nama')->class('form-label') }}
                        {{ html()->text('nama', old('nama'))->class('form-control')->placeholder('Cari Nama') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-search me-1"></i>Cari Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('partials.errors')
    
    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="ti ti-list me-2"></i>Daftar Jenis Kasus
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Total {{ $jenis_kasuses->total() }} data ditemukan
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary">
                        Halaman {{ $jenis_kasuses->currentPage() }} dari {{ $jenis_kasuses->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($jenis_kasuses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($jenis_kasuses->currentPage() - 1) * $jenis_kasuses->perPage() + 1;
                            @endphp
                            @foreach ($jenis_kasuses as $jenis_kasus)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $no++ }}</td>
                                    <td>{{$jenis_kasus->nama}}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <h6 class="dropdown-header">Pilih Aksi</h6>
                                                @can('jenis_kasus-edit')
                                                    <a class="dropdown-item" href="{{ route('jenis_kasus.edit', $jenis_kasus->uuid) }}">
                                                        <i class="ti ti-pencil me-2"></i>Edit Jenis Kasus
                                                    </a>
                                                @endcan
                                                @can('jenis_kasus-delete')
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('jenis_kasus.destroy', $jenis_kasus->uuid) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?\n\nTindakan ini tidak dapat dibatalkan.')">
                                                            <i class="ti ti-trash me-2"></i>Hapus
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="ti ti-file-x" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-2">Tidak Ada Data</h5>
                    <p class="text-muted mb-3">Belum ada Jenis Kasus yang tersedia atau sesuai dengan filter pencarian.</p>
                    @can('jenis_kasus-create')
                        <a href="{{ route('jenis_kasus.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Data
                        </a>
                    @endcan
                </div>
            @endif
        </div>
        
        @if($jenis_kasuses->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $jenis_kasuses->firstItem() }} - {{ $jenis_kasuses->lastItem() }} 
                        dari {{ $jenis_kasuses->total() }} data
                    </div>
                    <div>
                        {{ $jenis_kasuses->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
