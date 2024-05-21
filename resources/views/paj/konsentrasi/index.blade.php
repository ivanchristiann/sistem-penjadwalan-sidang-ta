@extends('layout.layoutpaj')

@section('content')
    <div>
        <h2 class="text-primary float-start"><strong>DAFTAR KONSENTRASI</strong></h2>
        <a href="{{ route('konsentrasi.create') }}" class="btn btn-primary float-end">Tambah</a>
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
    {{-- Konsentrasi Aktif --}}
    <div class="mb-4">
        <h4>Konsentrasi Aktif</h4>
        <table id="activeProgramTable" class="table w-auto text-start">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>Nama Konsentrasi</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Nonaktif</th>
                </tr>
            </thead>
            <?php $counter = 0; ?>
            <tbody>
                @if (count($konsentrasiAktif) == 0)
                    <tr>
                        <td class="text-center" colspan="4">Tidak ada konsentrasi yang terdata</td>
                    </tr>
                @else
                    @foreach ($konsentrasiAktif as $ka)
                        <tr>
                            <?php $counter++; ?>
                            <td class="text-center">{{ $counter }}</td>
                            <td>{{ $ka->nama }}</td>
                            <td class="text-center"><a href="{{ route('konsentrasi.edit', $ka->id) }}"
                                    class="btn btn-sm btn-primary"><i class='bx bx-edit-alt'></i></a>
                            </td>
                            <td class="text-center"><button onclick="nonaktifkan({{ $ka->id }})"
                                    class="btn btn-sm btn-danger"><i class='bx bx-power-off'></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    {{-- Konsentrasi Non-Aktif --}}
    <div>
        <h4>Konsentrasi Nonaktif</h4>
        <table id="nonactiveProgramTable" class="table w-auto text-start">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>Nama Konsentrasi</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Aktifkan</th>
                </tr>
            </thead>
            <?php $counter = 0; ?>
            <tbody>
                @if (count($konsentrasiNonaktif) == 0)
                    <tr>
                        <td class="text-center" colspan="4">Tidak ada konsentrasi yang nonaktif</td>
                    </tr>
                @else
                    @foreach ($konsentrasiNonaktif as $kn)
                        <tr>
                            <?php $counter++; ?>
                            <td class="text-center">{{ $counter }}</td>
                            <td>{{ $kn->nama }}</td>
                            <td class="text-center"><a href="{{ route('konsentrasi.edit', $kn->id) }}"
                                    class="btn btn-sm btn-primary"><i class='bx bx-edit-alt'></i></a>
                            </td>
                            <td class="text-center"><button onclick="aktifkan({{ $kn->id }})"
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
                url: '{{ route('konsentrasi.nonaktifkan') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'id': id,
                },
                success: function(data) {
                    console.log(data['status']);
                    if (data['status'] == 'success') {
                        window.location.reload(true);
                    } else if (data['status'] == 'fail') {
                        alert('Konsentrasi tidak dapat dinonaktifkan karena telah terdata pada satu/lebih sidang!');
                    }
                }
            });
        }

        function aktifkan(id) {
            $.ajax({
                type: 'POST',
                url: '{{ route('konsentrasi.aktifkan') }}',
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
