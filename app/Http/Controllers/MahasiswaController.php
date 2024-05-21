<?php

namespace App\Http\Controllers;

use App\Imports\MahasiswaSidangImport;
use App\Mail\SendMail;
use App\Models\Dosen;
use App\Models\Jadwalkosong;
use App\Models\Mahasiswa;
use App\Models\HistoryMahasiswa;
use App\Models\HistorySidang;
use App\Models\Konsentrasi;
use App\Models\Periode;
use App\Models\Sidang;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

            $sidang = Sidang::where('mahasiswa_id', Auth::user()->mahasiswa->id)->first();
            $jadwalSidang = Carbon::parse($sidang->tanggal)->isoFormat('dddd, D MMMM Y');
            if ($sidang->tanggal != null) {
                $slotJam = (new UtilController)->getSlotJam($periodeAktif->durasi, $sidang->nomor_slot);
                return view('mahasiswa.dashboard', compact('sidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'jadwalSidang', 'slotJam'));
            }
            return view('mahasiswa.dashboard', compact('sidang', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'jadwalSidang'));
        }
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
        $validatedData = $request->validate(
            [
                'otp1' => 'required|numeric',
                'otp2' => 'required|numeric',
                'otp3' => 'required|numeric',
                'otp4' => 'required|numeric',
            ],
            [
                'otp1.required' => 'OTP tidak boleh kosong!',
                'otp2.required' => 'OTP tidak boleh kosong!',
                'otp3.required' => 'OTP tidak boleh kosong!',
                'otp4.required' => 'OTP tidak boleh kosong!',
            ]
        );
        $kode1 = $request->get('otp1');
        $kode2 = $request->get('otp2');
        $kode3 = $request->get('otp3');
        $kode4 = $request->get('otp4');
        $inputKodeOTP = $kode1 . $kode2 . $kode3 . $kode4;
        $kodeOTP = session('kodeOTP');
        $email = 's' . $request->get('hidNRP') . '@student.ubaya.ac.id';
        $snrp = 's' . $request->get('hidNRP');
        $nrp = $request->get('hidNRP');
        $mengulang = $request->get('hidMengulang');

        if ($kodeOTP == $inputKodeOTP) {
            if ($mengulang) {
                $periode = Periode::where('status', 'aktif')->first();
                $idHistory = HistorySidang::select('history_mahasiswa_id as id')
                    ->join('history_mahasiswas', 'history_sidangs.history_mahasiswa_id', '=', 'history_mahasiswas.id')
                    ->where('history_mahasiswas.nrp', $request->get('hidNRP'))
                    ->orderBy('history_sidangs.created_at', 'desc')
                    ->first();

                $datasidang = HistorySidang::select('id', 'judul', 'pembimbing_1', 'pembimbing_2', 'penguji_1', 'penguji_2', 'konsentrasi_id')
                    ->where('history_mahasiswa_id', $idHistory->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $cekMengulang = HistorySidang::select('id')
                    ->join('history_mahasiswas', 'history_sidangs.history_mahasiswa_id', '=', 'history_mahasiswas.id')
                    ->where('history_mahasiswas.nrp', $request->get('hidNRP'))
                    ->where('history_sidangs.judul', $datasidang->judul)
                    ->count();
                if ($cekMengulang < 3) {
                    User::create([
                        'email' => $email,
                        'username' => $snrp,
                        'password' => Hash::make($request->get('hidPass')),
                        'role' => 'mahasiswa',
                    ]);
                    $user_id = User::select('id')->where('username', 's' . $request->get('hidNRP'))->get();
                    $historyMahasiswa = HistoryMahasiswa::where('nrp', $request->get('hidNRP'))->first();
                    $mahasiswa = new Mahasiswa();
                    $mahasiswa->nrp = $historyMahasiswa->nrp;
                    $mahasiswa->nama = $historyMahasiswa->nama;
                    $mahasiswa->user_id = $user_id[0]['id'];
                    $mahasiswa->save();

                    $idPembimbing1 = $datasidang->pembimbing_1;
                    $idPembimbing2 = $datasidang->pembimbing_2;
                    $idPenguji1 = $datasidang->penguji_1;
                    $idPenguji2 = $datasidang->penguji_2;

                    // Cari Jadwal Tersedia
                    $jadwalSidang = DB::select(DB::Raw("SELECT sj1.tanggal, sj1.nomor_slot
                    FROM slot_jadwals sj1
                    JOIN slot_jadwals sj2 ON sj1.tanggal = sj2.tanggal AND sj1.nomor_slot = sj2.nomor_slot
                    JOIN slot_jadwals sj3 ON sj1.tanggal = sj3.tanggal AND sj1.nomor_slot = sj3.nomor_slot
                    JOIN slot_jadwals sj4 ON sj1.tanggal = sj4.tanggal AND sj1.nomor_slot = sj4.nomor_slot
                    WHERE sj1.dosen_id = " . $idPembimbing1 . " AND sj2.dosen_id = " . $idPembimbing2 . " AND sj1.status='tersedia' AND sj2.status='tersedia' AND sj3.dosen_id = " . $idPenguji1 . " AND sj4.dosen_id = " . $idPenguji2 . " AND sj3.status='tersedia' AND sj4.status='tersedia';"));
                    $jadwal = json_decode(json_encode($jadwalSidang), true);

                    // Cari Ruangan tersedia
                    $ruangan = null;
                    $tanggal = null;
                    $nomor_slot = null;
                    if ($jadwalSidang != []) {
                        foreach ($jadwal as $j) {
                            $cariruangan = DB::select(DB::raw("SELECT * from ruangans where id not in (select ruangan_id from sidangs where tanggal='" . $j['tanggal'] . "' and nomor_slot='" . $j['nomor_slot'] . "') limit 1"));
                            if ($cariruangan != []) {
                                $ruangan = json_decode(json_encode($cariruangan), true);
                                $tanggal = $j['tanggal'];
                                $nomor_slot = $j['nomor_slot'];
                                break;
                            }
                        }
                    }
                    $sidang = new Sidang();
                    $sidang->mahasiswa_id = $mahasiswa->id;
                    $sidang->judul = $datasidang->judul;
                    $sidang->pembimbing_1 = $idPembimbing1;
                    $sidang->pembimbing_2 = $idPembimbing2;
                    $sidang->konsentrasi_id = $datasidang->konsentrasi_id;
                    $sidang->periode_id = $periode->id;

                    if ($jadwalSidang != [] && $cariruangan != []) {
                        $slotDosbing1 = Jadwalkosong::where('dosen_id', $idPembimbing1)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
                        $slotDosbing1->status = 'terpakai';
                        $slotDosbing1->save();

                        $slotDosbing2 = Jadwalkosong::where('dosen_id', $idPembimbing2)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
                        $slotDosbing2->status = 'terpakai';
                        $slotDosbing2->save();

                        $slotPenguji1 = Jadwalkosong::where('dosen_id', $idPenguji1)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
                        $slotPenguji1->status = 'terpakai';
                        $slotPenguji1->save();

                        $slotPenguji2 = Jadwalkosong::where('dosen_id', $idPenguji2)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
                        $slotPenguji2->status = 'terpakai';
                        $slotPenguji2->save();

                        $sidang->penguji_1 = $idPenguji1;
                        $sidang->penguji_2 = $idPenguji2;
                        $sidang->tanggal = $tanggal;
                        $sidang->nomor_slot = $nomor_slot;
                        $sidang->ruangan_id = $ruangan[0]['id'];
                    }
                    $sidang->save();
                }
                return redirect('/login');
            } else {
                User::create([
                    'email' => $email,
                    'username' => $snrp,
                    'password' => Hash::make($request->get('hidPass')),
                    'role' => 'mahasiswa',
                ]);
                $user_id = User::select('id')->where('username', 's' . $request->get('hidNRP'))->get();
                $mahasiswa = Mahasiswa::where('nrp', $request->get('hidNRP'))->first();
                $mahasiswa->user_id = $user_id[0]['id'];
                $mahasiswa->save();

                return redirect('/login')->with('status', 'Registrasi berhasil! Anda sudah dapat login ke sistem');
            }
            return redirect('/register');
        } else {
            $message = "Kode OTP yang anda masukkan salah, coba lagi!";
            $nrp = $request->get('hidNRP');
            $password = $request->get('hidPass');
            return view('otp', compact('nrp', 'password', 'message'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function show(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function edit(Mahasiswa $mahasiswa)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        //
    }

    public function daftarSeluruhMahasiswa(Request $request)
    {
        $nrp = $request->get('nrp');
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $mahasiswaAktif = Mahasiswa::where('nrp', $nrp)->get();
        if ($periodeAktif != null) {
            $arrslot = UtilController::getAllSlots($periodeAktif->durasi);
            $mahasiswaNonaktif = HistoryMahasiswa::where('nrp', '=', $nrp)->get();
            return view('paj.carimahasiswa', compact('mahasiswaAktif', 'mahasiswaNonaktif', 'arrslot'));
        } else {
            $mahasiswaNonaktif = HistoryMahasiswa::where('nrp', '=', $nrp)->get();
            return view('paj.carimahasiswa', compact('mahasiswaNonaktif', 'mahasiswaAktif'));
        }
    }

    public function validasiRegistrasi(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $kodeOTP = random_int(1000, 9999);

            date_default_timezone_set("Asia/Jakarta");
            $validatedData = $request->validate(
                [
                    'nrp' => 'required|numeric',
                    'password' => 'required',
                    'password_confirmation' => 'required|same:password'
                ],
                [
                    'nrp.required' => 'NRP tidak boleh kosong!',
                    'nrp.numeric' => 'NRP harus berupa angka!',
                    'password_confirmation.same' => 'Konfirmasi Password harus sama dengan Password'
                ]
            );

            $password = $request->get('password');
            $nrp = $request->get('nrp');
            $mengulang = $request->get('mengulang');
            $email = "s" . $request->get('nrp') . "@student.ubaya.ac.id";
            $message = "Kode OTP telah dikirimkan ke email Anda";

            $checkUser = User::select('username')->where('username', '=', 's' . $nrp)->get();
            if (count($checkUser) == 0) {
                if (isset($mengulang)) {
                    $idHistory = HistorySidang::select('history_mahasiswa_id as id')
                        ->join('history_mahasiswas', 'history_sidangs.history_mahasiswa_id', '=', 'history_mahasiswas.id')
                        ->where('history_mahasiswas.nrp', $request->get('nrp'))
                        ->orderBy('history_sidangs.created_at', 'desc')
                        ->first();

                    if ($idHistory != null) {
                        $datasidang = HistorySidang::select('id', 'judul', 'pembimbing_1', 'pembimbing_2', 'penguji_1', 'penguji_2', 'konsentrasi_id')
                            ->where('history_mahasiswa_id', $idHistory->id)
                            ->orderBy('created_at', 'desc')
                            ->first();

                        $cekMengulang = HistorySidang::select('id')
                            ->join('history_mahasiswas', 'history_sidangs.history_mahasiswa_id', '=', 'history_mahasiswas.id')
                            ->where('history_mahasiswas.nrp', $request->get('nrp'))
                            ->where('history_sidangs.judul', $datasidang->judul)
                            ->count();

                        if ($cekMengulang > 2) {
                            return redirect('/errorregister')->with('message', 'mengulangover');
                        }
                    } else {
                        return redirect('/errorregister')->with('message', 'mengulang');
                    }

                    $mahasiswa = HistoryMahasiswa::where('nrp', $nrp)->first();
                    if ($mahasiswa != null) {
                        MailController::mailOTPMahasiswa($email, $mahasiswa->nama, $kodeOTP);
                        session(['kodeOTP' => $kodeOTP]);
                        return view('otp', compact('nrp', 'password', 'mengulang', 'message'));
                    } else {
                        return redirect('/errorregister')->with('message', 'mengulang');
                    }
                } else {
                    $mahasiswa = Mahasiswa::where('nrp', $nrp)->first();
                    if ($mahasiswa != null) {
                        MailController::mailOTPMahasiswa($email, $mahasiswa->nama, $kodeOTP);
                        session(['kodeOTP' => $kodeOTP]);
                        return view('otp', compact('nrp', 'password', 'message'));
                    } else {
                        return redirect('/errorregister')->with('message', 'baru');
                    }
                }
            } else {
                return redirect('/errorregister')->with('message', 'terdaftar');
            }
        } else {
            return redirect('/errorregister')->with('message', 'tidakadaperiode');
        }
    }

    public function registerCSV(Request $request)
    {
        $fileCSV = $request->file('csvfile');

        DB::beginTransaction();
        try {
            Excel::import(new MahasiswaSidangImport, $fileCSV);
            DB::commit();

            return redirect()->route('paj.periode.mahasiswa')->with('status', 'Upload Data Mahasiswa dengan CSV Telah Berhasil!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('paj.periode.mahasiswa')->withErrors('Terdapat Kesalahan pada File CSV yang diupload! Mohon cek kembali.');
        }
    }

    public function validasiDataSidang(Request $request)
    {
        $sidang = Sidang::find($request->get('id'));
        $sidang->validasi = 'paj';
        $sidang->save();

        return redirect()->route('paj.periode.mahasiswa')->with('status', 'Validasi Data Mahasiswa ' . $sidang->mahasiswa->nrp . '-' . $sidang->mahasiswa->nama . ' Berhasil!');
    }

    public function JadwalSidang()
    {
        $sidangs = "";
        $jadwal = "";
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
            if ($periodeAktif->konfirmasi == 'final') {
                $arrSlot = UtilController::getAllSlots($periodeAktif->durasi);
                $sidangs = Sidang::select('mahasiswa_id', 'tanggal', 'nomor_slot', 'ruangan_id', 'judul', 'pembimbing_1', 'pembimbing_2')
                    ->orderBy('tanggal')
                    ->orderBy('nomor_slot')
                    ->get();
                return view('mahasiswa.jadwalsidang', compact('jadwal', 'sidangs', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'arrSlot'));
            } else {
                $jadwal = "noJadwal";
                return view('mahasiswa.jadwalsidang', compact('jadwal', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
            }
        }
    }

    public function gantipassword(Request $request)
    {
        $validatedData = $request->validate(
            [
                'passbaru' => 'required|min:8',
                'konfirmasipassbaru' => 'required|min:8|same:passbaru',

            ],
            [
                'passbaru.required' => 'Password baru tidak boleh kosong!',
                'konfirmasipassbaru.required' => 'Konfirmasi password baru tidak boleh kosong!',
                'konfirmasipassbaru.same' => 'Konfirmasi Password Baru harus sama dengan Password Baru',
                'passbaru.min' => 'Password baru minimal 8 digit',
                'konfirmasipassbaru.min' => 'Konfirmasi password baru minimal 8 digit',
            ]
        );
        $userDosen = Auth::user();
        $userDosen->password = Hash::make($request->get('passbaru'));
        $userDosen->save();

        return redirect()->route('mahasiswa.gantipassword')->with('status', 'Password berhasil diupdate');
    }
}
