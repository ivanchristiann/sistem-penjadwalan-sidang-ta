<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Sidang TA - Teknik Informatika UBAYA</title>

    <meta name="description" content="" />
    <meta name="author"
        content="SE Workshop 2023 (Lisana) - Gede Darma, Dastyn Susanto, Kenny Reandy Kwando, Ivan Christian, Ruth Flodian Rahakbauw [IF 2020]">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/ubaya.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('custom-styles.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/DataTables/datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/DataTables/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand mt-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <p class="display-7 text-primary mb-1"><strong>{{ Auth::user()->dosen->nama }}</strong>
                                </p>
                            </div>
                            <div class="row">
                                <p class="fs-6">{{ Auth::user()->dosen->npk }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item {{ request()->is('dosen') ? ' active' : '' }}">
                        <a href="{{ url('/dosen') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-alt"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>

                    {{-- Jadwal Sidang Keseluruhan --}}
                    <li class="menu-item {{ request()->is('dosen/jadwalsidang') ? ' active' : '' }}">
                        <a href="{{ url('/dosen/jadwalsidang') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-time-five"></i>
                            <div>Jadwal Sidang</div>
                        </a>
                    </li>

                    {{-- Jadwal Kosong --}}
                    <li class="menu-item {{ request()->is('dosen/jadwalkosong*') ? ' active' : '' }}">
                        @if ($periodeAktif != null)
                            @if ($periodeAktif->konfirmasi != 'final')
                                <a href="{{ route('jadwalkosong.edit', ['dosen' => Auth::user()->dosen->id]) }}"
                                    class="menu-link">
                                    <i class="menu-icon tf-icons bx bx-user"></i>
                                    <div>Jadwal Kosong</div>
                                </a>
                            @else
                                <a href="{{ url('/dosen/jadwalkosong') }}" class="menu-link">
                                    <i class="menu-icon tf-icons bx bx-user"></i>
                                    <div>Jadwal Kosong</div>
                                </a>
                            @endif
                        @else
                            <a href="{{ url('/dosen/jadwalkosong') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-user"></i>
                                <div>Jadwal Kosong</div>
                            </a>
                        @endif
                    </li>

                    {{-- Penjadwalan --}}
                    @if (str_contains(Auth::user()->dosen->posisi, 'Scheduler'))
                        <li class="menu-item {{ request()->is('dosen/scheduler/penjadwalan') ? ' active' : '' }}">
                            <a href="{{ route('sidang.penjadwalankalab') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-calendar-alt"></i>
                                <div>Penjadwalan</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('dosen/scheduler/rekapPenguji') ? ' active' : '' }}">
                            <a href="{{ route('sidang.rekappenguji') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bxs-book-alt"></i>
                                <div>Rekap Penguji</div>
                            </a>
                        </li>
                    @endif

                    {{-- Ganti Passsword --}}
                    {{-- <li class="menu-item {{ request()->is('dosen/resetpass') ? ' active' : '' }}">
                        <a href="{{ route('dosen.resetpass') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-shield"></i>
                            <div>Ganti Password</div>
                        </a>
                    </li> --}}

                    {{-- Logout --}}
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">Logout</span></li>
                    <li class="menu-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn menu-link btn-logout">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div>Logout</div>
                            </button>
                        </form>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-primary"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>
                    <div class="d-flex justify-content-start align-items-center">
                        <p class="fs-4 mt-0 mb-0 fw-bold text-white">Sistem Sidang TA - IF UBAYA</p>
                    </div>
                </nav>
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                Teknik Informatika - Universitas Surabaya
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- DataTables -->
    <script src="{{ asset('assets/DataTables/dataTables1.13.6.min.js') }}"></script>
    <script src="{{ asset('assets/DataTables/responsive.min.js') }}"></script>
    <!-- Script  -->
    @yield('script')
</body>

</html>
