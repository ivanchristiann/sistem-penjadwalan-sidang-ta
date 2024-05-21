@extends('layout.layoutpajhistoryperiode')

@section('content')
    <style>
        #container-search {
            display: inline-flex;
            width: 100%;
        }
    </style>
    <div class="card card-sidang-berlangsung">
        <div class="d-flex align-items-end row">
            <div class="col-12">
                <div class="card-body">
                    <h2 class="card-title text-primary"><strong>Detail Periode Sidang <strong></h2>
                    <p class="mb-0">PERIODE {{ $bulan }}</p>
                    <p><span style="color: red">({{ $tanggalMulai }} - {{ $tanggalBerakhir }})</span></p>
                    <p class="mb-0"><strong>Link Google Drive</strong><br>
                        <a href="{{ $periode->link_google_drive }}"
                            target="blank"><u>{{ $periode->link_google_drive }}</u></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <br>
    @if (count($historySidangs) != 0)
        <h3 class="text-primary float-start"><strong>DAFTAR JADWAL HISTORI SIDANG<strong></h3>
        <br>
        <div class="mb-4">
            <table id="tabelJadwalSidang" class="table text-start nowrap">
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
                    @foreach ($historySidangs as $key => $sidang)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $sidang->historyMahasiswa->nrp }}</td>
                            <td>{{ $sidang->historyMahasiswa->nama }}</td>
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
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            @endif
                            <td>{{ $sidang->judul }}</td>
                            <td>{{ $sidang->pembimbingsatu->nama }}</td>
                            <td>{{ $sidang->pembimbingdua->nama }}</td>
                            @if ($sidang->penguji_1 != null && $sidang->penguji_2 != null && $sidang->ruangan_id != null)
                                <td>{{ $sidang->pengujisatu->nama }}</td>
                                <td>{{ $sidang->pengujidua->nama }}</td>
                            @else
                                <td>-</td>
                                <td>-</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Tidak Ada detail Sidang dalam periode ini<strong>
                        </h2>
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
                scrollX: true,
            });
        });
    </script>
@endsection
