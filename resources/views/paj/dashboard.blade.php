@extends('layout.layoutpaj')

@section('content')
    <style>
        #container-search {
            display: inline-flex;
            width: 100%;
        }
    </style>

    @if ($periodeAktif != null)
    <div class="card card-sidang-berlangsung mb-2">
        <div class="d-flex align-items-end row">
            <div class="col-12">
                <div class="card-body">
                    <h2 class="card-title text-primary"><strong>Periode Sidang yang Sedang Berlangsung<strong></h2>
                    <p class="mb-0">PERIODE {{ $bulan }}</p>
                    <p><span style="color: red">({{ $tanggalMulai }} - {{ $tanggalBerakhir }})</span></p>
                    <p class="mb-0"><strong>Link Google Drive</strong><br>
                        <a href="{{ $periodeAktif->link_google_drive }}" target="blank"><u>{{ $periodeAktif->link_google_drive }}</u></a>
                    </p>
                    <div class="text-end">
                        <a href="{{ url('paj/periode') }}" class="btn btn-primary">Detail</a>
                        <a href="{{ route('periode.edit', $periodeAktif->id) }}" class="btn btn-edit-periode ">Edit</a>
                        <form action="{{ route('periode.nonaktifkan') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="submit" class="btn btn-logout" value="Nonaktifkan"
                                onclick="return confirm('Apa Anda yakin untuk menonaktifkan periode saat ini?');">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="card card-sidang-berlangsung mb-2">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Belum Ada Periode Sidang yang Sedang
                                Berlangsung<strong></h2>
                        <br>
                        <a href="{{ route('periode.create') }}" class="btn btn-primary">Buka Periode</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success alert-dismissible text-uppercase" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible text-uppercase" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('error') }}
        </div>
    @endif
    <h3 class="text-primary float-start mt-2"><strong>Daftar Periode<strong></h3>

    <div class="mb-4">
        <table id="PeriodeTable" class="table text-start nowrap" style="width: 100%;">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>Semester</th>
                    <th>Periode Sidang</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Jumlah Mahasiswa</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($periodeNonaktif as $pn)
                    <tr>
                        <td class="text-center">{{$loop->iteration}}</td>
                        <td class="text-uppercase">{{ $pn->semester }}</td>
                        <td>{{ $pn->periode_sidang }}</td>
                        <td>{{ \Carbon\Carbon::parse($pn->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($pn->tanggal_berakhir)->format('d-m-Y') }}</td>
                        <td>{{ $pn->jumlahMahasiswa }} Mahasiswa</td>
                        <td>
                            <a href="{{ route('periode.history', $pn->id) }}" class="btn btn-sm btn-primary"><i class='bx bx-detail'></i></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#PeriodeTable').DataTable({
                scrollX: true,
            });
        });
    </script>
@endsection
