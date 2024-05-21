<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Sidang Tugas Akhir</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <style>
        * {
            box-sizing: border-box;
        }

        #card {
            padding-top: 15px;
            padding-bottom: 15px;
            border-radius: 25px;
            box-shadow: 0px 4px 3px 0px rgba(161, 161, 161, 1);
        }

        label,
        h3 {
            color: #000066;
            font-weight: bold;
        }

        .label {
            font-size: 20px;
        }

        .label-judul {
            font-weight: 500;
            color: black
        }

        .card-body {
            padding-right: 20px;
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <div class="row align-items-center" style="height: 100vh;">
        <div class="col-12">
            <div class="container">
                <div class="card" id="card">
                    <h3 class="text-center">Registrasi Data Maju Sidang</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
                            @foreach ($errors->all() as $error)
                                <p class="mt-0 mb-1">- {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="card-body">
                        <form method="POST" action="{{ route('sidang.updateDataRegistrasiMajuSidang') }}">
                            @csrf
                            @method('POST')
                            <div class="mb-3">
                                <label for="judul" class="form-label label-judul">Judul Tugas Akhir</label><br>
                                <label for="judul" class="form-label label">{{ $sidang->judul }}</label>
                                {{-- <input type="text" class="form-control" id="judul" name="judul"
                                    value="{{ $sidang->judul }}" required> --}}
                            </div>
                            <div class="mb-3">
                                <label for="judul" class="form-label label-judul">Konsentrasi</label><br>
                                <label for="judul" class="form-label label">{{ $sidang->konsentrasi->nama }}</label>
                                {{-- <label for="konsentrasi" class="form-label">Konsentrasi</label><br>
                                @foreach ($konsentrasis as $konsentrasi)
                                    <div class="form-check form-check-inline">
                                        @if ($konsentrasi->id == $sidang->konsentrasi_id)
                                            <input class="form-check-input" type="radio" name="konsentrasi"
                                                id="{{ $konsentrasi->nama }}" value="{{ $konsentrasi->id }}"
                                                checked>{{ $konsentrasi->nama }}
                                        @else
                                            <input class="form-check-input" type="radio" name="konsentrasi"
                                                id="{{ $konsentrasi->nama }}"
                                                value="{{ $konsentrasi->id }}">{{ $konsentrasi->nama }}
                                        @endif
                                    </div>
                                @endforeach --}}
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="judul" class="form-label label-judul">Dosen Pembimbing 1</label><br>
                                        <label for="judul"
                                            class="form-label label">{{ $sidang->pembimbingsatu->nama }}</label>
                                        {{-- <label for="pembimbing1" class="form-label">Dosen Pembimbing 1</label>
                                        <select class="form-select" id="pembimbing1" name="pembimbing1" required>
                                            @foreach ($dosens as $dosen)
                                                @if ($dosen->id == $sidang->pembimbing_1)
                                                    <option value="{{ $dosen->id }}" selected>{{ $dosen->nama }}
                                                    </option>
                                                @else
                                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                                @endif
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="judul" class="form-label label-judul">Dosen Pembimbing 2</label><br>
                                        <label for="judul"
                                            class="form-label label">{{ $sidang->pembimbingdua->nama }}</label>
                                        {{-- <label for="pembimbing2" class="form-label">Dosen Pembimbing 2</label>
                                        <select class="form-select" id="pembimbing2" name="pembimbing2" required>
                                            @foreach ($dosens as $dosen)
                                                @if ($dosen->id == $sidang->pembimbing_2)
                                                    <option value="{{ $dosen->id }}" selected>{{ $dosen->nama }}
                                                    </option>
                                                @else
                                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                                @endif
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" value="{{ $sidang->id }}" name="sidangId">
                            <div class="row">
                                <div class="col text-end">
                                    &nbsp;&nbsp;
                                    <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit"
                                        value="Simpan">
                                </div>
                            </div>
                        </form>
                        <form style="float: left" action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</body>


</html>
