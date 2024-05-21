@extends('layout.layoutpajperiode')

@section('content')
    <br>
    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="card card-sidang-berlangsung">
        <div class="d-flex align-items-end row">
            <div class="col-12">
                <div class="card-body">
                    <h2 class="card-title text-primary"><strong>Detail Periode Sidang <strong></h2>
                    <p class="mb-0">PERIODE {{ $bulan }}</p>
                    <p><span style="color: red">({{ $tanggalMulai }} - {{ $tanggalBerakhir }})</span></p>
                    <p class="mb-0"><strong>Link Google Drive</strong><br>
                        <a href="{{ $periodeAktif->link_google_drive }}"
                            target="_blank"><u>{{ $periodeAktif->link_google_drive }}</u></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <h3 class="text-primary"><strong>BAGIKAN JADWAL SIDANG<strong></h3>
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary">Untuk Dosen</h4>
            <form action="{{ route('periode.kirimjadwaldosen') }}" method="post">
                @csrf
                <div class="row mb-2">
                    <div class="col-8">
                        <textarea name="message" id="" rows="8" class="form-control"
                            placeholder="Tuliskan body email untuk dosen di sini"></textarea>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-8 text-end">
                        <button type="submit" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold text-primary">Untuk Mahasiswa</h4>
            <form action="{{ route('periode.kirimjadwalmahasiswa') }}" method="post">
                @csrf
                <div class="row mb-2">
                    <div class="col-8">
                        <textarea name="message" id="" rows="8" class="form-control"
                            placeholder="Tuliskan body email untuk mahasiswa di sini"></textarea>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-8 text-end">
                        <button type="submit" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border spinner-border-lg text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h4 class="mt-4 text-primary text-center">Sedang Memproses Pengiriman Email</h4>
                </div>
            </div>
        </div>
    </div>
@endsection
