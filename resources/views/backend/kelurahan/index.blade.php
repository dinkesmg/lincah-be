@extends('layouts/layoutMaster')

@section('title', 'Kelurahan List - Pages')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    @include('partials.success')

    <script>
        $(document).ready(function () {
            let map = null;
            let marker = null;

            $('.btn-lihat-peta').on('click', function () {
                let koordinat = $(this).data('koordinat');
                let nama = $(this).data('nama');
                if (!koordinat) return;

                let [lat, lng] = koordinat.split(',').map(parseFloat);

                $('#modalPeta').modal('show');

                $('#modalPeta').one('shown.bs.modal', function () {
                    if (map) {
                        map.remove();
                    }

                    map = L.map('leafletMap', {
                        center: [lat, lng],
                        zoom: 13,
                        zoomControl: false
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    L.control.zoom({ position: 'bottomleft' }).addTo(map);

                    marker = L.marker([lat, lng]).addTo(map).bindPopup(nama).openPopup();
                });
            });

            $('#modalPeta').on('hidden.bs.modal', function () {
                if (map) {
                    map.remove();
                    map = null;
                    marker = null;
                }
            });
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
                        <i class="ti ti-file-text me-2"></i>Kelurahan
                    </h4>
                    <p class="text-muted mb-0">Daftar Kelurahan Kota Semarang</p>
                </div>
                <div class="d-flex gap-2">
                    @can('kelurahan-create')
                        <a href="{{ route('kelurahan.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Kelurahan
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
                        {{ html()->label('Kecamatan')->class('form-label') }}
                        {{ html()->select('kecamatan_id', $kecamatan->pluck('nama', 'id'), old('kecamatan_id', request()->kecamatan_id))->class('select2')->placeholder('Pilih Kecamatan') }}
                    </div>    
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
                        <i class="ti ti-list me-2"></i>Daftar Kelurahan
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Total {{ $kelurahans->total() }} data ditemukan
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary">
                        Halaman {{ $kelurahans->currentPage() }} dari {{ $kelurahans->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($kelurahans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Kecamatan</th>
                                <th>Nama</th>
                                <th>Koordinat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($kelurahans->currentPage() - 1) * $kelurahans->perPage() + 1;
                            @endphp
                            @foreach ($kelurahans as $kelurahan)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $no++ }}</td>
                                    <td>{{$kelurahan->kecamatan->nama}}</td>
                                    <td>{{$kelurahan->nama}}</td>
                                    <td class="text-center">
                                        @if ($kelurahan->koordinat)
                                            <button type="button" class="btn btn-sm btn-info btn-lihat-peta" 
                                                data-koordinat="{{ $kelurahan->koordinat }}" 
                                                data-nama="{{ $kelurahan->nama }}">
                                                <i class="ti ti-map me-1"></i> Lihat Peta
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <h6 class="dropdown-header">Pilih Aksi</h6>
                                                @can('kelurahan-edit')
                                                    <a class="dropdown-item" href="{{ route('kelurahan.edit', $kelurahan->uuid) }}">
                                                        <i class="ti ti-pencil me-2"></i>Edit Kelurahan
                                                    </a>
                                                @endcan
                                                @can('kelurahan-delete')
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('kelurahan.destroy', $kelurahan->uuid) }}" method="post" class="d-inline">
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
                    <p class="text-muted mb-3">Belum ada Kelurahan yang tersedia atau sesuai dengan filter pencarian.</p>
                    @can('kelurahan-create')
                        <a href="{{ route('kelurahan.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Tambah Data
                        </a>
                    @endcan
                </div>
            @endif
        </div>
        
        @if($kelurahans->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $kelurahans->firstItem() }} - {{ $kelurahans->lastItem() }} 
                        dari {{ $kelurahans->total() }} data
                    </div>
                    <div>
                        {{ $kelurahans->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalPeta" tabindex="-1" aria-labelledby="modalPetaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPetaLabel">Peta Lokasi Kelurahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height: 450px;">
                <div id="leafletMap" style="height: 100%; width: 100%;"></div>
            </div>
            </div>
        </div>
    </div>
@endsection
