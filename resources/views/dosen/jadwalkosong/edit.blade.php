@extends('layout.layoutdosen')

@if (session('status'))
    <div class="alert alert-success">@dd(status)</div>
@endif

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
                    </div>
                </div>
            </div>
        </div>
        <br>
        @if ($periodeAktif->konfirmasi != 'final')
            @if (session("savestatus"))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session("savestatus") }}
                </div>
            @endif
            <form action="{{ route('jadwalkosong.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <h3 class="card-title text-primary"><strong>Jadwal Kosong Dosen</strong></h3>
                    </div>
                    <div class="col-6 text-end">
                        <input type="hidden" name="dosen_id" value={{ $dosen->id }}>
                        <button type="submit" class="btn btn-primary float-end mt-10">Simpan</button>
                    </div>
                </div>
                <div class="overflow-auto">
                    <table class="table table-bordered table-jadwal-kosong" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                @php
                                    foreach ($tanggalSidang as $tanggal) {
                                        if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || date('w', strtotime($tanggal)) == 0 || date('w', strtotime($tanggal)) == 6) {
                                            echo '<th scope="col" class="bg-danger text-white">' . \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') . '</th>';
                                        } else {
                                            echo '<th scope="col">' . \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') . '</th>';
                                        }
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
                                                if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || date('w', strtotime($tanggal)) == 0 || date('w', strtotime($tanggal)) == 6) {
                                                    echo '<th scope="col" class="bg-danger"></th>';
                                                } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'tersedia') {
                                                    echo '<th scope="col" class="text-center"><input type="checkbox" class="big-checkbox" checked value="' . $data['id'] . '-' . strtotime($tanggal) . '" name="jamKosong[]"></th>';
                                                } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
                                                    echo '<th scope="col" class="bg-dark text-center text-white">Terjadwal</th>';
                                                } else {
                                                    echo '<th scope="col" class="text-center"><input type="checkbox" class="big-checkbox" value="' . $data['id'] . '-' . strtotime($tanggal) . '" name="jamKosong[]"></th>';
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
                                                if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || date('w', strtotime($tanggal)) == 0 || date('w', strtotime($tanggal)) == 6) {
                                                    echo '<th scope="col" class="bg-danger"></th>';
                                                } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'tersedia') {
                                                    echo '<th scope="col" class="text-center"><input type="checkbox" class="big-checkbox" checked value="' . $data['id'] . '-' . strtotime($tanggal) . '" name="jamKosong[]"></th>';
                                                } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
                                                    echo '<th scope="col" class="bg-dark text-center text-white">Terjadwal</th>';
                                                } else {
                                                    echo '<th scope="col" class="text-center"><input type="checkbox" class="big-checkbox" value="' . $data['id'] . '-' . strtotime($tanggal) . '" name="jamKosong[]"></th>';
                                                }
                                            }
                                        @endphp
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            <div class="card card-sidang-berlangsung">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-12">
                        <div class="card-body text-center">
                            <h2 class="card-title text-register-red"><strong>Penjadwalan Sidang Telah Selesai</strong></h2>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Belum Ada Periode Sidang yang Sedang
                                Berlangsung</strong></h2>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('javascript')
@endsection
