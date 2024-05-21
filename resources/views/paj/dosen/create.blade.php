@extends('layout.layoutpaj')

@section('content')
    <h2 class="text-primary"><strong>TAMBAH DOSEN</strong></h2>
    @if (session('status'))
        <div class="alert alert-danger alert-dismissible" role="alert">
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
    <form method="POST" action="{{ route('dosen.store') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtNama" class="form-label">Nama Lengkap (Beserta Gelar)</label>
                <input type="text" class="form-control" id="txtNama" name="nama"
                    placeholder="Contoh: Yohanes Kurniawan, S.Kom., M.Sc." required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtNPK" class="form-label">NPK</label>
                <input type="text" class="form-control" id="txtNPK" name="npk" placeholder="Contoh: 221155"
                    required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtEmail" class="form-label">Email UBAYA</label>
                <input type="email" class="form-control" id="txtEmail" name="email"
                    placeholder="Contoh: yohanesk@staff.ubaya.ac.id" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="txtEmail" class="form-label">Konsentrasi</label>
            @foreach ($konsentrasi as $k)
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="konsentrasi[]" value="{{ $k->id }}">
                        <label class="form-check-label"> {{ $k->nama }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row mb-3">
            <label for="txtEmail" class="form-label">Posisi Penguji</label>
            <div class="col-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="posisi[]" value="Pembimbing">
                    <label class="form-check-label"> Pembimbing</label>
                </div>
            </div>
            <div class="col-2">
                <div class="form-check">
                    <input class="form-check-input" id="chcPKetua" type="checkbox" name="posisi[]" value="Ketua">
                    <label class="form-check-label"> Ketua</label>
                </div>
            </div>
            <div class="col-2">
                <div class="form-check">
                    <input class="form-check-input" id="chcPSekretaris" type="checkbox" name="posisi[]" value="Sekretaris">
                    <label class="form-check-label"> Sekretaris</label>
                </div>
            </div>
            <div class="col-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="posisi[]" value="Scheduler">
                    <label class="form-check-label"> Scheduler</label>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ url('paj/dosen') }}" type="button" class="btn btn-danger">Batal</a>
                &nbsp;&nbsp;
                <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $('#chcPKetua').on('click', function() {
            if ($(this).prop("checked") == true) {
                $('#chcPSekretaris').prop('checked', 'true');
            }
        });
    </script>
@endsection
