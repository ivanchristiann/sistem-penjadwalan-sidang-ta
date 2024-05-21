@extends('layout.layoutpajperiode')

@section('content')
    <div class="card card-sidang-berlangsung">
        <div class="d-flex align-items-end row">
            <div class="col-12">
                <div class="card-body">
                    <h2 class="card-title text-primary"><strong>Detail Periode Sidang </strong></h2>
                    <p class="mb-0"><strong>PERIODE {{ $bulan }}</strong></p>
                    <p><span style="color: red"><strong>({{ $tanggalMulai }} - {{ $tanggalBerakhir }})</strong></span></p>
                    <p class="mb-0"><strong>Link Google Drive</strong><br>
                        <strong><a href="{{ $periodeAktif->link_google_drive }}"
                                target="blank"><u>{{ $periodeAktif->link_google_drive }}</u></a></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <br>
    @if (isset($sidangs))
        <div>
            <h3 class="text-primary float-start"><strong>Daftar Jadwal Sidang Final</strong></h3>
            {{-- <a href="{{ route('periode.kirimjadwal') }}" class="btn btn-primary float-end"><i class='bx bx-mail-send'></i>
                Kirim Jadwal</a> --}}
            <form action="{{ route('periode.downloadjadwalmahasiswa') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary float-end me-2"><i class='bx bx-download'></i>&nbsp;&nbsp;PDF
                    Mahasiswa</button>
            </form>
            <form action="{{ route('periode.downloadjadwaldosen') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary float-end me-2"><i class='bx bx-download'></i>&nbsp;&nbsp;PDF
                    Dosen</button>
            </form>
        </div>
        <div class="clearfix"></div>
        <div class="mb-4">
            <tbody>
                <table id="tabelJadwalSidang" class="table text-start wrap w-100">
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
            </tbody>
            </table>
        </div>
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h1 class="fw-bold text-center text-danger">Jadwal Belum Tersedia!</h1>
                        <h2 class="fw-bold text-center text-danger">Masih Sedang Proses Penyusunan</h2>
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
                dom: 'lBfrtip',
                buttons: [
                    { extend: 'excel', text:'Download Excel', className: 'btn ms-2' },
                ]
            });
        });
    </script>
@endsection
