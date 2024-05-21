@extends('layout.layoutdosen')

@section('content')
    @if (
        $periodeAktif->konfirmasi == 'paj' ||
            $periodeAktif->konfirmasi == 'scheduler' ||
            $periodeAktif->konfirmasi == 'final')
        <h2 class="text-primary"><strong>Penjadwalan Dosen Penguji</strong></h2>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible" role="alert">
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
        <div class="mb-4">
            <h3 class="text-danger fw-bold">Daftar Mahasiswa yang Tidak ditemukan Jadwal Dosen Penguji</h3>
            <table id="SidangNoSlotTable" class="table wrap">
                <thead class="bg-danger">
                    <tr>
                        <th class="text-white">No</th>
                        <th class="text-white">NRP</th>
                        <th class="text-white">Nama Mahasiswa</th>
                        <th class="text-white">Peminatan</th>
                        <th class="text-white">Pembimbing 1</th>
                        <th class="text-white">Pembimbing 2</th>
                        <th class="text-white">Jadwal</th>
                        <th class="text-white">Reset Jadwal</th>
                    </tr>
                </thead>
                <?php $counter = 0; ?>
                <tbody>
                    @foreach ($sidangTidakAdaPenguji as $sTAP)
                        <?php $counter++; ?>
                        <tr>
                            <td class="fs-5">{{ $counter }}</td>
                            <td class="fs-5">{{ $sTAP->mahasiswa->nrp }}</td>
                            <td class="fs-5">{{ $sTAP->mahasiswa->nama }}</td>
                            <td class="fs-5">{{ $sTAP->konsentrasi->nama }}</td>
                            <td class="fs-5">{{ $sTAP->pembimbingsatu->nama }}</td>
                            <td class="fs-5">{{ $sTAP->pembimbingdua->nama }}</td>
                            <td class="fs-5">{{ $sTAP->formatdate }} | Pk.{{ $sTAP->slot }}</td>
                            <td class="fs-5">
                                <form action="{{ route('sidang.resetPenjadwalan1Kalab') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="dosbing1Reset" value="{{ $sTAP->pembimbing_1 }}">
                                    <input type="hidden" name="dosbing2Reset" value="{{ $sTAP->pembimbing_2 }}">
                                    <input type="hidden" name="sidangIdReset" value="{{ $sTAP->id }}">
                                    <input type="submit" class="btn btn-logout" value="RESET"
                                        onclick="return confirm('Apa Anda yakin untuk mereset jadwal sidang {{ $sTAP->mahasiswa->nrp }} - {{ $sTAP->mahasiswa->nama }}?');">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row mb-3">
            <div class="col-8">
                <h3 class="fw-bold">Penjadwalan Dosen Penguji</h3>
            </div>
            <div class="col-4 text-end">
                @if ($periodeAktif->konfirmasi == 'paj')
                    <form action="{{ route('periode.kalabKonfirmasi') }}" method="post">
                        @csrf
                        <input type="hidden" name="idPeriode" value="{{ $periodeAktif->id }}">
                        <button type="submit" class="btn btn-primary">Konfirmasi Jadwal</button>
                    </form>
                @endif
            </div>

            @foreach ($sidangReset as $sR)
                <form action="{{ route('sidang.setPenjadwalan1Kalab') }}" method="POST">
                    @csrf
                    <div class="card px-4 py-3 mb-4 bg-pale-red">
                        <div class="row mt-0 mb-0">
                            <div class="col-10">
                                @if ($sR->mengulang == 'yes')
                                    <p class="mb-0 text-primary fs-4 fw-bold">{{ $sR->mahasiswa->nama }}
                                        ({{ $sR->mahasiswa->nrp }})
                                        [{{ $sR->konsentrasi->nama }}] <span class="badge bg-danger">Mengulang</span>
                                    </p>
                                @else
                                    <p class="mb-0 text-primary fs-4 fw-bold">{{ $sR->mahasiswa->nama }}
                                        ({{ $sR->mahasiswa->nrp }})
                                        [{{ $sR->konsentrasi->nama }}]
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-0 mb-0">
                            <div class="col-10">
                                <p class="fs-3 fw-bold">
                                    {{ Str::upper($sR->judul) }}
                                </p>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6">
                                <p class="mb-0 fs-6">Pembimbing 1</p>
                                <p class="fs-5 text-primary fw-bold">{{ $sR->pembimbingsatu->nama }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 fs-6">Pembimbing 2</p>
                                <p class="fs-5 text-primary fw-bold">{{ $sR->pembimbingdua->nama }}</p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <p class="mb-0 fs-5">Jadwal Sidang</p>
                                <div class="mb-0">
                                    <select id="jadwalSelect"
                                        class="form-select form-select-lg bg-select-penjadwalan jadwalSelect"
                                        sidangId="{{ $sR->id }}" name="slot" required>
                                        <option selected disabled>Pilih Jadwal</option>
                                        @foreach ($sR['slot'] as $slot)
                                            <option value="{{ $slot->tanggal }}#{{ $slot->nomor_slot }}">
                                                {{ $slot->formatdate . ' | Pk.' . $slot->formattime }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <p class="mb-0 fs-5">Ruang Sidang</p>
                                <div class="mb-0">
                                    <select id="ruangSelect_{{ $sR->id }}"
                                        class="form-select form-select-lg bg-select-penjadwalan" name="ruangan" required>
                                        <option selected disabled>Pilih Ruangan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <input type="hidden" name="dosbing1" value={{ $sR->pembimbingsatu->id }}>
                                <input type="hidden" name="dosbing2" value={{ $sR->pembimbingdua->id }}>
                                <input type="hidden" name="sidangId" value={{ $sR->id }}>
                                <button type="submit" class="btn btn-primary">SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endforeach

            @foreach ($sidangBelumTerjadwalPenguji as $sBTP)
                <form action="{{ route('sidang.setPenjadwalan2') }}" method="POST">
                    <div class="card px-4 py-3 mb-4">
                        @csrf
                        <div class="row mt-0 mb-0">
                            <div class="col-10">
                                @if($sBTP->mengulang=='yes')
                                <p class="mb-0 text-primary fs-4 fw-bold">{{ $sBTP->mahasiswa->nama }}
                                    ({{ $sBTP->mahasiswa->nrp }})
                                    [{{ $sBTP->konsentrasi->nama }}] <span class="badge bg-danger">Mengulang</span>
                                </p>
                                @else
                                <p class="mb-0 text-primary fs-4 fw-bold">{{ $sBTP->mahasiswa->nama }}
                                    ({{ $sBTP->mahasiswa->nrp }})
                                    [{{ $sBTP->konsentrasi->nama }}]
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-0 mb-0">
                            <div class="col-10">
                                <p class="fs-3 fw-bold">
                                    {{ strtoupper($sBTP->judul) }}
                                </p>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6">
                                <p class="mb-0 fs-6">Pembimbing 1</p>
                                <p class="fs-5 text-primary fw-bold">{{ $sBTP->pembimbingsatu->nama }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 fs-6">Pembimbing 2</p>
                                <p class="fs-5 text-primary fw-bold">{{ $sBTP->pembimbingdua->nama }}</p>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6">
                                <p class="mb-0 fs-6">Jadwal Sidang</p>
                                <p class="fs-5 text-primary fw-bold">{{ $sBTP->formatdate }} |
                                    Pk.{{ $sBTP->slot }}
                                </p>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 fs-6">Ruang Sidang</p>
                                <p class="fs-5 text-primary fw-bold">{{ $sBTP->ruangan->nama }}</p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <p class="mb-0 fs-5">Dosen Penguji 1</p>
                                <div class="mb-0">
                                    <select id="penguji1Select" class="form-select form-select-lg bg-select-penjadwalan"
                                        sidangId="{{ $sBTP->id }}" name="penguji1" required>
                                        <option selected disabled>Pilih Dosen Penguji 1</option>
                                        @foreach ($sBTP['ketuas'] as $ketua)
                                            <option value="{{ $ketua->id }}">{{ $ketua->nama }}
                                                ({{ $ketua->jmlh_ketua }} | {{ $ketua->jmlh_sekretaris }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 fs-5">Dosen Penguji 2</p>
                                <div class="mb-0">
                                    <select id="penguji1Select2" class="form-select form-select-lg bg-select-penjadwalan"
                                        name="penguji2" required>
                                        <option selected disabled>Pilih Dosen Penguji 2</option>
                                        @foreach ($sBTP['sekretaris'] as $sekret)
                                            <option value="{{ $sekret->id }}">{{ $sekret->nama }}
                                                ({{ $sekret->jmlh_ketua }} | {{ $sekret->jmlh_sekretaris }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <input type="hidden" name="sidangId" value="{{ $sBTP->id }}">
                                <button type="submit" class="btn btn-primary me-2">SIMPAN</button>
                </form>
                <form action="{{ route('sidang.resetPenjadwalan1Kalab') }}" method="POST" class="float-end">
                    @csrf
                    <input type="hidden" name="dosbing1Reset" value="{{ $sBTP->pembimbing_1 }}">
                    <input type="hidden" name="dosbing2Reset" value="{{ $sBTP->pembimbing_2 }}">
                    <input type="hidden" name="sidangIdReset" value="{{ $sBTP->id }}">
                    <button type="submit" class="btn btn-danger text-white"
                        onclick="return confirm('Apa Anda yakin untuk mereset jadwal sidang {{ $sBTP->mahasiswa->nrp }} - {{ $sBTP->mahasiswa->nama }}?');"><strong>RESET
                            JADWAL</strong></button>
                </form>
                <div class="clearfix"></div>
        </div>

        </div>
        </div>
    @endforeach

    @foreach ($sidangTerjadwalPenguji as $sTP)
        <form action="{{ route('sidang.resetPenjadwalan2') }}" method="POST">
            @csrf
            <div class="card px-4 py-3 mb-4">
                <div class="row mt-0 mb-0">
                    <div class="col-10">
                        @if($sTP->mengulang=='yes')
                        <p class="mb-0 text-primary fs-4 fw-bold">{{ $sTP->mahasiswa->nama }}
                            ({{ $sTP->mahasiswa->nrp }})
                            [{{ $sTP->konsentrasi->nama }}] <span class="badge bg-danger">Mengulang</span>
                        </p>
                        @else
                        <p class="mb-0 text-primary fs-4 fw-bold">{{ $sTP->mahasiswa->nama }}
                            ({{ $sTP->mahasiswa->nrp }})
                            [{{ $sTP->konsentrasi->nama }}]
                        </p>
                        @endif
                    </div>
                </div>
                <div class="row mt-0 mb-0">
                    <div class="col-10">
                        <p class="fs-3 fw-bold">
                            {{ strtoupper($sTP->judul) }}
                        </p>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-6">
                        <p class="mb-0 fs-6">Pembimbing 1</p>
                        <p class="fs-5 text-primary fw-bold">{{ $sTP->pembimbingsatu->nama }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 fs-6">Pembimbing 2</p>
                        <p class="fs-5 text-primary fw-bold">{{ $sTP->pembimbingdua->nama }}</p>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-6">
                        <p class="mb-0 fs-6">Jadwal Sidang</p>
                        <p class="fs-5 text-primary fw-bold">{{ $sTP->formatdate }} |
                            Pk.{{ $sTP->slot }}
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 fs-6">Ruang Sidang</p>
                        <p class="fs-5 text-primary fw-bold">{{ $sTP->ruangan->nama }}</p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <p class="mb-0 fs-5">Dosen Penguji 1</p>
                        <div class="mb-0">
                            <p class="fs-4 text-primary fw-bold">{{ $sTP->pengujisatu->nama }}</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 fs-5">Dosen Penguji 2</p>
                        <div class="mb-0">
                            <p class="fs-4 text-primary fw-bold">{{ $sTP->pengujidua->nama }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-end">
                        <input type="hidden" name="penguji1" value={{ $sTP->pengujisatu->id }}>
                        <input type="hidden" name="penguji2" value={{ $sTP->pengujidua->id }}>
                        <input type="hidden" name="sidangId" value="{{ $sTP->id }}">
                        @if ($periodeAktif->konfirmasi == 'paj')
                            <button type="submit" class="btn btn-warning"><strong>RESET PENGUJI</strong></button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    @endforeach
    </div>
@else
    <h1 class="fw-bold text-center text-danger">Penjadwalan belum dapat dilakukan!</h1>
    <h2 class="fw-bold text-center text-danger">PAJ masih melakukan proses penjadwalan</h2>
    @endif
@endsection

@section('script')
    <script>
        $('.jadwalSelect').on('change', function() {
            var select = $(this);
            var sidangId = $(this).attr('sidangId');
            var value = $(this).val();
            var tanggal = value.substring(0, 10);
            var slot = value.substring(11);

            $.ajax({
                type: 'POST',
                url: '{{ route('sidang.getRuanganAvailableKalab') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'tanggal': tanggal,
                    'slot': slot,
                },
                success: function(data) {
                    if (data.status == "ok") {
                        $('#ruangSelect_' + sidangId).html(data.msg);
                        $('#ruangSelect_' + sidangId).prop('disabled', false);
                        $('#ruangSelect_' + sidangId).addClass('bg-menu-theme');
                        $('#ruangSelect_' + sidangId).removeClass('bg-danger text-white');
                    } else {
                        $('#ruangSelect_' + sidangId).html(data.msg);
                        $('#ruangSelect_' + sidangId).prop('disabled', true);
                        $('#ruangSelect_' + sidangId).removeClass('bg-menu-theme');
                        $('#ruangSelect_' + sidangId).addClass('bg-danger text-white');
                    }
                }
            })
        });
    </script>
@endsection
