@extends('layout.layoutpajperiode')

@section('content')
    <style>
        th {
            text-align: center;
        }

        #container-search {
            display: inline-flex;
            width: 100%;
        }
    </style>
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
        <h3 class="text-primary float-start"><strong>Daftar Mahasiswa</strong></h3>
        @if($periodeAktif->konfirmasi == 'belum')
        <div class="float-end">
            <a href="{{ route('paj.periode.mahasiswa.create') }}" class="btn btn-primary ">Tambah</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCSV">Upload
                CSV</button>
        </div>
        @endif
        <br><br>
        <div class="mb-4">
            <table id="MahasiswaTable" class="table wrap text-start" style="width: 100%;">
                <thead class="table-header">
                    <tr>
                        <th>No</th>
                        <th>NRP</th>
                        <th>Nama Mahasiswa</th>
                        <th>Konsentrasi</th>
                        <th>Judul</th>
                        <th>Pembimbing 1</th>
                        <th>Pembimbing 2</th>
                        <th>Validasi Data</th>
                        @if ($countBelumValidasi > 0)
                            <th>Edit</th>
                        @endif
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 0; ?>
                    @foreach ($mahasiswaSidang as $ms)
                        <tr>
                            <?php $counter++; ?>
                            <td class="text-center">{{ $counter }}</td>
                            <td>{{ $ms->mahasiswa->nrp }}</td>
                            <td>{{ $ms->mahasiswa->nama }}</td>
                            <td>{{ $ms->konsentrasi->nama }}</td>
                            <td>{{ $ms->judul }}</td>
                            <td>{{ $ms->pembimbingsatu->nama }}</td>
                            <td>{{ $ms->pembimbingdua->nama }}</td>
                            @if ($ms->validasi == 'belum' || $ms->validasi == 'mahasiswa')
                                <td class="text-center">
                                    <form action="{{ route('paj.periode.mahasiswa.validasiData') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $ms->id }}">
                                        <button type="submit" class="btn btn-sm btn-primary text-white"><i
                                                class='bx bx-check-square'></i></button>
                                    </form>
                                </td>
                            @else
                                <td class="text-center"><i class='bx bxs-check-circle'></i> Tervalidasi
                                </td>
                            @endif
                            @if ($countBelumValidasi > 0)
                                @if ($ms->validasi == 'belum')
                                    <td>
                                        <a href="{{ route('paj.periode.mahasiswa.editdatasidang', $ms->id) }}"
                                            class="btn btn-sm btn-edit-datamajusidang text-white text-center"><i
                                                class='bx bx-edit-alt'></i></a>
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            @endif
                            <td>
                                <form action="{{ route('paj.periode.mahasiswa.hapusdatamajusidang', $ms->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger text-white text-center"
                                        onclick="return confirm('Apa Anda yakin untuk menghapus data sidang {{ $ms->mahasiswa->nama }} ?');"><i
                                            class='bx bx-trash'></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modal Add CSV --}}
        <div class="modal fade" id="modalCSV" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            sytle="display: block;" aria-labelledby="modalCSVLabel" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary"><strong>Upload Daftar Mahasiswa maju Sidang</strong></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body mb-3">
                        <p>Upload File CSV:</p>
                        <form enctype="multipart/form-data" action="{{ route('paj.periode.registercsv') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <input type="file" name="csvfile" accept=".csv" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card card-sidang-berlangsung">
            <div class="d-flex align-items-end row">
                <div class="col-sm-12">
                    <div class="card-body text-center">
                        <h2 class="card-title text-register-red"><strong>Belum Ada Periode Sidang yang Sedang
                                Berlangsung<strong></h2>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        $('#MahasiswaTable').DataTable({});
    </script>
@endsection
