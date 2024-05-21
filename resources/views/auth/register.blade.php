@if (Auth::user() != null)
    <script>
        window.location = "https://if.ubaya.ac.id/sita";
    </script>
@endif

<?php
session()->forget('kodeOTP');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register | Sidang TA - Teknik Informatika UBAYA</title>
    <meta name="author"
        content="SE Workshop 2023 (Lisana) - Gede Darma, Dastyn Susanto, Kenny Reandy Kwando, Ivan Christian, Ruth Flodian Rahakbauw [IF 2020]">

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/vendor/css/core.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/img/ubaya.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />


    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('custom-styles.css') }}" />

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background: url("{{ asset('assets/img/GambarFakultasTeknikUniversitasSurabaya.jpg') }}") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            font-family: 'PT Sans';
            height: 100vh;
            width: 100vw;
        }

        .font-opensans-bold {
            font-family: 'Open Sans';
            font-weight: 700;
        }

        #login-box {
            width: 30%;

            background-color: #FFFFFF;
            opacity: 0.95;
        }

        #login-form {
            padding: 20px;
        }

        @media screen and (min-width:768px) and (max-width:992px) {
            #login-box {
                width: 60%;
            }
        }

        @media screen and (min-width:992px) and (max-width:1200px) {
            #login-box {
                width: 30%;
            }
        }

        @media screen and (max-width:768px) {
            #login-box {
                width: 80%;
            }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div id="login-box" class="col-12 mx-auto rounded">
            <form method="POST" id="login-form" class="form" action="{{ route('mahasiswa.register.otp') }}">
                @csrf
                <div class="row">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
                            @foreach ($errors->all() as $error)
                                <p class="mt-0 mb-1">- {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div id="welcome" class="text-start">
                        <h2 class="font-opensans-bold text-login-blue fw-bold">Registrasi</h2>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group text-login-blue">
                        <label for="nrp" class="text-login-blue"><strong>NRP</strong></label><br>
                        <input type="text" name="nrp" id="nrp" class="form-control text-login-blue"
                            placeholder="Masukkan NRP" required>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="form-group">
                        <label for="password" class="text-login-blue"><strong>Password</strong></label><br>
                        <input type="password" name="password" id="password" class="form-control text-login-blue"
                            placeholder="Masukkan Password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="form-group">
                        <label for="confPassword" class="text-login-blue"><strong>Konfirmasi
                                Password</strong></label><br>
                        <input type="password" id="password" class="form-control text-login-blue"
                            placeholder="Masukkan Password" name="password_confirmation" required
                            autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mengulang" value="false">
                            <label class="form-check-label text-register-red"><strong>Saya mengulang sidang dengan judul
                                    yang sama seperti sidang sebelumnya</strong></label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <input type="submit" name="submit" class="btn btn-primary w-100" value="REGISTRASI">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{ url('login') }}" class="btn btn-registermahasiswa w-100">LOGIN</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
</body>

</html>
