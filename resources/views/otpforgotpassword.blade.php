<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>OTP | Sidang TA - Teknik Informatika UBAYA</title>

    <meta name="author" content="SE Workshop 2023 (Lisana) - Gede Darma, Dastyn Susanto, Kenny Reandy Kwando, Ivan Christian, Ruth Flodian Rahakbauw [IF 2020]">

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
            width: 40%;

            background-color: #FFFFFF;
            opacity: 0.95;
        }

        #login-form {
            padding: 20px;
        }

        .input-otp {
            height: 3.5rem;
            width: 4rem;
            font-size: 1.5rem;
            text-align: center;
            font-weight: 700;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        @media screen and (min-width:768px) and (max-width:992px){
            #login-box{
                width: 70%;
            }
        }

        @media screen and (min-width:992px) and (max-width:1200px){
            #login-box{
                width: 50%;
            }
        }

        @media screen and (max-width:768px){
            #login-box{
                width: 90%;
            }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div id="login-box" class="col-12 mx-auto rounded">
            @if ($message == 'Kode OTP yang anda masukkan salah, coba lagi!')
                <div class="row mb-0">
                    <div class="col-12">
                        <div class="alert alert-danger mt-2 mx-3 mb-0" role="alert">
                            {{ $message }}
                        </div>
                    </div>
                </div>
            @else
                <div class="row mb-0">
                    <div class="col-12">
                        <div class="alert alert-success mt-2 mx-3 mb-0" role="alert">
                            {{ $message }}
                        </div>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="row mb-0">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible mt-2 mx-3 mb-0" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                            @foreach ($errors->all() as $error)
                                <p class="mt-0 mb-1">- {{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <form id="login-form" class="form" action="{{ route('forgotpassword.validasiotpforgotpassword') }}"
                method="POST">
                @csrf
                @method('POST')
                <div class="row mb-4">
                    <div id="welcome" class="text-start">
                        <h2 class="font-opensans-bold text-login-blue fw-bold">Kode OTP</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="text-start">
                        <h5 class="font-opensans-bold text-login-blue fw-bold">Masukkan Kode OTP</h5>
                    </div>
                </div>
                <div class="row mb-5 justify-content-center">
                    <div class="col-3">
                        <div class="form-group text-login-blue">
                            <input type="number" name="otp1" id="otp1"
                                class="form-control mx-auto text-login-blue input-otp" required>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group text-login-blue">
                            <input type="number" name="otp2" id="otp2"
                                class="form-control mx-auto text-login-blue input-otp" required>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group text-login-blue">
                            <input type="number" name="otp3" id="otp3"
                                class="form-control mx-auto text-login-blue input-otp" required>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group text-login-blue">
                            <input type="number" name="otp4" id="otp4"
                                class="form-control mx-auto text-login-blue input-otp"required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-end">
                        <input type="submit" name="submit" class="btn btn-primary" value="KONFIRMASI">
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
    <script src="{{ asset('assets/js/custom-script.js') }}"></script>
</body>

</html>
