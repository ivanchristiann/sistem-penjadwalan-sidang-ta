@extends('layout.layoutpajperiode')

@section('content')
    <h2 class="text-primary"><strong>Penjadwalan Dosen Pembimbing</strong></h2>
    @if ($periodeAktif->konfirmasi == 'paj')
        <div class="alert alert-warning" role="alert">
            <p class="mb-0">Saat ini Scheduler sedang melakukan penjadwalan</p>
        </div>
    @endif
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
    @if ($periodeAktif->konfirmasi == 'belum')
        <div class="mb-4">
            <h3 class="text-danger fw-bold">Daftar Mahasiswa yang Tidak ditemukan Jadwal Dosen Pembimbing</h3>
            <table id="SidangNoSlotTable" class="table wrap">
                <thead class="bg-danger">
                    <tr>
                        <th class="text-white">No</th>
                        <th class="text-white">NRP</th>
                        <th class="text-white">Nama Mahasiswa</th>
                        <th class="text-white">Peminatan</th>
                        <th class="text-white">Pembimbing 1</th>
                        <th class="text-white">Pembimbing 2</th>
                    </tr>
                </thead>
                <?php $counter = 0; ?>
                <tbody>
                    @foreach ($sidangTidakAdaSlot as $sTAS)
                        <?php $counter++; ?>
                        <tr>
                            <td class="fs-5">{{ $counter }}</td>
                            <td class="fs-5">{{ $sTAS->mahasiswa->nrp }}</td>
                            <td class="fs-5">{{ $sTAS->mahasiswa->nama }}</td>
                            <td class="fs-5">{{ $sTAS->konsentrasi->nama }}</td>
                            <td class="fs-5">{{ $sTAS->pembimbingsatu->nama }}</td>
                            <td class="fs-5">{{ $sTAS->pembimbingdua->nama }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <div>
        <div class="row mb-3">
            <div class="col-8">
                <h3 class="fw-bold">Penjadwalan Maju Sidang</h3>
            </div>
            <div class="col-4 text-end">
                @if ($periodeAktif->konfirmasi == 'belum' && count($periodeAktif->sidangs) != 0)
                    <form action="{{ route('periode.pajkonfirmasi') }}" method="post">
                        @csrf
                        <input type="hidden" name="idPeriode" value="{{ $periodeAktif->id }}">
                        <button type="submit" class="btn btn-primary">Konfirmasi Jadwal</button>
                    </form>
                @elseif($periodeAktif->konfirmasi == 'scheduler')
                    <form action="{{ route('periode.pajfinalisasijadwal') }}" method="post">
                        @csrf
                        <input type="hidden" name="idPeriode" value="{{ $periodeAktif->id }}">
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Apa anda yakin untuk menyimpan seluruh jadwal sidang mahasiswa? Pastikan seluruh jadwal sudah benar!');">Finalisasi
                            Jadwal</button>
                    </form>
                @endif
            </div>
        </div>
        @if ($periodeAktif->konfirmasi == 'belum')
            @foreach ($sidangBelumTerjadwal as $sBT)
                <form action="{{ route('sidang.setPenjadwalan1') }}" method="POST">
                    @csrf
                    <div class="card px-4 py-3 mb-4">
                        <div class="row mt-0 mb-0">
                            <div class="col-10">
                                @if ($sBT->mengulang == 'yes')
                                    <p class="mb-0 text-primary fs-5 fw-bold">{{ $sBT->mahasiswa->nama }}
                                        ({{ $sBT->mahasiswa->nrp }})
                                        <span class="badge bg-danger">Mengulang</span>
                                    </p>
                                @else
                                    <p class="mb-0 text-primary fs-5 fw-bold">{{ $sBT->mahasiswa->nama }}
                                        ({{ $sBT->mahasiswa->nrp }})
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-0 mb-0">
                            <div class="col-10">
                                <p class="fs-5 fw-bold">
                                    {{ Str::upper($sBT->judul) }}
                                </p>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6">
                                <p class="mb-0 fs-5">Pembimbing 1</p>
                                <p class="fs-4 text-primary fw-bold">{{ $sBT->pembimbingsatu->nama }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 fs-5">Pembimbing 2</p>
                                <p class="fs-4 text-primary fw-bold">{{ $sBT->pembimbingdua->nama }}</p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <p class="mb-0 fs-5">Jadwal Sidang</p>
                                <div class="mb-0">
                                    <select id="jadwalSelect"
                                        class="form-select form-select-lg bg-select-penjadwalan jadwalSelect"
                                        sidangId="{{ $sBT->id }}" name="slot" required>
                                        <option selected disabled>Pilih Jadwal</option>
                                        @foreach ($sBT['slot'] as $slot)
                                            <option value="{{ $slot->tanggal }}#{{ $slot->nomor_slot }}">
                                                {{ $slot->formatdate . ' | Pk.' . $slot->formattime }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <p class="mb-0 fs-5">Ruang Sidang</p>
                                <div class="mb-0">
                                    <select id="ruangSelect_{{ $sBT->id }}"
                                        class="form-select form-select-lg bg-select-penjadwalan" name="ruangan" required>
                                        <option selected disabled>Pilih Ruangan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <input type="hidden" name="dosbing1" value={{ $sBT->pembimbingsatu->id }}>
                                <input type="hidden" name="dosbing2" value={{ $sBT->pembimbingdua->id }}>
                                <input type="hidden" name="sidangId" value={{ $sBT->id }}>
                                <button type="submit" class="btn btn-primary">SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endforeach
        @endif
        @foreach ($sidangTerjadwal as $sT)
            @if ($sT->periode->konfirmasi == 'belum')
                <form action="{{ route('sidang.resetPenjadwalan1') }}" method="POST">
            @endif
            @csrf
            <div class="card px-4 py-3 mb-4">
                <div class="row mt-0 mb-0">
                    <div class="col-10">
                        @if ($sT->mengulang == 'yes')
                            <p class="mb-0 text-primary fs-5 fw-bold">{{ $sT->mahasiswa->nama }}
                                ({{ $sT->mahasiswa->nrp }})
                                <span class="badge bg-danger">Mengulang</span>
                            </p>
                        @else
                            <p class="mb-0 text-primary fs-5 fw-bold">{{ $sT->mahasiswa->nama }}
                                ({{ $sT->mahasiswa->nrp }})
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row mt-0 mb-0">
                    <div class="col-10">
                        <p class="fs-5 fw-bold">
                            {{ Str::upper($sT->judul) }}
                        </p>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-6">
                        <p class="mb-0 fs-5">Pembimbing 1</p>
                        <p class="fs-4 text-primary fw-bold">{{ $sT->pembimbingsatu->nama }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 fs-5">Pembimbing 2</p>
                        <p class="fs-4 text-primary fw-bold">{{ $sT->pembimbingdua->nama }}</p>
                    </div>
                </div>
                @if ($periodeAktif->konfirmasi == 'scheduler' || $periodeAktif->konfirmasi == 'final')
                    <div class="row mb-0">
                        <div class="col-6">
                            <p class="mb-0 fs-5">Penguji 1</p>
                            <p class="fs-4 text-primary fw-bold">{{ $sT->pengujisatu->nama }}</p>
                        </div>
                        <div class="col-6">
                            <p class="mb-0 fs-5">Penguji 2</p>
                            <p class="fs-4 text-primary fw-bold">{{ $sT->pengujidua->nama }}</p>
                        </div>
                    </div>
                @endif
                <div class="row mb-0">
                    <div class="col-6">
                        <p class="mb-0 fs-5">Jadwal Sidang</p>
                        <p class="mb-0 text-primary fs-4 fw-bold">{{ $sT->formatdate . ' | Pk.' . $sT->slot }}</p>
                        </p>
                    </div>
                    <div class="col-3">
                        <p class="mb-0 fs-5">Ruang Sidang</p>
                        <p class="mb-0 text-primary fs-4 fw-bold">{{ $sT->ruangan->nama }}</p>
                    </div>
                </div>
                @if ($periodeAktif->konfirmasi == 'belum')
                    <div class="row mb-2">
                        <div class="col-12 fs-5">
                            <p class="mb-0">Status Penguji</p>
                            @if ($sT->jumlah_penguji == 0)
                                <p class="text-danger fw-bold">Tidak Tersedia sesuai Konsentrasi</p>
                            @elseif($sT->jumlah_penguji == -1)
                                <p class="text-primary fw-bold">Sudah Terjadwal</p>
                            @elseif($sT->jumlah_penguji == 1)
                                <p class="text-warning fw-bold">Hanya Tersedia 1 sesuai Konsentrasi</p>
                            @else
                                <p class="text-primary fw-bold">Tersedia {{ $sT->jumlah_penguji }} sesuai Konsentrasi</p>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12 text-end">
                        <input type="hidden" name="dosbing1" value={{ $sT->pembimbingsatu->id }}>
                        <input type="hidden" name="dosbing2" value={{ $sT->pembimbingdua->id }}>
                        <input type="hidden" name="sidangId" value={{ $sT->id }}>
                        @if ($sT->periode->konfirmasi == 'belum')
                            @if ($sT->jumlah_penguji != -1)
                                <button type="submit" class="btn btn-danger fw-bold">RESET</button>
                            @endif
                        @elseif($sT->periode->konfirmasi == 'scheduler' || $sT->periode->konfirmasi == 'final')
                            <a href="{{ route('sidang.editKK', $sT->id) }}" data-toggle="modal"
                                class="btn btn-danger fw-bold" onclick="editKasusKhusus({{ $sT->id }})">EDIT
                                KASUS KHUSUS</a>
                        @endif
                    </div>
                </div>
            </div>
            @if ($sT->periode->konfirmasi == 'belum')
                </form>
            @endif
        @endforeach
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#SidangNoSlotTable').DataTable({});
        });
        $('.jadwalSelect').on('change', function() {
            var select = $(this);
            var sidangId = $(this).attr('sidangId');
            var value = $(this).val();
            var tanggal = value.substring(0, 10);
            var slot = value.substring(11);

            $.ajax({
                type: 'POST',
                url: '{{ route('sidang.getRuanganAvailable') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'tanggal': tanggal,
                    'slot': slot
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
