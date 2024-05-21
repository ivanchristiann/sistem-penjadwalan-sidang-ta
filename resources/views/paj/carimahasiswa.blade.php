@extends('layout.layoutpaj')

@section('content')
    <h2 class="text-primary"><strong>CARI MAHASISWA</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-6">
            <form method="post" action="{{ route('paj.carimahasiswa.post') }}">
                @csrf
                <h4>NRP Mahasiswa</h4>
                <div class="d-inline-flex w-100">
                    <input type="text" class="form-control text-primary" id="txtNrp" name="nrp"
                        placeholder="Contoh: 160420005" required>&nbsp;
                    <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Cari">
                </div>
            </form>
        </div>
    </div>
    <br>
    <div class="mb-4">
        <table id="MahasiswaTable" class="table nowrap text-start">
            <thead class="table-header text-center">
                <tr>
                    <th>NRP</th>
                    <th>Nama Mahasiswa</th>
                    <th>Konsentrasi</th>
                    <th>Periode Sidang</th>
                    <th>Hari Sidang</th>
                    <th>Tanggal Sidang</th>
                    <th>Slot Jam</th>
                    <th>Ruang Sidang</th>
                    <th>Judul</th>
                    <th>Pembimbing 1</th>
                    <th>Pembimbing 2</th>
                    <th>Penguji 1</th>
                    <th>Penguji 2</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($mahasiswaAktif) || isset($mahasiswaNonaktif))
                    @if (count($mahasiswaAktif) != 0)
                        @foreach ($mahasiswaAktif as $mhs)
                            <tr>
                                <td>{{ $mhs->nrp }}</td>
                                <td>{{ $mhs->nama }}</td>
                                <td>{{ $mhs->sidang->konsentrasi->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($mhs->sidang->periode->tanggal_mulai)->isoFormat('MMMM Y') }}
                                </td>
                                @if ($mhs->sidang->tanggal)
                                    <td>{{ \Carbon\Carbon::parse($mhs->sidang->tanggal)->isoFormat('dddd') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($mhs->sidang->tanggal)->isoFormat('D MMMM Y') }}</td>
                                @else
                                    <td>-</td>
                                    <td>-</td>
                                @endif

                                @if ($mhs->sidang->nomor_slot != null)
                                    @foreach ($arrslot as $s)
                                        @if ($s['id'] == $mhs->sidang->nomor_slot)
                                            <td>{{ $s['slot'] }}</td>
                                        @endif
                                    @endforeach
                                @else
                                    <td>-</td>
                                @endif

                                @if ($mhs->sidang->ruangan != null)
                                    <td>{{ $mhs->sidang->ruangan->nama }}</td>
                                @else
                                    <td>-</td>
                                @endif

                                <td>{{ $mhs->sidang->judul }}</td>
                                <td>{{ $mhs->sidang->pembimbingsatu->nama }}</td>
                                <td>{{ $mhs->sidang->pembimbingdua->nama }}</td>

                                @if ($mhs->sidang->pengujisatu != null)
                                    <td>{{ $mhs->sidang->pengujisatu->nama }}</td>
                                @else
                                    <td>-</td>
                                @endif

                                @if ($mhs->sidang->pengujidua != null)
                                    <td>{{ $mhs->sidang->pengujidua->nama }}</td>
                                @else
                                    <td>-</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif

                    @foreach ($mahasiswaNonaktif as $key => $history)
                        <tr>
                            <td>{{ $history->nrp }}</td>
                            <td>{{ $history->nama }}</td>
                            <td>{{ $history->historySidang->konsentrasi->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($history->historySidang->periode->tanggal_mulai)->isoFormat('MMMM Y') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($history->historySidang->tanggal)->isoFormat('dddd') }}</td>
                            <td>{{ \Carbon\Carbon::parse($history->historySidang->tanggal)->isoFormat('D MMMM Y') }}
                            </td>
                            @php
                                $slot = App\Http\Controllers\JadwalkosongController::getSlotJadwal($history->historySidang->periode->durasi);
                            @endphp
                            @foreach ($slot as $s)
                                @if ($s['id'] == $history->historySidang->nomor_slot)
                                    <td>{{ $s['slot'] }}</td>
                                @endif
                            @endforeach
                            <td>{{ $history->historySidang->ruangan->nama }}</td>
                            <td>{{ $history->historySidang->judul }}</td>
                            <td>{{ $history->historySidang->pembimbingsatu->nama }}</td>
                            <td>{{ $history->historySidang->pembimbingdua->nama }}</td>
                            <td>{{ $history->historySidang->pengujisatu->nama }}</td>
                            <td>{{ $history->historySidang->pengujidua->nama }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $('body').on('click', '#btnSubmit', function() {
            var nrp = $('#txtNrp').val();

            $('#mahasiswaTable').DataTable();
        });
        $('#MahasiswaTable').DataTable({
            scrollX: true,
        });
    </script>
@endsection
