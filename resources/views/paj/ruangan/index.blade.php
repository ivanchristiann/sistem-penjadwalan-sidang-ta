@extends('layout.layoutpaj')

@section('content')
    <div>
        <h2 class="text-primary float-start"><strong>DAFTAR RUANG SIDANG</strong></h2>
        <a href="{{ route('ruangan.create') }}" class="btn btn-primary float-end">Tambah</a>
    </div>
    <div class="clearfix"></div>
    @if (str_contains(session('status'), 'Berhasil'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('status') }}
        </div>
    @elseif(str_contains(session('status'), 'Gagal'))
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
    {{-- Ruangan Aktif --}}
    <div class="mb-4">
        <h4>Ruangan Aktif</h4>
        <table id="activeRuangTable" class="table w-auto text-start">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>Nama Ruangan</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Nonaktif</th>
                </tr>
            </thead>
            <?php $counter = 0; ?>
            <tbody>
                @if (count($ruanganAktif) == 0)
                    <tr>
                        <td class="text-center" colspan="4">Tidak ada ruangan yang terdata</td>
                    </tr>
                @else
                    @foreach ($ruanganAktif as $ra)
                        <tr>
                            <?php $counter++; ?>
                            <td class="text-center">{{ $counter }}</td>
                            <td>{{ $ra->nama }}</td>
                            <td class="text-center"><a href="{{ route('ruangan.edit', $ra->id) }}"
                                    class="btn btn-sm btn-primary"><i class='bx bx-edit-alt'></i></a>
                            </td>
                            <td class="text-center"><button onclick="nonaktifkan({{ $ra->id }})"
                                    class="btn btn-sm btn-danger"><i class='bx bx-power-off'></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    {{-- Ruangan Non-Aktif --}}
    <div>
        <h4>Ruangan Nonaktif</h4>
        <table id="nonactiveRuanganTable" class="table w-auto text-start">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>Nama Ruangan</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Aktifkan</th>
                </tr>
            </thead>
            <?php $counter = 0; ?>
            <tbody>
                @if (count($ruanganNonaktif) == 0)
                    <tr>
                        <td class="text-center" colspan="5">Tidak ada ruangan yang nonaktif</td>
                    </tr>
                @else
                    @foreach ($ruanganNonaktif as $rn)
                        <tr>
                            <?php $counter++; ?>
                            <td class="text-center">{{ $counter }}</td>
                            <td>{{ $rn->nama }}</td>
                            <td class="text-center"><a href="{{ route('ruangan.edit', $rn->id) }}"
                                    class="btn btn-sm btn-primary"><i class='bx bx-edit-alt'></i></a>
                            </td>
                            <td class="text-center"><button onclick="aktifkan({{ $rn->id }})"
                                    class="btn btn-sm btn-success"><i class='bx bx-power-off'></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        function nonaktifkan(id) {
            $.ajax({
                type: 'POST',
                url: '{{ route('ruangan.nonaktifkan') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'id': id,
                },
                success: function(data) {
                    if (data['status'] == 'success') {
                        window.location.reload(true);
                    }
                    else if (data['status'] == 'danger') {
                        alert('Ruangan tidak dapat dinonaktifkan karena telah terdata pada satu/lebih sidang!');
                    }
                }
            });
        }

        function aktifkan(id) {
            $.ajax({
                type: 'POST',
                url: '{{ route('ruangan.aktifkan') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'id': id,
                },
                success: function(data) {
                    if (data['status'] == 'success') {
                        window.location.reload(true);
                    }
                }
            });
        }
    </script>
@endsection
