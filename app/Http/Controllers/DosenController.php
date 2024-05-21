<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\Dosen;
use App\Models\Konsentrasi;
use App\Models\Periode;
use App\Models\Sidang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $dosenAktif = Dosen::where('status', '1')->get();
        $dosenNonaktif = Dosen::where('status', '0')->get();

        return view('paj.dosen.daftardosen', compact('dosenAktif', 'dosenNonaktif'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $konsentrasi = Konsentrasi::select('id', 'nama')->where('status', '1')->get();
        if (count($konsentrasi) == 0) {
            return redirect()->route('dosen.index')->withErrors('Tidak dapat menambah dosen karena tidak ada konsentrasi yang aktif');
        } else {
            return view('paj.dosen.create', compact('konsentrasi'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validatedData = $request->validate(['nama' => 'required|unique:dosens|max:255', 'npk' => 'required|numeric|unique:dosens', 'email' => 'required|email:rfc,dns|ends_with:staff.ubaya.ac.id|unique:users|max:255', 'konsentrasi' => 'required|array', 'konsentrasi.*' => 'numeric', 'posisi' => 'required|array', 'posisi.*' => 'string'], ['nama.required' => 'Nama dosen tidak boleh kosong!', 'nama.unique' => 'Nama dosen ini telah terdaftar!', 'npk.required' => 'NPK tidak boleh kosong!', 'npk.numeric' => 'NPK harus berupa angka!', 'email.required' => 'Email tidak boleh kosong!', 'email.ends_with' => 'Email harus merupakan email UBAYA!']);

        $generatePass = "IF1n1P@ssw0rdUB4Y4#";
        $email = User::where('email', $request->get('email'))->first();
        $dosen = Dosen::where('npk', $request->get('npk'))->first();
        if ($email == null) {
            if ($dosen == null) {
                // Create User Dosen
                User::create([
                    'username' => $request->get('npk'),
                    'email' => $request->get('email'),
                    'password' => Hash::make($generatePass),
                    'role' => 'dosen',
                ]);

                $user_id = User::select('id')->where('username', $request->get('npk'))->get();

                // Create Data Dosen
                $dosen = new Dosen();
                $dosen->npk = $request->get('npk');
                $dosen->nama = $request->get('nama');
                $posisiArr = $request->get('posisi');

                $posisi = "";
                foreach ($posisiArr as $p) {
                    $posisi .= $p . ',';
                }
                $posisi = rtrim($posisi, ',');
                $dosen->posisi = $posisi;
                $dosen->status = 1;
                $dosen->user_id = $user_id[0]['id'];

                $dosen->save();

                $konsentrasiArr = $request->get('konsentrasi');
                foreach ($konsentrasiArr as $konsentrasiId) {
                    $dosen->konsentrasis()->attach($konsentrasiId);
                }

                //MailController::mailPassDosen($request->get('email'), $dosen->nama, $generatePass);

                return redirect()->route('dosen.index')->with('status', 'Berhasil mendaftarkan dosen ' . $request->get('nama') . '!');
            } else {
                return redirect()->route('dosen.create')->with('status', 'Gagal mendaftarkan dosen ' . $request->get('nama') . '! NPK telah digunakan oleh dosen lain!.');
            }
        } else {
            return redirect()->route('dosen.create')->with('status', 'Gagal mendaftarkan dosen ' . $request->get('nama') . '! Email telah digunakan oleh dosen lain!.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function show(Dosen $dosen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function edit(Dosen $dosen)
    {
        $konsentrasi = Konsentrasi::select('id', 'nama')->where('status', '1')->get();
        $kDosenArr = [];
        foreach ($dosen->konsentrasis as $dk) {
            $kDosenArr[] = $dk->nama;
        }

        $pDosenArr = explode(',', $dosen->posisi);
        return view('paj.dosen.edit', compact('dosen', 'konsentrasi', 'kDosenArr', 'pDosenArr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dosen $dosen)
    {
        $pesanError = [];
        if ($request->get('konsentrasi') == [] || $request->get('posisi') == []) {
            if ($request->get('konsentrasi') == []) {
                array_push($pesanError, "Konsentrasi tidak boleh kosong!");
            }
            if ($request->get('posisi') == []) {
                array_push($pesanError, "Posisi Penguji tidak boleh kosong!");
            }
            return redirect()->back()->withErrors($pesanError);
        }


        if ($dosen->npk != $request->get('npk')) {
            $ceknpk = Dosen::where('npk', $request->get('npk'))->first();
            if ($ceknpk == null) {
                $dosen->npk = $request->get('npk');
            } else {
                return redirect()->route('dosen.edit', $dosen->id)->with('status', 'Gagal mengubah dosen ' . $request->get('nama') . '! NPK telah digunakan oleh dosen lain!.');
            }
        }
        $dosen->nama = $request->get('nama');
        $dosen->save();

        $userIdDosen = $dosen->user_id;
        $user = User::where('id', $userIdDosen)->first();

        if ($dosen->user->email != $request->get('email')) {
            $cekemail = User::where('email', $request->get('email'))->first();
            if ($cekemail == null) {
                $user->email = $request->get('email');
                $user->save();
            } else {
                return redirect()->route('dosen.edit', $dosen->id)->with('status', 'Gagal mengubah dosen ' . $request->get('nama') . '! Email telah digunakan oleh dosen lain!.');
            }
        }

        // if ($request->get('password') != null) {
        //     $validatedData = $request->validate(
        //         [
        //             'password' => 'required|min:8',

        //         ],
        //         [
        //             'password.min' => 'Password baru minimal 8 digit',
        //         ]
        //     );
        //     $user->password = Hash::make($request->get('password'));
        //     $user->save();
        // }


        $konsentrasiArr = $request->get('konsentrasi');
        $dosen->konsentrasis()->detach();
        foreach ($konsentrasiArr as $konsentrasiId) {
            $dosen->konsentrasis()->attach($konsentrasiId);
        }

        $posisiArr = $request->get('posisi');
        $posisi = "";
        foreach ($posisiArr as $p) {
            $posisi .= $p . ',';
        }
        $posisi = rtrim($posisi, ',');
        $dosen->posisi = $posisi;

        $dosen->save();

        return redirect()->route('dosen.index')->with('status', 'Berhasil mengubah dosen ' . $request->get('nama') . '!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dosen  $dosen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dosen $dosen)
    {
        try {
            $npkDosen = $dosen->npk;
            $dosen->konsentrasis()->detach();
            $dosen->delete();

            $user = User::where('username', $npkDosen)->first();
            $user->delete();

            return redirect()->route('dosen.index')->with('status', 'Berhasil menghapus dosen ' . $dosen->nama . '!');
        } catch (\PDOException $e) {
            $msg = "Gagal menghapus Dosen " . $dosen->nama . " karena dosen telah terdaftar pada sidang-sidang yang sedang berlangsung/sudah selesai";

            return redirect()->route('dosen.index')->with('error', $msg);
        }
    }

    public static function dosenDashboard()
    {
        if (Auth::user()->ganti_password == 0) {
            return view('dosen.layoutdosengantipassword');
        } else {
            $idDosen = Auth::user()->dosen->id;
            $periodeAktif = Periode::where('status', 'aktif')->first();
            $jadwal = "";
            $arrslot = "";
            if ($periodeAktif != null) {
                $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
                $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
                $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
                if ($periodeAktif->konfirmasi == 'final') {
                    $sidangs = Sidang::where('periode_id', $periodeAktif->id)
                        ->where('pembimbing_1', $idDosen)
                        ->orWhere('pembimbing_2', $idDosen)
                        ->orWhere('penguji_1', $idDosen)
                        ->orWhere('penguji_2', $idDosen)
                        ->orderBy('tanggal')
                        ->orderBy('nomor_slot')
                        ->get();
                    $arrslot = UtilController::getAllSlots($periodeAktif->durasi);
                    return view('dosen.dashboard', compact('sidangs', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'arrslot', 'jadwal'));
                } else {
                    $jadwal = "noJadwal";
                }
                return view('dosen.dashboard', compact('periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'jadwal'));
            }
            return view('dosen.dashboard', compact('periodeAktif'));
        }
    }

    public static function jadwalSidang()
    {
        if (Auth::user()->ganti_password == 0) {
            return redirect()->route('dosen.dashboard');
        } else {
            $idDosen = Auth::user()->dosen->id;
            $periodeAktif = Periode::where('status', 'aktif')->first();
            $jadwal = "";
            $arrslot = "";
            if ($periodeAktif != null) {
                $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
                $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
                $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
                if ($periodeAktif->konfirmasi == 'final') {
                    $sidangs = Sidang::where('periode_id', $periodeAktif->id)
                        ->orderBy('tanggal')
                        ->orderBy('nomor_slot')
                        ->get();
                    $arrslot = UtilController::getAllSlots($periodeAktif->durasi);
                    return view('dosen.jadwalsidang', compact('sidangs', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'arrslot', 'jadwal'));
                } else {
                    $jadwal = "noJadwal";
                }
                return view('dosen.jadwalsidang', compact('periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'jadwal'));
            }
            return view('dosen.jadwalsidang', compact('periodeAktif'));
        }
    }

    public static function getAllDosenAktif()
    {
        $dosenAktif = Dosen::where('status', '1')->get();
        return $dosenAktif;
    }

    public function nonaktifkan(Request $request)
    {
        $dosenId = $request->get('id');
        $cekSidang = Sidang::select('id')->where('pembimbing_1', $dosenId)->orWhere('pembimbing_2', $dosenId)->orWhere('penguji_1', $dosenId)->orWhere('penguji_2', $dosenId)->count();
        if ($cekSidang == 0) {
            $data = Dosen::find($request->get('id'));
            $data->status = '0';
            $data->save();

            return response()->json(array('status' => 'success'), 200);
        } else {
            return response()->json(array('status' => 'fail'), 200);
        }
    }

    public function aktifkan(Request $request)
    {
        $data = Dosen::find($request->get('id'));
        $data->status = '1';
        $data->save();

        return response()->json(array('status' => 'success'), 200);
    }

    public function resetPassPage()
    {
        if (Auth::user()->ganti_password == 0) {
            return view('dosen.layoutdosengantipassword');
        } else {
            $periodeAktif = Periode::where('status', 'aktif')->first();

            return view('dosen.resetpassword', compact('periodeAktif'));
        }
    }

    public function resetPassword(Request $request)
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
        if ($userDosen->ganti_password == 0) {
            $userDosen->ganti_password = 1;
            $userDosen->password = Hash::make($request->get('passbaru'));
            $userDosen->save();
            return redirect()->route('dosen.dashboard')->with('status', 'Password berhasil diupdate');
        } else {
            $userDosen->password = Hash::make($request->get('passbaru'));
            $userDosen->save();
            return redirect()->route('dosen.resetpass')->with('status', 'Password berhasil diupdate');
        }
    }
}
