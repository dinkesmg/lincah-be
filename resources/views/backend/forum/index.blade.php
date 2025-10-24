@extends('layouts/layoutMaster')

@section('title', 'Forum Diskusi List - Pages')

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
                        <i class="ti ti-file-text me-2"></i>Forum Diskusi
                    </h4>
                    <p class="text-muted mb-0">Forum Diskusi Kesehatan dan Masyarakat</p>
                </div>
                <div class="d-flex gap-2">
                    @can('forum-create')
                        <a href="{{ route('forum.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Forum Diskusi
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
                    @role('SUPERADMIN')
                    <div class="col-md-4 mb-3">
                        {{ html()->label('Pengguna')->class('form-label') }}
                        {{ html()->select('user_id', $user->pluck('name', 'id'), old('user_id'))->class('select2')->placeholder('Pilih Pengguna') }}
                    </div>    
                    <div class="col-md-4 mb-3">
                        {{ html()->label('Kecamatan')->class('form-label') }}
                        {{ html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), old('kecamatan_id'))->class('select2')->placeholder('Pilih Kecamatan') }}
                    </div>    
                    @endrole
                    <div class="col-md-4 mb-3">
                        {{ html()->label('Kategori')->class('form-label') }}
                        {{ html()->select('topik_id', $topik->pluck('nama', 'id'), old('topik_id'))->class('select2')->placeholder('Pilih Kategori') }}
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
                        <i class="ti ti-list me-2"></i>Daftar Forum Diskusi
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Total {{ $forums->total() }} data ditemukan
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary">
                        Halaman {{ $forums->currentPage() }} dari {{ $forums->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($forums->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Pengguna</th>
                                <th>Judul</th>
                                <th>Foto</th>
                                <th>Wilayah</th>
                                <th>Tanggal</th>
                                <th>Detail</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($forums->currentPage() - 1) * $forums->perPage() + 1;
                            @endphp
                            @foreach ($forums as $forum)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $no++ }}</td>
                                    <td>{{$forum->user->name ?? '-'}}</td>
                                    <td>
                                        {{$forum->judul}}<br><br>
                                        <span class="badge rounded-pill bg-info">{{$forum->topik->nama ?? '-'}}</span>
                                    </td>
                                    <td>
                                        <img src="{{ url('storage/' . $forum->foto) }}" 
                                            class="img-thumbnail preview-image" 
                                            style="height: 180px; width: auto; cursor:pointer;"
                                            alt="Foto" />
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-primary">{{$forum->kecamatan->nama ?? '-'}}</span>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($forum->created_at)->translatedFormat('l, d-m-Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('forum.show', $forum->uuid) }}" type="button" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <h6 class="dropdown-header">Pilih Aksi</h6>
                                                @can('forum-edit')
                                                    <a class="dropdown-item" href="{{ route('forum.edit', $forum->uuid) }}">
                                                        <i class="ti ti-pencil me-2"></i>Edit Forum Diskusi
                                                    </a>
                                                @endcan
                                                @can('forum-delete')
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('forum.destroy', $forum->uuid) }}" method="post" class="d-inline">
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
                    <p class="text-muted mb-3">Belum ada Forum Diskusi yang tersedia atau sesuai dengan filter pencarian.</p>
                    @can('forum-create')
                        <a href="{{ route('forum.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Data
                        </a>
                    @endcan
                </div>
            @endif
        </div>
        
        @if($forums->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $forums->firstItem() }} - {{ $forums->lastItem() }} 
                        dari {{ $forums->total() }} data
                    </div>
                    <div>
                        {{ $forums->appends(request()->query())->links('pagination::bootstrap-5') }}
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
