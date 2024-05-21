@extends('layout.layoutpajperiode')
@section('content')
    @if ($periodeAktif != null)
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-12">
                    <div class="card-body">
                        <h2 class="card-title text-primary"><strong>Periode Sidang yang Sedang
                                Berlangsung</strong></h2>
                        <p class="mb-0"><strong>PERIODE {{ $bulan }}</strong></p>
                        <p><span style="color: red"><strong>
                                    ({{ $tanggalMulai }} - {{ $tanggalBerakhir }})
                                </strong></span></p>
                        <p class="mb-0"><strong>Link Google Drive</strong><br>
                            <strong><a href="{{ $periodeAktif->link_google_drive }}"
                                    target="blank"><u>{{ $periodeAktif->link_google_drive }}</u></a></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <h3 class="card-title text-primary"><strong>Jadwal Kosong Dosen</strong></h3>
        <form action="{{ route('jadwalkosong.paj') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="clearfix mb-3">
                        <select class="form-select float-start" name="pilihDosen" id="pilihDosen">
                            @foreach ($dosenAktif as $dosen)
                                @if ($dosen->id == $dosen1->id)
                                    <option value="{{ $dosen->id }}" selected>{{ $dosen->nama }}</option>
                                @else
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="clearfix mb-3">
                        <select class="form-select float-start" name="pilihDosen2" id="pilihDosen2">
                            @foreach ($dosenAktif as $dosen)
                                @if ($dosen->id == $dosen2->id)
                                    <option value="{{ $dosen->id }}" selected>{{ $dosen->nama }}</option>
                                @else
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>
        <br>
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Tidak Ada Periode Berlangsung<strong>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <form action="{{ route('jadwalkosong.paj.edit') }}" method="POST">
        @csrf
        <div>
            <h5 class="fw-bold" id="npkDosen">NPK : {{ $dosen1->npk }}</h5>
            <h5 class="fw-bold" id="namaDosen">Nama : {{ $dosen1->nama }}</h5>
        </div>

        <div class="overflow-auto">
            <table class="table table-bordered table-jadwal-kosong" style="white-space: nowrap;">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        @php
                            foreach ($tanggalSidang as $tanggal) {
                                $bg = in_array(date('j', strtotime($tanggal)), $tanggalMerah) || in_array(date('w', strtotime($tanggal)), [0, 6]) ? ' bg-danger text-white' : '';
                                echo '<th scope="col" class="text-center' . $bg . '">' . \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') . '</th>';
                            }
                        @endphp
                    </tr>
                </thead>

                <tbody>
                    @foreach ($slot as $data)
                        @if ($data['slot'] == '11:00-12:00' || $data['slot'] == '11:00-12:30' || $data['slot'] == '10:00-12:00')
                            <tr>
                                <td><strong>{{ $data['slot'] }}</strong></td>
                                @php
                                    foreach ($tanggalSidang as $tanggal) {
                                        if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || in_array(date('w', strtotime($tanggal)), [0, 6])) {
                                            echo '<th scope="col" class="text-center bg-danger"></th>';
                                        } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'false') {
                                            echo '<th scope="col"></th>';
                                        } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
                                            echo '<th scope="col" class="bg-dark text-center text-white">Terjadwal</th>';
                                        } else {
                                            echo '<th scope="col" class="pilih-sidang"></th>';
                                        }
                                    }
                                @endphp
                            </tr>
                            <tr>
                                @if ($data['slot'] == '11:00-12:00' || $data['slot'] == '10:00-12:00')
                                    <td><strong>12:00-13:00</strong></td>
                                @else
                                    <td><strong>12:30-13:00</strong></td>
                                @endif
                                @php
                                    foreach ($tanggalSidang as $tanggal) {
                                        echo '<th scope="col" class="bg-pale-red text-center">Istirahat</th>';
                                    }
                                @endphp
                            </tr>
                        @else
                            <tr>
                                <td><strong>{{ $data['slot'] }}</strong></td>
                                @php
                                    foreach ($tanggalSidang as $tanggal) {
                                        if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || in_array(date('w', strtotime($tanggal)), [0, 6])) {
                                            echo '<th scope="col" class="text-center bg-danger"></th>';
                                        } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'false') {
                                            echo '<th scope="col"></th>';
                                        } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
                                            echo '<th scope="col" class="bg-dark text-center text-white">Terjadwal</th>';
                                        } else {
                                            echo '<th scope="col" class="pilih-sidang"></th>';
                                        }
                                    }
                                @endphp
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <input type="hidden" name="dosen1" value="{{ $dosen1->id }}">
        <input type="hidden" name="dosen2" value="{{ $dosen2->id }}">
        <br>
        <button type="submit" class="btn btn-primary float-end mt-10">Edit</button>
    </form>

    <br>
    <br>

    <div>
        <h5 class="fw-bold" id="npkDosen2">NPK : {{ $dosen2->npk }}</h5>
        <h5 class="fw-bold" id="namaDosen2">Nama : {{ $dosen2->nama }}</h5>
    </div>

    <div class="overflow-auto">
        <table class="table table-bordered table-jadwal-kosong" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th scope="col"></th>
                    @php
                        foreach ($tanggalSidang as $tanggal) {
                            $bg = in_array(date('j', strtotime($tanggal)), $tanggalMerah) || in_array(date('w', strtotime($tanggal)), [0, 6]) ? ' bg-danger text-white' : '';
                            echo '<th scope="col" class="text-center' . $bg . '">' . \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') . '</th>';
                        }
                    @endphp
                </tr>
            </thead>

            <tbody>
                @foreach ($slot as $data)
                    @if ($data['slot'] == '11:00-12:00' || $data['slot'] == '11:00-12:30' || $data['slot'] == '10:00-12:00')
                        <tr>
                            <td><strong>{{ $data['slot'] }}</strong></td>
                            @php
                                foreach ($tanggalSidang as $tanggal) {
                                    if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || in_array(date('w', strtotime($tanggal)), [0, 6])) {
                                        echo '<th scope="col" class="text-center bg-danger"></th>';
                                    } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen2->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'false') {
                                        echo '<th scope="col"></th>';
                                    } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen2->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
                                        echo '<th scope="col" class="bg-dark text-center text-white">Terjadwal</th>';
                                    } else {
                                        echo '<th scope="col" class="pilih-sidang"></th>';
                                    }
                                }
                            @endphp
                        </tr>
                        <tr>
                            @if ($data['slot'] == '11:00-12:00' || $data['slot'] == '10:00-12:00')
                                <td><strong>12:00-13:00</strong></td>
                            @else
                                <td><strong>12:30-13:00</strong></td>
                            @endif
                            @php
                                foreach ($tanggalSidang as $tanggal) {
                                    echo '<th scope="col" class="bg-pale-red text-center">Istirahat</th>';
                                }
                            @endphp
                        </tr>
                    @else
                        <tr>
                            <td><strong>{{ $data['slot'] }}</strong></td>
                            @php
                                foreach ($tanggalSidang as $tanggal) {
                                    if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || in_array(date('w', strtotime($tanggal)), [0, 6])) {
                                        echo '<th scope="col" class="text-center bg-danger"></th>';
                                    } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen2->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'false') {
                                        echo '<th scope="col"></th>';
                                    } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen2->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
                                        echo '<th scope="col" class="bg-dark text-center text-white">Terjadwal</th>';
                                    } else {
                                        echo '<th scope="col" class="pilih-sidang"></th>';
                                    }
                                }
                            @endphp
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
