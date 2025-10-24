@extends('layouts/layoutMaster')

@section('title', ' Kecamatan - Form')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

    <script>
        $(document).ready(function () {
            let defaultLat = -2.5489;
            let defaultLng = 118.0149;

            let oldValue = $('#koordinat').val();
            if (oldValue) {
                let parts = oldValue.split(',');
                if (parts.length === 2) {
                    defaultLat = parseFloat(parts[0]);
                    defaultLng = parseFloat(parts[1]);
                }
            }

            let map = L.map('map', {
                center: [defaultLat, defaultLng],
                zoom: 5,
                zoomControl: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© Dinas Kesehatan Kota Semarang',
            }).addTo(map);
            
            L.control.zoom({
                position: 'bottomleft'
            }).addTo(map);

            let marker = null;
            if (oldValue) {
                marker = L.marker([defaultLat, defaultLng]).addTo(map);
            }

            map.on('click', function (e) {
                let lat = e.latlng.lat.toFixed(6);
                let lng = e.latlng.lng.toFixed(6);

                $('#koordinat').val(`${lat},${lng}`);

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng]).addTo(map);
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
                    <h5 class="mb-0">{{ isset($kecamatan) ? 'Edit' : 'Tambah' }} Kecamatan</h5>
                </div>
                <div class="card-body">
                    <form action="@isset($kecamatan) {{ route('kecamatan.update', $kecamatan->uuid) }} @endisset @empty($kecamatan) {{ route('kecamatan.store') }} @endempty"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($kecamatan)
                            @method('PUT')
                        @endisset
                        <div class="row mb-3">
                            @include('partials.errors')
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama">Nama</label>
                            <div class="col-sm-10">
                                {{html()->text('nama', isset($kecamatan) ? $kecamatan->nama : @old('nama'))->class('form-control')->placeholder('Masukkan Nama')->required(true)}}
                                @error('nama')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                            
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="koordinat">Koordinat</label>
                            <div class="col-sm-10">
                                <input type="hidden" id="koordinat" name="koordinat"
                                    value="{{ isset($kecamatan) ? $kecamatan->koordinat : old('koordinat') }}"
                                    class="form-control mb-2" placeholder="" required readonly>

                                <div id="map" style="height: 400px; border-radius: 10px; border: 1px solid #ddd;"></div>

                                @error('koordinat')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end text-end">
                            <div class="col-sm-10">
                                <a href="{{ route('kecamatan.index') }}" class="btn btn-warning">Kembali</a>
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
