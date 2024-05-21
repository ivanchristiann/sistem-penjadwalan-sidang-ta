@extends('layout.layoutmhs')

@section('content')
    <h2 class="text-primary"><strong>GANTI PASSWORD</strong></h2>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Maaf, terjadi kesalahan!</strong></p>
            @foreach ($errors->all() as $error)
                <p class="mt-0 mb-1">- {{ $error }}</p>
            @endforeach
        </div>
    @endif
    @if (session('status'))
        <br>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('status') }}
        </div>
        <br>
    @endif
    <form method="POST" action="{{ route('mahasiswa.gantipassword') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-12 col-md-5">
                <label for="txtPassword" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="txtPasswordBaru" name="passbaru"
                     required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 col-md-5">
                <label for="txtKonfirmasiPassword" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="txtKonfirmasiPassword" name="konfirmasipassbaru" 
                    required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 col-md-5 text-end">
                <input type="submit" class="btn btn-primary" id="btnSubmit" name="submit" value="Simpan">
            </div>
        </div>
    </form>
@endsection

@section('javascript')
@endsection
