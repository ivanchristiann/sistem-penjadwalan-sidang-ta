@extends('layout.layoutpaj')

@section('content')
    <h2 class="text-primary"><strong>EDIT RUANG SIDANG</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
                @foreach ($errors->all() as $error)
                    <p class="mt-0 mb-1">- {{ $error }}</p>
                @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('ruangan.update', $ruangan->id) }}">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-6">
                <label for="txtNama" class="form-label">Nama Ruangan</label>
                <input type="text" class="form-control" id="txtNama" name="nama"
                    value='{{ $ruangan->nama }}' required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6 text-end">
                <a href="{{ url('paj/ruangan') }}" type="button" class="btn btn-danger">Batal</a>
                &nbsp;&nbsp;
                <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
            </div>
        </div>
    </form>
@endsection
