@extends('layout.layoutpaj')

@section('content')
    <h2 class="text-primary"><strong>BUKA PERIODE BARU</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('periode.store') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-3">
                <label for="semester" class="form-label">Semester</label>
                <select class="form-select" id="semester" name="semester" required>
                    <option value="">Pilih Semester</option>
                    <option value="ganjil">GANJIL</option>
                    <option value="genap">GENAP</option>
                </select>
            </div>
            <div class="col-3">
                <label for="numberPeriodeSidang" class="form-label">Periode Sidang</label>
                <input type="number" class="form-control" id="numberPeriodeSidang" name="periodeSidang" max="20" min="1" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-3">
                <label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai" required>
            </div>
            <div class="col-3">
                <label for="tanggalBerakhir" class="form-label">Tanggal Berakhir</label>
                <input type="date" class="form-control" id="tanggalBerakhir" name="tanggalBerakhir" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-3">
                <label for="durasi" class="form-label">Durasi</label>
                <select class="form-select" id="durasi" name="durasi" required>
                    <option value="">Pilih Durasi Sidang</option>
                    <option value="01:00:00">1 Jam</option>
                    <option value="01:30:00">1 Jam 30 Menit</option>
                    <option value="02:00:00">2 Jam</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label for="txtLinkGoogleDrive" class="form-label">Link Google Drive</label>
                <input type="text" class="form-control" id="txtLinkGoogleDrive" name="linkGoogleDrive" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-3">
                <label for="tanggalMerah" class="form-label">Tanggal Merah</label>
                <input type="date" class="form-control" id="tanggalMerah" name="tanggalMerah">
            </div>
            <div class="col-2">
                <label for="btnTambah" class="form-label">Aksi</label><br>
                <button type="button" class="btn btn-primary" class="form-control" id="btnTambah">Tambah</button>
            </div>
        </div>

        <div>
            <table id="tabelTanggalMerah" class="table w-auto text-start table-bordered">
                <thead class="table-header">
                    <th>Tanggal Merah</th>
                    <th>Aksi</th>
                </thead>
                <tbody id="tabelTanggalMerahKonten">
                </tbody>
            </table>
        </div>

        <div class="row mb-3">
            <div class="col-6 text-end">
                <a href="{{ url('paj') }}" type="button" class="btn btn-danger">Batal</a>
                &nbsp;&nbsp;
                <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
         $('#btnTambah').on('click', function() {
            var tanggalMerah = $('#tanggalMerah').val();
            if (tanggalMerah != "") {
                var myDate = new Date(tanggalMerah);
                var tanggal = myDate.getDate() + '-' + (myDate.getMonth() + 1) + '-' + myDate.getFullYear();
                var baris = "<tr><td>" + tanggal +
                    "</td><td><button type='button' class='btn btn-danger btnHapus'>Hapus</button></td><input type='hidden' name='tglMerah[]' value='" +
                    tanggalMerah + "'></tr>";
                $('#tabelTanggalMerahKonten').append(baris);
            } else {
                alert('Tanggal Belum diinputkan!')
            }
        });
        $('body').on('click', '.btnHapus', function() {
            $(this).parent().parent().remove();
            alert("Berhasil menghapus tanggal merah");
        });
    </script>
@endsection
