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
                        <a href="{{ $periode->link_google_drive }}" target="blank"><u>{{ $periode->link_google_drive }}</u></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <br>

    @if (count($historySidangs) != 0)
        <h3 class="text-primary float-start"><strong>DAFTAR HISTORI MAHASISWA<strong></h3>
        <br>
        <div class="mb-4">
            <table id="tabelJadwalSidang" class="table text-start nowrap w-100">
                <thead class="table-header">
                    <tr>
                        <th class="w-5">No</th>
                        <th class="w-10">NRP</th>
                        <th class="w-50">Nama Mahasiswa</th>
                        <th class="w-25">Konsentrasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historySidangs as $key => $sidang)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $sidang->historyMahasiswa->nrp }}</td>
                            <td>{{ $sidang->historyMahasiswa->nama }}</td>
                            <td>{{ $sidang->konsentrasi->nama }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </table>
        </div>
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Tidak Ada detail Mahasiswa dalam periode ini<strong>
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
