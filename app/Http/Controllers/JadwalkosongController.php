<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwalkosong;
use App\Models\Periode;
use DateTime;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalkosongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $idDosen = Auth::user()->dosen->id;
        DB::table('slot_jadwals')->where('dosen_id', $idDosen)->where('status', 'tersedia')->delete();
        $arrayJadwalKosong = $request->get('jamKosong');
        if ($arrayJadwalKosong != null) {
            for ($i = 0; $i < count($arrayJadwalKosong); $i++) {
                $jadwalKosong = new Jadwalkosong();
                $value = explode('-', $arrayJadwalKosong[$i]);
                $date = \Carbon\Carbon::createFromTimestamp($value[1])->toDateString();
                $jadwalKosong->tanggal = $date;
                $jadwalKosong->nomor_slot = $value[0];
                $jadwalKosong->dosen_id = $idDosen;
                $jadwalKosong->save();
            }
        }
        
        return redirect()->route('jadwalkosong.edit', $idDosen)->with("savestatus", "Jadwal Kosong Anda Berhasil disimpan!");


    }

    public function storefrompaj(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('D MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

            $slot = JadwalkosongController::getSlotJadwal($periodeAktif->durasi);
            $tanggalMerah = TanggalMerahController::getHoliday($periodeAktif);
            $tanggalSidang = TanggalMerahController::getDay($periodeAktif->id);

            $dosen1 = Dosen::find($request->get('dosen1'));
            $dosen2 = Dosen::find($request->get('dosen2'));

            DB::table('slot_jadwals')->where('dosen_id', $dosen1->id)->where('status', 'tersedia')->delete();
            $dosenAktif = Dosen::where('status', '1')->orderBy('nama')->get();
            $arrayJadwalKosong = $request->get('jamKosong');
            if ($arrayJadwalKosong != null) {
                for ($i = 0; $i < count($arrayJadwalKosong); $i++) {
                    $jadwalKosong = new Jadwalkosong();
                    $value = explode('-', $arrayJadwalKosong[$i]);
                    $date = \Carbon\Carbon::createFromTimestamp($value[1])->toDateString();
                    $jadwalKosong->tanggal = $date;
                    $jadwalKosong->nomor_slot = $value[0];
                    $jadwalKosong->dosen_id = $request->get('dosen1');
                    $jadwalKosong->save();
                }
            }
            return view('paj.dosen.jadwalkosong.index', compact('slot', 'tanggalMerah', 'tanggalSidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'dosenAktif', 'dosen1', 'dosen2'));
        }
        return abort(403, 'Tidak Ada Periode Berlangsung');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function show(Jadwalkosong $jadwalkosong)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function edit($idDosen)
    {
        if (Auth::user()->ganti_password == 0) {
            return redirect()->route('dosen.dashboard');
        } else {
            if ($idDosen == Auth::user()->dosen->id) {
                $dosen = Dosen::find($idDosen);
                $periodeAktif = Periode::where('status', 'aktif')->first();

                if ($periodeAktif != null && $periodeAktif->konfirmasi != 'final') {
                    $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
                    ;
                    $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
                    $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

                    $slot = JadwalkosongController::getSlotJadwal($periodeAktif->durasi);
                    $tanggalMerah = TanggalMerahController::getHoliday($periodeAktif->id);
                    $tanggalSidang = TanggalMerahController::getDay($periodeAktif->id);

                    return view('dosen.jadwalkosong.edit', compact('dosen', 'slot', 'tanggalMerah', 'tanggalSidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
                } else if ($periodeAktif->konfirmasi == 'final') {
                    return redirect()->route('dosen.dashboard');
                }
                return abort(403, 'Tidak Ada Periode Berlangsung');
            } else {
                return redirect()->route('dosen.dashboard');
            }
        }
    }

    public function editPaj(Request $request)
    {
        $dosen1 = Dosen::find($request->get('dosen1'));
        $dosen2 = Dosen::find($request->get('dosen2'));

        $periodeAktif = Periode::where('status', 'aktif')->first();

        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

            $slot = JadwalkosongController::getSlotJadwal($periodeAktif->durasi);
            $tanggalMerah = TanggalMerahController::getHoliday($periodeAktif->id);
            $tanggalSidang = TanggalMerahController::getDay($periodeAktif->id);

            return view('paj.dosen.jadwalkosong.edit', compact('slot', 'tanggalMerah', 'tanggalSidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'dosen1', 'dosen2'));
        } else {
            return view('paj.dosen.jadwalkosong.edit', compact('periodeAktif'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Jadwalkosong $jadwalkosong)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jadwalkosong $jadwalkosong)
    {
        //
    }

    public static function getSlotJadwal($durasi)
    {
        if ($durasi == '01:00:00') {
            $durasi = 3600;
        } elseif ($durasi == '01:30:00') {
            $durasi = 5400;
        } elseif ($durasi == '02:00:00') {
            $durasi = 7200;
        }
        $mulai = strtotime("08:00:00");
        $batasSelesai = strtotime("16:00:00");
        $slotJadwal = array();
        $jumlahSlot = 1;
        while ($mulai <= $batasSelesai) {
            $selesai = $mulai + $durasi;

            if ($selesai <= $batasSelesai) {
                if ($mulai == strtotime("12:00:00")) {
                    $mulai += 3600;
                    $selesai += 3600;
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                } else if ($mulai == strtotime("12:30:00")) {
                    $mulai += 1800;
                    $selesai += 1800;
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                } else {
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                }
                $slotJadwal[] = $jadwal;
            }
            $mulai = $selesai;
            $jumlahSlot += 1;
        }
        return $slotJadwal;
    }

    public static function getJadwalByDosen($idDosen, $nomorSlot, $tanggal)
    {
        $jadwal = Jadwalkosong::select('nomor_slot', 'tanggal', 'status')->where('dosen_id', $idDosen)->where('nomor_slot', $nomorSlot)->where('tanggal', $tanggal)->get();
        if ($jadwal->count() > 0) {
            if ($jadwal[0]->status == 'tersedia') {
                $hasil = "tersedia";
            } else {
                $hasil = "terpakai";
            }
        } else {
            $hasil = "false";
        }
        return $hasil;
    }

    public static function checkSudahIsiJadwal($dosen)
    {
        $jadwal = Jadwalkosong::where('dosen_id', $dosen)->get();
        return $jadwal->count() > 0 ? true : false;
    }

    public static function getAllJadwalDosen()
    {
        $dosenAktif = Dosen::where('status', '1')->orderBy('nama')->get();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeBulanTahun = "";
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
            $slot = JadwalkosongController::getSlotJadwal($periodeAktif->durasi);
            $tanggalMerah = TanggalMerahController::getHoliday($periodeAktif->id);
            $tanggalSidang = TanggalMerahController::getDay($periodeAktif->id);
            $dosen1 = Dosen::find($dosenAktif[0]->id);
            $dosen2 = Dosen::find($dosenAktif[1]->id);

            return view('paj.dosen.jadwalkosong.index', compact('dosenAktif', 'slot', 'tanggalMerah', 'tanggalSidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'dosen1', 'dosen2'));
        }
        return abort(403, 'Tidak Ada Periode Berlangsung');
    }

    public static function getAllJadwalDosenBaruPost(Request $request)
    {
        $dosenAktif = Dosen::where('status', '1')->orderBy('nama')->get();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

            $slot = JadwalkosongController::getSlotJadwal($periodeAktif->durasi);
            $tanggalMerah = TanggalMerahController::getHoliday($periodeAktif->id);
            $tanggalSidang = TanggalMerahController::getDay($periodeAktif->id);
            $dosen1 = Dosen::find($request->get('pilihDosen'));
            $dosen2 = Dosen::find($request->get('pilihDosen2'));


            return view('paj.dosen.jadwalkosong.index', compact('dosenAktif', 'slot', 'tanggalMerah', 'tanggalSidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'dosen1', 'dosen2'));
        }
        return abort(403, 'Tidak Ada Periode Berlangsung');
    }

    public static function getJadwalDosen()
    {
        if (Auth::user()->ganti_password == 0) {
            return redirect()->route('dosen.dashboard');
        } else {
            $dosen = Dosen::where('id', Auth::user()->dosen->id)->first();
            $periodeAktif = Periode::where('status', 'aktif')->first();
            if ($periodeAktif != null) {
                $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
                $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
                $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

                $slot = JadwalkosongController::getSlotJadwal($periodeAktif->durasi);
                $tanggalMerah = TanggalMerahController::getHoliday($periodeAktif->id);
                $tanggalSidang = TanggalMerahController::getDay($periodeAktif->id);

                $jadwal = Jadwalkosong::select('nomor_slot', 'tanggal', 'status')->where('dosen_id', $dosen->id)->get();

                return view('dosen.jadwalkosong.index', compact('jadwal', 'dosen', 'slot', 'tanggalMerah', 'tanggalSidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
            }
            return view('dosen.jadwalkosong.index', compact('periodeAktif', 'dosen'));
        }
    }
}