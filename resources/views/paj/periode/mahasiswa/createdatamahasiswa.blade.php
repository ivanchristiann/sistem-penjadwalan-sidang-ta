@extends('layout.layoutpajperiode')

@section('content')
    <h2 class="text-primary"><strong>TAMBAH MAHASISWA</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('paj.periode.mahasiswa.store') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtNama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="txtNama" name="nama" placeholder="Contoh: Andi Yohanes"
                    required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtNPK" class="form-label">NRP(Nomor Registrasi Pokok)</label>
                <input type="text" class="form-control" id="txtNPK" name="nrp" placeholder="Contoh: 160420588"
                    required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <label for="txtEmail" class="form-label">Judul Tugas Akhir</label>
                <input type="text" class="form-control" id="txtJudulTugasAkhir" name="judul"
                    placeholder="Contoh: Pembuatan Sistem Informasi Supermarket" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="txtEmail" class="form-label">Konsentrasi</label>
            @foreach ($konsentrasis as $konsentrasi)
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="konsentrasi" id="{{ $konsentrasi->nama }}"
                            value="{{ $konsentrasi->id }}">
                        <label class="form-check-label"> {{ $konsentrasi->nama }}</label>
                    </div>

                </div>
            @endforeach
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class="mb-3">
                    <label for="pembimbing1" class="form-label">Dosen Pembimbing 1</label>
                    <select class="form-select" id="pembimbing1" name="pembimbing1" required>
                        <option value="">Pilih Dosen Pembimbing 1</option>
                        @foreach ($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="pembimbing2" class="form-label">Dosen Pembimbing 2</label>
                    <select class="form-select" id="pembimbing2" name="pembimbing2" required>
                        <option value="">Pilih Dosen Pembimbing 2</option>
                        @foreach ($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="mengulang" value="false">
                    <label class="form-label text-register-red"><strong>Mahasiswa mengulang sidang dengan judul yang
                            sama seperti sidang sebelumnya</strong></label>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="form-check">
                <div class="col-12 text-end">
                    <a href="{{ url('paj/periode/mahasiswa') }}" type="button" class="btn btn-danger">Batal</a>
                    &nbsp;&nbsp;
                    <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        var pembimbing2 = @json($dosens);

        $('body').on('change', '#pembimbing1', function() {
            var idPembimbing1 = $("#pembimbing1").val();
            var option = "<option value=''>Pilih Dosen Pembimbing 2</option>";
            pembimbing2.forEach(element => {
                if (idPembimbing1 != element['id']) {
                    option += "<option value='" + element['id'] + "'>" + element['nama'] + "</option>";
                }
            });
            $("#pembimbing2").html(option);
        });
    </script>
@endsection
