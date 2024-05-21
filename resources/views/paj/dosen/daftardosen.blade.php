@extends('layout.layoutpaj')

@section('content')
    <div>
        <h2 class="text-primary float-start"><strong>DAFTAR DOSEN</strong></h2>
        <a href="{{ route('dosen.create') }}" class="btn btn-primary float-end">Tambah</a>
    </div>
    <div class="clearfix"></div>
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

    {{-- Dosen Aktif --}}
    <div class="mb-4">
        <h4>Dosen Aktif</h4>
        <table id="activeDosenTable" class="table text-start wrap">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>NPK</th>
                    <th>Nama Dosen</th>
                    <th>Email</th>
                    <th>Konsentrasi</th>
                    <th>Posisi Penguji</th>
                    <th>Edit</th>
                    <th>Nonaktif</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 0; ?>
                @foreach ($dosenAktif as $da)
                    <tr>
                        <?php $counter++; ?>
                        <td class="text-center">{{ $counter }}</td>
                        <td>{{ $da->npk }}</td>
                        <td>{{ $da->nama }}</td>
                        <td>{{ $da->user->email }}</td>
                        <?php $konsentrasi = ''; ?>
                        @foreach ($da->konsentrasis as $k)
                            <?php $konsentrasi .= $k->nama . ', '; ?>
                        @endforeach
                        <?php $konsentrasi = rtrim($konsentrasi, ' ,'); ?>
                        <td>
                            {{ $konsentrasi }}
                        </td>
                        <td>{{ $da->posisi }}</td>
                        <td><a href="{{ route('dosen.edit', $da->id) }}" class="btn btn-sm btn-primary"><i
                                    class='bx bx-edit-alt'></i></a>
                        </td>
                        <td><button class="btn btn-sm btn-danger" onclick="nonaktifkan({{ $da->id }})"><i
                                    class='bx bx-power-off'></i></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <h4>Dosen Nonaktif</h4>
        {{-- Dosen Nonaktif --}}
        <table id="nonactiveDosenTable" class="table text-start nowrap w-100">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>NPK</th>
                    <th>Nama Dosen</th>
                    <th>Email</th>
                    <th>Konsentrasi</th>
                    <th>Posisi Penguji</th>
                    <th>Aktifkan</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 0; ?>
                @foreach ($dosenNonaktif as $key => $dn)
                    <tr>
                        <?php $counter++; ?>
                        <td class="text-center">{{ $counter }}</td>
                        <td>{{ $dn->npk }}</td>
                        <td>{{ $dn->nama }}</td>
                        <td>{{ $dn->user->email }}</td>
                        <?php $konsentrasi = ''; ?>
                        @foreach ($dn->konsentrasis as $k)
                            <?php $konsentrasi .= $k->nama . ', '; ?>
                        @endforeach
                        <?php $konsentrasi = rtrim($konsentrasi, ' ,'); ?>
                        <td>
                            {{ $konsentrasi }}
                        </td>
                        <td>{{ $dn->posisi }}</td>
                        <td><button onclick="aktifkan({{ $dn->id }})" class="btn btn-sm btn-success"><i
                                    class='bx bx-power-off'></i></button></td>
                        <td>
                            <form method="POST" action="{{ route('dosen.destroy', $dn->id) }}"
                                onclick="if(!confirm('Apakah anda yakin untuk menghapus dosen ini?')) return false;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class='bx bx-trash'></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#activeDosenTable').DataTable({

            });
            $('#nonactiveDosenTable').DataTable({
                "scrollX": true
            });
        });

        function nonaktifkan(id) {
            $.ajax({
                type: 'POST',
                url: '{{ route('dosen.nonaktifkan') }}',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'id': id,
                },
                success: function(data) {
                    if (data['status'] == 'success') {
                        window.location.reload(true);
                    } else if (data['status'] == 'fail') {
                        alert('Dosen tidak dapat dinonaktifkan karena telah terdaftar pada satu/lebih sidang!');
                    }
                }
            });
        }

        function aktifkan(id) {
            $.ajax({
                type: 'POST',
                url: '{{ route('dosen.aktifkan') }}',
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
