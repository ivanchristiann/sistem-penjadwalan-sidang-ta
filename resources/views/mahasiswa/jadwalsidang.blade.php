@extends('layout.layoutmhs')

@section('content')
    @if ($periodeAktif != null)
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-12">
                    <div class="card-body">
                        <h2 class="card-title text-primary"><strong>Periode Sidang yang Sedang
                                Berlangsung</strong></h2>
                        <p class="mb-0">PERIODE {{ $bulan }}</p>
                        <p><span style="color: red">
                                ({{ $tanggalMulai }} - {{ $tanggalBerakhir }})
                            </span></p>
                        <p class="mb-0"><strong>Link Google Drive</strong><br>
                            <a href="{{ $periodeAktif->link_google_drive }}"
                                target="blank"><u>{{ $periodeAktif->link_google_drive }}</u></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <div class="mb-4">
            @if ($jadwal == 'noJadwal')
                <div class="card card-sidang-berlangsung">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body text-center">
                                <h2 class="card-title text-register-red"><strong>Jadwal Sidang Final Belum Tersedia</strong>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <h3 class="text-primary float-start"><strong>Daftar Mahasiswa</strong></h3>
                <table id="tabelJadwalSidang" class="table display text-start wrap">
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sidangs as $key => $sidang)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>{{ $sidang->mahasiswa->nrp }}</td>
                                <td>{{ $sidang->mahasiswa->nama }}</td>
                                @if ($sidang->tanggal != null)
                                    <td>{{ \Carbon\Carbon::parse($sidang->tanggal)->isoFormat('dddd') }}</td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($sidang->tanggal != null)
                                    <td>{{ \Carbon\Carbon::parse($sidang->tanggal)->isoFormat('D MMMM Y') }}</td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($sidang->nomor_slot != null)
                                    @foreach ($arrSlot as $slot)
                                        @if ($sidang->nomor_slot == $slot['id'])
                                            <td>{{ $slot['slot'] }}</td>
                                        @endif
                                    @endforeach
                                @else
                                    <td>-</td>
                                @endif
                                @if ($sidang->ruangan != null)
                                    <td>{{ $sidang->ruangan->nama }}</td>
                                @else
                                    <td>-</td>
                                @endif
                                <td>{{ $sidang->judul }}</td>
                                <td>{{ $sidang->pembimbingsatu->nama }}</td>
                                <td>{{ $sidang->pembimbingdua->nama }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
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