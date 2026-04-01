@php
    $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    @if (!isset($navbarFull))
        <div class="app-brand demo">
            <a href="{{ url('/') }}" class="app-brand-link">
                <span class="app-brand-logo demo">
                    @include('_partials.macros', ['height' => '100%'])
                </span>
                <span class="app-brand-text demo menu-text fw-bold">LINCAH</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
            </a>
        </div>
    @endif


    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Dashboard</span>
        </li>
        <li class="menu-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <a href="{{ route('dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-dashboard"></i>
                <div>Dashboard</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data</span>
        </li>
        {{-- CRUD-GENERATOR-SIDEBAR --}}
        @can('forum-index')
            <li class="menu-item {{ request()->routeIs('forum.*') ? 'active' : '' }}">
                <a href="{{ route('forum.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-messages"></i>
                    <div>Forum Diskusi</div>
                </a>
            </li>
        @endcan
                
        @can('linsek-index')
            <li class="menu-item {{ request()->routeIs('linsek.*') ? 'active' : '' }}">
                <a href="{{ route('linsek.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-pencil"></i>
                    <div>Linsek Tematik</div>
                </a>
            </li>
        @endcan

        @can('berita-index')
        <li class="menu-item {{ request()->routeIs('foto.*') ? 'active' : '' }}" style="">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-brand-telegram"></i>
                <div>Publikasi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('berita.index') }}" class="menu-link">
                        <div>Berita</div>
                    </a>
                </li>
            </ul>
        @endcan

        @can('foto-index')
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('foto.index') }}" class="menu-link">
                        <div>Foto</div>
                    </a>
                </li>
            </ul>
        @endcan
        @can('video-index')
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('video.index') }}" class="menu-link">
                        <div>Video</div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan
        
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Potensial Dampak</span>
        </li>

        @can('data-index')

        @php
            $jenisKasus = \App\Models\JenisKasus::get();
            $currentUuid = request()->route('jenis_kasus_uuid');
        @endphp

        <li class="menu-item {{ request()->is('data/*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon ti ti-file-analytics"></i>
                <div>Data Monitoring</div>
            </a>

            <ul class="menu-sub">

                @foreach ($jenisKasus as $jk)
                    @php
                        // apakah ini jenis kasus yg sedang dibuka?
                        $isActiveJenis = $currentUuid === $jk->uuid;
                    @endphp

                    <li class="menu-item {{ $isActiveJenis ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div>{{ $jk->nama }}</div>
                        </a>

                        <ul class="menu-sub">

                            {{-- Monitoring --}}
                            <li class="menu-item 
                                {{ $isActiveJenis && request()->routeIs('monitoring.*') ? 'active' : '' }}">
                                <a href="{{ route('monitoring.index', $jk->uuid) }}" class="menu-link">
                                    <div>Monitoring Kelurahan</div>
                                </a>
                            </li>

                            {{-- Monitoring RT --}}
                            <li class="menu-item 
                                {{ $isActiveJenis && request()->routeIs('monitoring-rt.*') ? 'active' : '' }}">
                                <a href="{{ route('monitoring-rt.index', $jk->uuid) }}" class="menu-link">
                                    <div>Monitoring RT</div>
                                </a>
                            </li>

                        </ul>
                    </li>

                @endforeach

            </ul>
        </li>

        @endcan

        @can('jenis_kasus-index')

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data Master</span>
        </li>

            <li class="menu-item {{ request()->routeIs('jenis_kasus.*') ? 'active' : '' }}">
                <a href="{{ route('jenis_kasus.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-brand-google-fit"></i>
                    <div>Jenis Kasus</div>
                </a>
            </li>
        @endcan

        @can('topik-index')
            <li class="menu-item {{ request()->routeIs('topik.*') ? 'active' : '' }}">
                <a href="{{ route('topik.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-forms"></i>
                    <div>Kategori Forum</div>
                </a>
            </li>
        @endcan
        
        @can('kategori_berita-index')
            <li class="menu-item {{ request()->routeIs('kategori_berita.*') ? 'active' : '' }}">
                <a href="{{ route('kategori_berita.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-brand-feedly"></i>
                    <div>Kategori Berita</div>
                </a>
            </li>
        @endcan

        @can('kecamatan-index')

        <li class="menu-item {{ request()->routeIs('kecamatan.*') ? 'active' : '' }}" style="">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-map"></i>
                <div>Wilayah</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('kecamatan.index') }}" class="menu-link">
                        <div>Kecamatan</div>
                    </a>
                </li>
            </ul>
        @endcan

        @can('kelurahan-index')
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('kelurahan.index') }}" class="menu-link">
                        <div>Kelurahan</div>
                    </a>
                </li>
            </ul>
        @endcan

        @can('rw-index')
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('rw.index') }}" class="menu-link">
                        <div>RW</div>
                    </a>
                </li>
            </ul>
        @endcan

        @can('rt-index')
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="{{ route('rt.index') }}" class="menu-link">
                        <div>RT</div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('user-index')

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

            <li class="menu-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-user"></i>
                    <div>Pengguna</div>
                </a>
            </li>
        @endcan

        @can('role-index')
            <li class="menu-item {{ request()->routeIs('role.*') ? 'active' : '' }}">
                <a href="{{ route('role.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-shield"></i>
                    <div>Role</div>
                </a>
            </li>
        @endcan

        @can('permission-index')
            <li class="menu-item {{ request()->routeIs('permission.*') ? 'active' : '' }}">
                <a href="{{ route('permission.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-fingerprint"></i>
                    <div>Permission</div>
                </a>
            </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Lainnya</span>
        </li>

        <li class="menu-item">
            <a href="{{ route('logout') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-logout"></i>
                <div>Logout</div>
            </a>
        </li>

    </ul>

</aside>
