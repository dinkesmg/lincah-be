@php
    $containerNav =
        isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
            ? 'container-xxl'
            : 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';
@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
        id="layout-navbar">
@endif
@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{ $containerNav }}">
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.year-select').on('click', function () {
            var year = $(this).data('year');

            $.ajax({
                url: "{{ route('setYear') }}",
                type: "POST",
                data: {
                    year: year,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    console.error("Gagal menyimpan tahun:", xhr);
                }
            });
        });
    });
</script>

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                @include('_partials.macros', ['height' => 20])
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="ti ti-x ti-sm align-middle"></i>
        </a>
    </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>
@endif

<div class="navbar-nav d-flex align-items-center w-100" id="navbar-collapse">
    <ul class="navbar-nav flex-row align-items-center me-auto">

        @php
            $currentYear = session('selected_year', date('Y'));
            $startYear = 2025;
            $years = [];

            $maxYear = max($currentYear, $startYear);

            for ($year = $maxYear; $year >= max($maxYear - 4, $startYear); $year--) {
                $years[] = $year;
            }
        @endphp

        <li class="nav-item dropdown-year dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown">
                Tahun {{ $currentYear }}
            </a>
            <ul class="dropdown-menu dropdown-menu-start">
                @foreach ($years as $year)
                    <li>
                        <a class="dropdown-item year-select" href="javascript:void(0);" data-year="{{ $year }}">
                            <span>{{ $year }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
    <ul class="navbar-nav flex-row align-items-center ms-auto">

        @if (isset($menuHorizontal))
            <!-- Search -->
            <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
                <a class="nav-link search-toggler" href="javascript:void(0);">
                    <i class="ti ti-search ti-md"></i>
                </a>
            </li>
            <!-- /Search -->
        @endif
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ Auth::user() ? asset('assets/img/avatars/1.png') : asset('assets/img/avatars/1.png') }}"
                        alt class="h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::user() ? asset('assets/img/avatars/1.png') : asset('assets/img/avatars/1.png') }}"
                                        alt class="h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-medium d-block">
                                    @if (Auth::check())
                                        {{ Auth::user()->name }}
                                    @else
                                        John Doe
                                    @endif
                                </span>
                                <small class="text-muted">{{ Auth::user()->getRole()->name }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                @if (Auth::check())
                    <li>
                        <a class="dropdown-item" href="{{ route('password.change.form') }}">
                            <i class='ti ti-key me-2'></i>
                            <span class="align-middle">Ganti Password</span>
                        </a>
                    </li>
                    @if (session()->has('admin_id'))
                        <li>
                            <a class="dropdown-item" href="{{ route('fastcrud_user.impersonate.login_back') }}">
                                <i class='ti ti-login me-2'></i>
                                <span class="align-middle">Kembali Ke Admin</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class='ti ti-logout me-2'></i>
                            <span class="align-middle">Logout</span>
                        </a>
                    </li>
                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                        @csrf
                    </form>
                @else
                    <li>
                        <a class="dropdown-item"
                            href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
                            <i class='ti ti-login me-2'></i>
                            <span class="align-middle">Login</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>

<!-- Search Small Screens -->
</nav>
<!-- / Navbar -->
