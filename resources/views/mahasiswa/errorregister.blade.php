@if (session('message') == null)
    <script>
        window.location = "https://if.ubaya.ac.id/sita/login";
    </script>
@endif

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | Sidang TA - Teknik Informatika UBAYA</title>
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

        .imageWarning {
            width: 100px;
            height: 100px;
        }

        @media screen and (min-width:768px) and (max-width:992px) {
            #login-box {
                width: 70%;
            }
        }

        @media screen and (min-width:992px) and (max-width:1200px) {
            #login-box {
                width: 50%;
            }
        }

        @media screen and (max-width:768px) {
            #login-box {
                width: 90%;
            }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div id="login-box" class="col-12 mx-auto rounded">
            <div class="text-center">
                <img class="imageWarning" src="{{ asset('assets/img/warning.png') }}" alt="masa"><br><br>
                @if (session('message') == 'mengulang')
                    <label class="text-login-blue"><strong>Anda tidak terdaftar pada sistem. Pastikan kembali Anda
                            mengulang sidang atau tidak!</strong></label>
                    <label class="text-login-blue"><strong>Apabila ada ketidaksesuaian mengulang sidang, harap
                            menghubungi PAJ.</strong></label>
                @elseif(session('message') == 'baru')
                    <label class="text-login-blue"><strong>Anda tidak terdaftar pada sistem. Pastikan kembali NRP Anda
                            sudah sesuai!</strong></label>
                    <label class="text-login-blue"><strong>Apabila ada ketidaksesuaian, harap
                            menghubungi PAJ.</strong></label>
                @elseif(session('message') == 'terdaftar')
                    <label class="text-login-blue"><strong>Data akun Anda telah terdaftar pada sistem!</strong></label>
                    <label class="text-login-blue"><strong>Apabila ada ketidaksesuaian mengulang sidang, harap
                            menghubungi PAJ.</strong></label>
                @elseif(session('message') == 'mengulangover')
                    <label class="text-login-blue"><strong>Anda tidak dapat mengulang dengan judul yang sama untuk
                            ketiga kalinya!</strong></label>
                    <label class="text-login-blue"><strong>Apabila ada ketidaksesuaian mengulang sidang, harap
                            menghubungi PAJ.</strong></label>
                @elseif(session('message') == 'tidakadaperiode')
                    <label class="text-login-blue"><strong>Tidak ada periode sidang yang sedang berlangsung!</strong></label>
                @endif
            </div><br>
            <div class="text-end">
                <a href="{{ url('/login') }}" class="btn btn-primary text-white">BACK</a>
            </div>
        </div>
    </div>
</body>

</html>
