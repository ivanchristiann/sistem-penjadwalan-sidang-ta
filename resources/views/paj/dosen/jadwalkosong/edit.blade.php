@extends('layout.layoutpajperiode')

@if (session('status'))
    {{-- <div class="alert alert-success">{{session('status')}}</div>   --}}
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
        <div>
            <h5 class="fw-bold" id="npkDosen">NPK : {{ $dosen1->npk }}</h5>
            <h5 class="fw-bold" id="namaDosen">Nama : {{ $dosen1->nama }}</h5>
        </div>
        <form action="{{ route('jadwalkosong.paj.store') }}" method="POST">
            @csrf
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
                                            if (in_array(date('j', strtotime($tanggal)), $tanggalMerah) || date('w', strtotime($tanggal)) == 0 || date('w', strtotime($tanggal)) == 6) {
                                                echo '<th scope="col" class="bg-danger"></th>';
                                            } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'tersedia') {
                                                echo '<th scope="col" class="text-center"><input type="checkbox" class="big-checkbox" checked value="' . $data['id'] . '-' . strtotime($tanggal) . '" name="jamKosong[]"></th>';
                                            } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
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
                                            } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'tersedia') {
                                                echo '<th scope="col" class="text-center"><input type="checkbox" class="big-checkbox" checked value="' . $data['id'] . '-' . strtotime($tanggal) . '" name="jamKosong[]"></th>';
                                            } elseif (App\Http\Controllers\JadwalkosongController::getJadwalByDosen($dosen1->id, $data['id'], date('Y-m-d', strtotime($tanggal))) == 'terpakai') {
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
            <input type="hidden" name="dosen1" value={{ $dosen1->id }}>
            <input type="hidden" name="dosen2" value={{ $dosen2->id }}>
            <br>
            <button type="submit" class="btn btn-primary float-end mt-10">Simpan</button>
        </form>
    @endif
@endsection

@section('script')
@endsection
