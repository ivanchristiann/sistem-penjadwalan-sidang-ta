@extends('layout.layoutmhs')

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
        <h4 class="mb-0 text-danger "><strong>Mohon pastikan data maju sidang Anda sudah sesuai! Apabila tidak sesuai
                silakan menghubungi PAJ.</strong></h4>
        <br>
        <div class="">
            <p class="mb-1"><strong>Judul Tugas Akhir</strong></p>
            <p class="display-7 text-primary"><strong>{{ $sidang->judul }} </strong></p>
            <p class="mb-1"><strong>Dosen Pembimbing 1</strong></p>
            <p class="display-7 text-primary"><strong>{{ $sidang->pembimbingsatu->nama }}</strong></p>
            <p class="mb-1"><strong>Dosen Pembimbing 2</strong></p>
            <p class="display-7 text-primary"><strong>{{ $sidang->pembimbingdua->nama }}</strong></p>
            <p class="mb-1"><strong>Konsentrasi</strong></p>
            <p class="display-7 text-primary"><strong>{{ $sidang->konsentrasi->nama }}</strong></p>
            <p class="mb-1"><strong>Jadwal Sidang</strong></p>
            @if ($sidang->periode->konfirmasi == 'final')
                @if (isset($sidang->tanggal))
                    <p class="display-7 text-primary"><strong>{{ $jadwalSidang }} | {{ $slotJam }}</strong></p>
                @else
                    <p class="display-7 text-danger"><strong>Belum Tersedia</strong></p>
                @endif
            @else
                <p class="display-7 text-danger"><strong>Belum Tersedia</strong></p>
            @endif
            <p class="mb-1"><strong>Ruang Sidang</strong></p>
            @if ($sidang->periode->konfirmasi == 'final')
                @if (isset($sidang->ruangan_id))
                    <p class="display-7 text-primary"><strong>{{ $sidang->ruangan->nama }}</strong></p>
                @else
                    <p class="display-7 text-danger"><strong>Belum Tersedia</strong></p>
                @endif
            @else
                <p class="display-7 text-danger"><strong>Belum Tersedia</strong></p>
            @endif
            <br>
            @if ($sidang->validasi == 'mahasiswa')
                <div class="text-end">
                    <a href="{{ route('mahasiswa.edit', $sidang->mahasiswa_id) }}" class="btn btn-primary">EDIT</a>
                </div>
            @endif
        </div>
    @endif
@endsection

@section('script')
@endsection
