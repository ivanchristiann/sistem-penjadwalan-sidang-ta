@extends('layout.layoutdosen')

@section('content')
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
    <br><br>
    <table id="tabelJadwalSidang" class="table text-start wrap">
        <thead class="table-header">
            <tr>
                <th class="text-center">No</th>
                <th>Nama Dosen</th>
                <th class="text-center">Penguji 1</th>
                <th class="text-center">Penguji 2</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dosens as $key => $d)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $d->nama }}</td>
                    <td class="text-center">{{ $d->jmlh_ketua }}</td>
                    <td class="text-center">{{ $d->jmlh_sekretaris }}</td>
                    <td class="text-primary text-center"><strong>{{ $d->total_penguji }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
