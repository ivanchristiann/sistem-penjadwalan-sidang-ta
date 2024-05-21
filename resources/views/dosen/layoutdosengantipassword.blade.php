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

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            font-family: 'PT Sans';
            height: 100vh;
            width: 100vw;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="col-6 mx-auto rounded">
            <h2 class="text-primary mb-1"><strong>GANTI DEFAULT PASSWORD</strong></h2>
            <h4 class="mb-0" style="color: black;">Hai, <span
                    class="text-primary"><strong>{{ Auth::user()->dosen->nama }}</strong></span>!</h4>
            <p class="text-danger fw-bold">Mohon untuk mengganti password anda saat pertama kali login!</p>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
                    @foreach ($errors->all() as $error)
                        <p class="mt-0 mb-1">- {{ $error }}</p>
                    @endforeach
                </div>
            @endif
            @if (session('status'))
                <br>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    {{ session('status') }}
                </div>
                <br>
            @endif
            <form method="POST" action="{{ route('dosen.resetpassword') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-12 col-md-12">
                        <label for="txtPassword" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="txtPasswordBaru" name="passbaru" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-12">
                        <label for="txtKonfirmasiPassword" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="txtKonfirmasiPassword" name="konfirmasipassbaru"
                            required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-12 text-end">
                        <input type="submit" class="btn btn-primary w-100 btn-block mb-2" id="btnSubmit" name="submit" value="Simpan">
            </form>
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <input type="submit" name="submit" class="btn btn-registermahasiswa w-100" value="Batal">
            </form>
        </div>
    </div>
    </div>

    </div>





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
