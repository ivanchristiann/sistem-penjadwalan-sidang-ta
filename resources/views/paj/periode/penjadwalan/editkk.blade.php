@extends('layout.layoutpajperiode')

@section('content')
    <h2 class="text-primary"><strong>EDIT KASUS KHUSUS</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('status') }}
        </div>
    @endif
    <form method="POST" action="{{ route('sidang.updatekk', $sidang->id) }}">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-12">
                <p class="mb-0 fs-5">Nama Lengkap</p>
                <p class="mb-0 text-primary fs-4 fw-bold">{{ $sidang->mahasiswa->nama }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <p class="mb-0 fs-5">NRP(Nomor Registrasi Pokok)</p>
                <p class="mb-0 text-primary fs-4 fw-bold">{{ $sidang->mahasiswa->nrp }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <p class="mb-0 fs-5">Judul Tugas Akhir</p>
                <p class="mb-0 text-primary fs-4 fw-bold">{{ strtoupper($sidang->judul) }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <p class="mb-0 fs-5">Dosen Pembimbing 1</p>
                <p class="mb-0 text-primary fs-4 fw-bold">{{ $sidang->pembimbingsatu->nama }}</p>
            </div>
            <div class="col-6">
                <p class="mb-0 fs-5">Dosen Pembimbing 2</p>
                <p class="mb-0 text-primary fs-4 fw-bold">{{ $sidang->pembimbingdua->nama }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <p class="mb-0 fs-5">Ketua Penguji</p>
                <select name="ketua" id="" class="form-select" required>
                    @foreach ($ketuaPenguji as $dKP)
                        @if ($dKP->id == $sidang->pengujisatu->id)
                            <option value="{{ $dKP->id }}" selected>{{ $dKP->nama }}</option>
                        @else
                            @if ($dKP->id != $sidang->pembimbingsatu->id && $dKP->id != $sidang->pembimbingdua->id)
                                <option value="{{ $dKP->id }}">{{ $dKP->nama }}</option>
                            @endif
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="mb-0 fs-5">Sekretaris Penguji</p>
                <select name="sekretaris" id="" class="form-select" required>
                    @foreach ($sekretarisPenguji as $dSP)
                        @if ($dSP->id == $sidang->pengujidua->id)
                            <option value="{{ $dSP->id }}" selected>{{ $dSP->nama }}</option>
                        @else
                            @if ($dSP->id != $sidang->pembimbingsatu->id && $dSP->id != $sidang->pembimbingdua->id)
                                <option value="{{ $dSP->id }}">{{ $dSP->nama }}</option>
                            @endif
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-4">
                <p class="mb-0 fs-5">Tanggal</p>
                <input type="date" name="date" class="form-control" id="txtDate" value="{{ $sidang->tanggal }}"
                    required>
            </div>
            <div class="col-4">
                <p class="mb-0 fs-5">Jam Sidang</p>
                <select name="slot" id="cbJam" class="form-select" required>
                    @foreach ($slots as $slot)
                        @if ($sidang->nomor_slot == $slot['id'])
                            <option value="{{ $slot['id'] }}" selected>{{ $slot['slot'] }}</option>
                        @else
                            <option value="{{ $slot['id'] }}">{{ $slot['slot'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <p class="mb-0 fs-5">Ruang Sidang</p>
                <select name="ruangan" id="cbRuangan" class="form-select" required>
                    @foreach ($ruangans as $ruang)
                        @if ($ruang->id == $sidang->ruangan->id)
                            <option value="{{ $ruang->id }}" selected>{{ $ruang->nama }}</option>
                        @else
                            <option value="{{ $ruang->id }}">{{ $ruang->nama }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ url('paj/periode/penjadwalan') }}" type="button" class="btn btn-danger">Batal</a>
                &nbsp;&nbsp;
                <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
            </div>
        </div>
    </form>
@endsection
