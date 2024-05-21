@extends('layout.layoutpajperiode')

@section('content')
    <h2 class="text-primary"><strong>EDIT DATA MAHASISWA</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('paj.periode.mahasiswa.updatedatasidang', $sidang->id) }}">
        @csrf
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtEmail" class="form-label">Judul Tugas Akhir</label>
                <input type="text" class="form-control" id="txtJudulTugasAkhir" name="judul"
                    placeholder="Contoh: Pembuatan Sistem Informasi Supermarket" value="{{ $sidang->judul }}" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="txtEmail" class="form-label">Konsentrasi</label>
            @foreach ($konsentrasis as $konsentrasi)
                <div class="col-2">
                    <div class="form-check">
                        @if ($sidang->konsentrasi->nama != $konsentrasi->nama)
                            <input class="form-check-input" type="radio" name="konsentrasi" id="{{ $konsentrasi->nama }}"
                                value="{{ $konsentrasi->id }}">
                        @else
                            <input class="form-check-input" type="radio" name="konsentrasi" id="{{ $konsentrasi->nama }}"
                                checked value="{{ $konsentrasi->id }}">
                        @endif

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
                            @if ($sidang->pembimbingsatu->id == $dosen->id)
                                <option value="{{ $dosen->id }}" selected>{{ $dosen->nama }}</option>
                            @else
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endif
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
                            @if ($sidang->pembimbingdua->id == $dosen->id)
                                <option value="{{ $dosen->id }}" selected>{{ $dosen->nama }}</option>
                            @elseif($sidang->pembimbingsatu->id != $dosen->id)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ url('paj/periode/mahasiswa') }}" type="button" class="btn btn-danger">Batal</a>
                &nbsp;&nbsp;
                <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
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
