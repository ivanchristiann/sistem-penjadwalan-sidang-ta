@php
    $slot = App\Http\Controllers\JadwalkosongController::getSlotJadwal();
@endphp

@extends('layout.layoutdosen')

@section('content')
    @if (count($periodeAktif) != 0)
        @foreach ($periodeAktif as $pa)
            <div class="card card-sidang-berlangsung">
                <div class="d-flex align-items-end row">
                    <div class="col-12">
                        <div class="card-body">
                            <h2 class="card-title text-primary"><strong>Periode Sidang yang Sedang
                                    Berlangsung<strong></h2>
                            <p class="mb-0">PERIODE {{ $periodeBulanTahun }}</p>
                            <p><span style="color: red">
                                    ({{ $tanggalMulai }} - {{ $tanggalBerakhir }})
                                </span></p>
                            <p class="mb-0"><strong>Link Google Drive</strong><br>
                                <a href="{{ $periodeAktif->link_google_drive }}" target="blank"><u>{{ $periodeAktif->link_google_drive }}</u></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
    <h3 class="card-title text-primary"><strong>Jadwal Kosong Dosenn<strong></h3>

    <div style="overflow-x:auto;">
        <table class="table table-bordered" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th scope="col"></th>
                    @for ($i = 1; $i < 10; $i++)
                        <th scope="col" style="text-align: center;">{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($slot as $data)
                    <tr>
                        <td>{{ $data['slot'] }}</td>
                        @for ($i = 1; $i < 100; $i++)
                            <td style="text-align: center;"><input type="checkbox" id="" name=""
                                    value=""></td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection



@section('javascript')
@endsection
