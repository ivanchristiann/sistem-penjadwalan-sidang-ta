@extends('layout.layoutpaj')

@section('content')
    <h2 class="text-primary"><strong>EDIT PERIODE</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('periode.update', $periode->id) }}">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-3">
                <label for="semester" class="form-label">Semester</label>
                <select class="form-select" id="semester" name="semester" required>
                    <option value="">Pilih Semester</option>
                    @if ($periode->semester == 'ganjil')
                        <option value="ganjil" selected>GANJIL</option>
                    @else
                        <option value="ganjil">GANJIL</option>
                    @endif

                    @if ($periode->semester == 'genap')
                        <option value="genap" selected>GENAP</option>
                    @else
                        <option value="genap">GENAP</option>
                    @endif
                </select>
            </div>
            <div class="col-3">
                <label for="numberPeriodeSidang" class="form-label">Periode Sidang</label>
                <input type="number" class="form-control" id="numberPeriodeSidang" name="periodeSidang"
                    value={{ $periode->periode_sidang }} required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-3">
                <label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai"
                    value={{ $periode->tanggal_mulai }} required>
            </div>
            <div class="col-3">
                <label for="tanggalBerakhir" class="form-label">Tanggal Berakhir</label>
                <input type="date" class="form-control" id="tanggalBerakhir" name="tanggalBerakhir"
                    value={{ $periode->tanggal_berakhir }} required>
            </div>
        </div>
        @if (count($cekValidasi) > 0 && $periode->konfirmasi == 'belum')
            <div class="row mb-3">
                <div class="col-3">
                    <label for="durasi" class="form-label">Durasi</label>
                    <select class="form-select" id="durasi" name="durasi" required>
                        <option value="">Pilih Durasi Sidang</option>
                        @if ($periode->durasi == '01:00:00')
                            <option value="01:00:00" selected>1 Jam</option>
                        @else
                            <option value="01:00:00">1 Jam</option>
                        @endif

                        @if ($periode->durasi == '01:30:00')
                            <option value="01:30:00" selected>1 Jam 30 Menit</option>
                        @else
                            <option value="01:30:00">1 Jam 30 Menit</option>
                        @endif

                        @if ($periode->durasi == '02:00:00')
                            <option value="02:00:00" selected>2 Jam</option>
                        @else
                            <option value="02:00:00">2 Jam</option>
                        @endif
                    </select>
                </div>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-6">
                <label for="txtLinkGoogleDrive" class="form-label">Link Google Drive</label>
                <input type="text" class="form-control" id="txtLinkGoogleDrive" name="linkGoogleDrive"
                    value={{ $periode->link_google_drive }} required>
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
                    @foreach ($tanggalMerah as $tgl)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($tgl->tanggal)->format('d-m-Y') }}</td>
                            <td><button type="button" class="btn btn-danger btnHapusDb"
                                    onclick="hapusTanggalMerah({{ $tgl->id }})">Hapus</button>
                            </td>
                            <input type="hidden" name="tglMerah[]" value="{{ $tgl->tanggal }}">
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mb-3">
            <div class="col-6 text-end">
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

        function hapusTanggalMerah(id) {
            $.ajax({
                type: 'POST',
                url: '{{ route('tanggalmerah.hapus') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'id': id
                },
                success: function(data) {
                    if (data['status'] == 'success') {
                        window.location.reload(true);
                        alert('Berhasil menghapus tanggal merah');
                    } else if (data['status'] == 'danger') {
                        alert('Tanggal merah gagal dihapus!');
                    }
                }
            });
        }
    </script>
@endsection
