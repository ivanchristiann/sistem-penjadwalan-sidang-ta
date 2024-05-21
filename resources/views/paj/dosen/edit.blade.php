@extends('layout.layoutpaj')

@section('content')
    <h2 class="text-primary"><strong>EDIT DOSEN</strong></h2>
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
    <form method="POST" action="{{ route('dosen.update', $dosen->id) }}">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtNama" class="form-label">Nama Lengkap (Beserta Gelar)</label>
                <input type="text" class="form-control" id="txtNama" name="nama" value="{{ $dosen->nama }}"
                    required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtNPK" class="form-label">NPK</label>
                <input type="text" class="form-control" id="txtNPK" name="npk" value="{{ $dosen->npk }}"
                    required>
                <input type="hidden" name="npklama" value="{{ $dosen->npk }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="txtEmail" class="form-label">Email UBAYA</label>
                <input type="email" class="form-control" id="txtEmail" name="email" value="{{ $dosen->user->email }}"
                    required>
            </div>
        </div>
        {{-- <div class="row mb-3">
            <div class="col-12">
                <label for="txtPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="txtPassword" name="password" value="">
            </div>
        </div> --}}
        <div class="row mb-3">
            <label for="txtEmail" class="form-label">Konsentrasi</label>
            @foreach ($konsentrasi as $k)
                @if (in_array($k->nama, $kDosenArr))
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="konsentrasi[]" value="{{ $k->id }}"
                                checked="true">
                            <label class="form-check-label"> {{ $k->nama }}</label>
                        </div>
                    </div>
                @else
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="konsentrasi[]"
                                value="{{ $k->id }}">
                            <label class="form-check-label"> {{ $k->nama }}</label>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="row mb-3">
            <label for="txtEmail" class="form-label">Posisi Penguji</label>

            @if (in_array('Pembimbing', $pDosenArr))
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="posisi[]" value="Pembimbing" checked="true">
                        <label class="form-check-label"> Pembimbing</label>
                    </div>
                </div>
            @else
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="posisi[]" value="Pembimbing">
                        <label class="form-check-label"> Pembimbing</label>
                    </div>
                </div>
            @endif

            @if (in_array('Ketua', $pDosenArr))
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" id="chcPKetua" type="checkbox" name="posisi[]" value="Ketua" checked="true">
                        <label class="form-check-label"> Ketua</label>
                    </div>
                </div>
            @else
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" id="chcPKetua" type="checkbox" name="posisi[]" value="Ketua">
                        <label class="form-check-label"> Ketua</label>
                    </div>
                </div>
            @endif

            @if (in_array('Sekretaris', $pDosenArr))
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" id="chcPSekretaris" type="checkbox" name="posisi[]" value="Sekretaris" checked="true">
                        <label class="form-check-label"> Sekretaris</label>
                    </div>
                </div>
            @else
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" id="chcPSekretaris" type="checkbox" name="posisi[]" value="Sekretaris">
                        <label class="form-check-label"> Sekretaris</label>
                    </div>
                </div>
            @endif

            @if (in_array('Scheduler', $pDosenArr))
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="posisi[]" value="Scheduler"
                            checked="true">
                        <label class="form-check-label"> Scheduler</label>
                    </div>
                </div>
            @else
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="posisi[]" value="Scheduler">
                        <label class="form-check-label"> Scheduler</label>
                    </div>
                </div>
            @endif
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
