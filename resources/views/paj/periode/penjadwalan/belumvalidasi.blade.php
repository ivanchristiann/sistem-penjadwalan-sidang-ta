@extends('layout.layoutpajperiode')

@section('content')
    @if ($status == 'tidakadamahasiswa')
        <h1 class="fw-bold text-center text-danger">Penjadwalan belum dapat dilakukan!</h1>
        <h2 class="fw-bold text-center text-danger">Tidak ada mahasiswa yang terdaftar pada sistem</h2>
    @elseif($status == 'belumvalidasi')
        <h1 class="fw-bold text-center text-danger">Penjadwalan belum dapat dilakukan!</h1>
        <h2 class="fw-bold text-center text-danger">Masih terdapat mahasiswa yang datanya belum tervalidasi</h2>
    @endif
@endsection
