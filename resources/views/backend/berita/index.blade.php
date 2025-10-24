@extends('layouts/layoutMaster')

@section('title', 'Berita List - Pages')

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
    <script>
    $(document).on('click', '.preview-image', function () {
        const src = $(this).attr('src');
        $('#modalImage').attr('src', src);
        $('#imageModal').modal('show');
    });
    </script>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold py-3 mb-2">
                        <i class="ti ti-file-text me-2"></i>Berita
                    </h4>
                    <p class="text-muted mb-0">Daftar Publikasi Berita</p>
                </div>
                <div class="d-flex gap-2">
                    @can('berita-create')
                        <a href="{{ route('berita.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Berita
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
                            {{ html()->label('Judul')->class('form-label') }}
                            {{ html()->text('judul', old('judul'))->class('form-control')->placeholder('Cari Judul') }}
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
                        <i class="ti ti-list me-2"></i>Daftar Berita
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Total {{ $beritas->total() }} data ditemukan
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary">
                        Halaman {{ $beritas->currentPage() }} dari {{ $beritas->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($beritas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Kategori Berita</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($beritas->currentPage() - 1) * $beritas->perPage() + 1;
                            @endphp
                            @foreach ($beritas as $berita)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $no++ }}</td>
                                    <td>
                                        <img src="{{ url('storage/' . $berita->gambar) }}" 
                                            class="img-thumbnail preview-image" 
                                            style="height: auto; width: 180px; cursor:pointer;"
                                            alt="Foto" />
                                    </td>
                                    <td>{{$berita->judul}}</td>
                                    <td>{{$berita->kategori->nama ?? '-'}}</td>
                                    <td>{{ \Carbon\Carbon::parse($berita->tanggal)->format('d-m-Y') }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <h6 class="dropdown-header">Pilih Aksi</h6>
                                                @can('berita-edit')
                                                    <a class="dropdown-item" href="{{ route('berita.edit', $berita->uuid) }}">
                                                        <i class="ti ti-pencil me-2"></i>Edit Berita
                                                    </a>
                                                @endcan
                                                @can('berita-delete')
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('berita.destroy', $berita->uuid) }}" method="post" class="d-inline">
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
                    <p class="text-muted mb-3">Belum ada Berita yang tersedia atau sesuai dengan filter pencarian.</p>
                    @can('berita-create')
                        <a href="{{ route('berita.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Dokumen Pertama
                        </a>
                    @endcan
                </div>
            @endif
        </div>
        
        @if($beritas->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $beritas->firstItem() }} - {{ $beritas->lastItem() }} 
                        dari {{ $beritas->total() }} data
                    </div>
                    <div>
                        {{ $beritas->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid rounded" alt="Preview" />
                </div>
            </div>
        </div>
    </div>
@endsection
