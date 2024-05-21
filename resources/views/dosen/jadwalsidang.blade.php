@extends('layout.layoutdosen')

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
        @if ($jadwal == 'noJadwal')
            <div class="card card-sidang-berlangsung mt-5">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-12">
                        <div class="card-body text-center">
                            <h2 class="card-title text-register-red"><strong>Belum Ada Jadwal Final Yang Tersedia<strong>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <br>
            <h3 class="card-title text-primary"><strong>Jadwal Sidang Keseluruhan Dosen<strong></h3>
            <table id="tabelJadwalSidang" class="table display text-start">
                <thead class="table-header">
                    <tr>
                        <th>No</th>
                        <th>NRP</th>
                        <th>Nama Mahasiswa</th>
                        <th>Hari Sidang</th>
                        <th>Tanggal Sidang</th>
                        <th>Slot Jam</th>
                        <th>Ruang Sidang</th>
                        <th>Judul</th>
                        <th>Pembimbing 1</th>
                        <th>Pembimbing 2</th>
                        <th>Penguji 1</th>
                        <th>Penguji 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 0; ?>
                    @foreach ($sidangs as $key => $sidang)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $sidang->mahasiswa->nrp }}</td>
                            <td>{{ $sidang->mahasiswa->nama }}</td>
                            @if ($sidang->tanggal != null && $sidang->nomor_slot != null && $sidang->ruangan_id != null)
                                <td>{{ \Carbon\Carbon::parse($sidang->tanggal)->isoFormat('dddd') }}</td>
                                <td>{{ \Carbon\Carbon::parse($sidang->tanggal)->isoFormat('D MMMM Y') }}</td>
                                @foreach ($arrslot as $s)
                                    @if ($s['id'] == $sidang->nomor_slot)
                                        <td>{{ $s['slot'] }}</td>
                                    @endif
                                @endforeach
                                <td>{{ $sidang->ruangan->nama }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td>{{ $sidang->judul }}</td>
                            <td>{{ $sidang->pembimbingsatu->nama }}</td>
                            <td>{{ $sidang->pembimbingdua->nama }}</td>
                            @if ($sidang->penguji_1 != null && $sidang->penguji_2 != null && $sidang->ruangan_id != null)
                                <td>{{ $sidang->pengujisatu->nama }}</td>
                                <td>{{ $sidang->pengujidua->nama }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Belum Ada Periode Sidang yang Sedang
                                Berlangsung<strong></h2>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#tabelJadwalSidang').DataTable({
                responsive: true,
            });
        });
    </script>
@endsection
