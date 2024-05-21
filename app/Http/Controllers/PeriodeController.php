<?php

namespace App\Http\Controllers;

use App\Exports\ExportJadwalSidang;
use App\Models\Dosen;
use App\Models\HistoryMahasiswa;
use App\Models\HistorySidang;
use App\Models\Jadwalkosong;
use App\Models\Konsentrasi;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\Ruangan;
use App\Models\Sidang;
use App\Models\TanggalMerah;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeNonaktif = Periode::select('periodes.id', 'periodes.semester', 'periodes.periode_sidang', 'periodes.tanggal_mulai', 'periodes.tanggal_berakhir', DB::raw('count(history_sidangs.history_mahasiswa_id) as jumlahMahasiswa'))
            ->leftJoin('history_sidangs', 'history_sidangs.periode_id', '=', 'periodes.id')
            ->groupBy('periodes.id', 'periodes.semester', 'periodes.periode_sidang', 'periodes.tanggal_mulai', 'periodes.tanggal_berakhir')
            ->where('periodes.status', 'nonaktif')
            ->orderBy('periodes.id', 'desc')
            ->get();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
            return view('paj.dashboard', compact('periodeAktif', 'periodeNonaktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
        } else {
            return view('paj.dashboard', compact('periodeAktif', 'periodeNonaktif'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jmlhDosen = Dosen::where('status', '1')->count();
        $jmlhKonsentrasi = Konsentrasi::where('status', '1')->count();
        $jmlhRuangan = Ruangan::where('status', '1')->count();

        if ($jmlhDosen == 0 || $jmlhKonsentrasi == 0 || $jmlhRuangan == 0) {
            return redirect()->route('paj.dashboard')->with('error', 'Tidak dapat menambah periode sidang baru karena dosen kosong/konsentrasi/ruangan masih kosong!');
        } else {
            $periodeAktif = Periode::select('status')->where('status', 'aktif')->first();
            if ($periodeAktif == null) {
                return view('paj.createdataperiode');
            } else {
                return redirect()->back();
            }
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
        $validatedData = $request->validate(
            [
                'semester' => 'required',
                'periodeSidang' => 'required',
                'tanggalMulai' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . date('Y-m-d')],
                'tanggalBerakhir' => ['required', 'date', 'after_or_equal:tanggalMulai'],
                'durasi' => 'required',
                'linkGoogleDrive' => 'required'
            ],
            [
                'tanggalMulai.required' => 'Tanggal mulai tidak boleh kosong!',
                'tanggalBerakhir.required' => 'Tanggal berakhir tidak boleh kosong!',
                'durasi.required' => 'Durasi tidak boleh kosong!',
                'linkGoogleDrive.required' => 'Link Google Drive tidak boleh kosong!'
            ]
        );

        $data = new Periode();
        $data->semester = $request->get('semester');
        $data->periode_sidang = $request->get('periodeSidang');
        $periodeSebelumnya = Periode::where('status', 'nonaktif')->orderBy('id', 'desc')->first();
        if ($periodeSebelumnya != null) {
            if ($request->get('tanggalMulai') >= $periodeSebelumnya->tanggal_berakhir) {
                $data->tanggal_mulai = $request->get('tanggalMulai');
                $data->tanggal_berakhir = $request->get('tanggalBerakhir');
            } else {
                return redirect()->back()->withErrors('Gagal menambah periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . ' karena tanggal mulai kurang dari tanggal berakhir (' . Carbon::parse($periodeSebelumnya->tanggal_berakhir)->isoFormat('D MMMM Y') . ') pada periode sebelumnya!');
            }
        } else {
            $data->tanggal_mulai = $request->get('tanggalMulai');
            $data->tanggal_berakhir = $request->get('tanggalBerakhir');
        }

        $data->durasi = $request->get('durasi');
        $data->link_google_drive = $request->get('linkGoogleDrive');
        $data->status = 'aktif';
        $data->konfirmasi = 'belum';
        $data->save();
        $tanggalMerahArr = $request->get('tglMerah');
        if ($tanggalMerahArr != null) {
            foreach ($tanggalMerahArr as $tglMerah) {
                $cekJadwal = Jadwalkosong::where('tanggal', $tglMerah)->get();
                if (count($cekJadwal) > 0) {
                    return redirect()->back()->withErrors('Gagal menambah periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . ' karena terdapat jadwal dosen pada tanggal merah ' . Carbon::parse($tglMerah)->isoFormat('D MMMM Y') . '!');
                } else {
                    if ($tglMerah >= $request->get('tanggalMulai') && $tglMerah <= $request->get('tanggalBerakhir')) {
                        $tanggal = new TanggalMerah();
                        $tanggal->tanggal = $tglMerah;
                        $tanggal->periode_id = $data->id;
                        TanggalMerah::where('periode_id', $data->id)->where('tanggal', $tglMerah)->delete();
                        $tanggal->save();
                    } else {
                        return redirect()->back()->withErrors('Gagal menambah periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . ' karena tanggal merah (' . Carbon::parse($tglMerah)->isoFormat('D MMMM Y') . ') tidak dalam rentang tanggal mulai dan tanggal berakhir periode!');
                    }
                }
            }
        }
        return redirect()->route('paj.dashboard')->with('status', 'Berhasil membuka periode baru!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function show(Periode $periode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function edit(Periode $periode)
    {
        $tanggalMerah = TanggalMerah::where('periode_id', $periode->id)->get();
        $dataSidang = Sidang::all();
        if (count($dataSidang) != 0) {
            $cekValidasi = Sidang::where('validasi', 'belum')->get();
        } else {
            $cekValidasi = ['ada'];
        }
        return view('paj.editdataperiode', compact('periode', 'tanggalMerah', 'cekValidasi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Periode $periode)
    {
        $dataSidang = Sidang::all();
        if (count($dataSidang) != 0) {
            $cekValidasi = Sidang::where('validasi', 'belum')->get();
            if (count($cekValidasi) > 0) {
                $request->validate(
                    ['durasi' => 'required'],
                    ['durasi.required' => 'Durasi tidak boleh kosong!']
                );
                $periode->durasi = $request->get('durasi');
            } else {
                $validatedData = $request->validate(
                    [
                        'semester' => 'required',
                        'periodeSidang' => 'required',
                        'tanggalMulai' => ['required', 'date', 'before_or_equal:tanggalBerakhir'],
                        'tanggalBerakhir' => ['required', 'date', 'after_or_equal:tanggalMulai'],
                        'linkGoogleDrive' => 'required'
                    ],
                    [
                        'tanggalMulai.required' => 'Tanggal mulai tidak boleh kosong!',
                        'tanggalBerakhir.required' => 'Tanggal berakhir tidak boleh kosong!',
                        'linkGoogleDrive.required' => 'Link Google Drive tidak boleh kosong!'
                    ]
                );
            }
        } else {
            $validatedData = $request->validate(
                [
                    'semester' => 'required',
                    'periodeSidang' => 'required',
                    'tanggalMulai' => ['required', 'date', 'before_or_equal:tanggalBerakhir'],
                    'tanggalBerakhir' => ['required', 'date', 'after_or_equal:tanggalMulai'],
                    'durasi' => 'required',
                    'linkGoogleDrive' => 'required'
                ],
                [
                    'tanggalMulai.required' => 'Tanggal mulai tidak boleh kosong!',
                    'tanggalBerakhir.required' => 'Tanggal berakhir tidak boleh kosong!',
                    'durasi.required' => 'Durasi tidak boleh kosong!',
                    'linkGoogleDrive.required' => 'Link Google Drive tidak boleh kosong!'
                ]
            );
            $periode->durasi = $request->get('durasi');
        }


        $periode->semester = $request->get('semester');
        $periode->periode_sidang = $request->get('periodeSidang');
        $periodeSebelumnya = Periode::where('status', 'nonaktif')->orderBy('id', 'desc')->first();
        if ($periodeSebelumnya != null) {
            if ($request->get('tanggalMulai') >= $periodeSebelumnya->tanggal_berakhir) {
                $periode->tanggal_mulai = $request->get('tanggalMulai');
                $periode->tanggal_berakhir = $request->get('tanggalBerakhir');
            } else {
                return redirect()->back()->withErrors('Gagal mengedit periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . ' karena tanggal mulai kurang dari tanggal berakhir (' . Carbon::parse($periodeSebelumnya->tanggal_berakhir)->isoFormat('D MMMM Y') . ') pada periode sebelumnya!');
            }
        } else {
            $periode->tanggal_mulai = $request->get('tanggalMulai');
            $periode->tanggal_berakhir = $request->get('tanggalBerakhir');
        }
        $periode->link_google_drive = $request->get('linkGoogleDrive');
        $periode->status = 'aktif';
        $periode->save();

        $tanggalMerahArr = $request->get('tglMerah');
        if ($tanggalMerahArr != null) {
            foreach ($tanggalMerahArr as $tglMerah) {
                $cekJadwal = Jadwalkosong::where('tanggal', $tglMerah)->get();
                if (count($cekJadwal) > 0) {
                    return redirect()->back()->withErrors('Gagal mengedit periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . ' karena terdapat jadwal dosen pada tanggal merah ' . Carbon::parse($tglMerah)->isoFormat('D MMMM Y') . '!');
                } else {
                    if ($tglMerah >= $request->get('tanggalMulai') && $tglMerah <= $request->get('tanggalBerakhir')) {
                        $tanggal = new TanggalMerah();
                        $tanggal->tanggal = $tglMerah;
                        $tanggal->periode_id = $periode->id;
                        TanggalMerah::where('periode_id', $periode->id)->where('tanggal', $tglMerah)->delete();
                        $tanggal->save();
                    } else {
                        return redirect()->back()->withErrors('Gagal mengedit periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . ' karena tanggal merah (' . Carbon::parse($tglMerah)->isoFormat('D MMMM Y') . ') tidak dalam rentang tanggal mulai dan tanggal berakhir periode!');
                    }
                }
            }
        }

        return redirect()->route('paj.dashboard')
            ->with('status', 'Berhasil mengedit periode ' . $request->get('semester') . " - " . $request->get('periodeSidang') . '!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Periode $periode)
    {
        //
    }

    public function showJadwalSidangPAJ()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
            $periodeAktifFinal = Periode::where('status', 'aktif')->where('konfirmasi', 'final')->first();
            if ($periodeAktifFinal != null) {
                $sidangs = Sidang::where('periode_id', $periodeAktifFinal->id)
                    ->orderBy('tanggal')
                    ->orderBy('nomor_slot')
                    ->get();
                $arrslot = UtilController::getAllSlots($periodeAktifFinal->durasi);
                return view('paj.periode.index', compact('periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'sidangs', 'arrslot'));
            }
            return view('paj.periode.index', compact('periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
        }
        return abort(403, 'Tidak Ada Periode Berlangsung');
    }

    public function detailPeriode($periode)
    {
        $periode = Periode::where('id', $periode)->first();
        $arrslot = UtilController::getAllSlots($periode->durasi);
        $historySidangs = HistorySidang::where('periode_id', '=', $periode->id)->get();
        $bulan = Carbon::parse($periode->tanggal_mulai)->isoFormat('MMMM Y');
        $tanggalMulai = Carbon::parse($periode->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
        $tanggalBerakhir = Carbon::parse($periode->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
        return view('paj.periode.historyperiode', compact('historySidangs', 'periode', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'arrslot'));
    }

    public function nonaktifkan()
    {
        try {
            $periodeAktif = Periode::where('status', 'aktif')->where('konfirmasi', 'final')->first();

            if ($periodeAktif != null) {
                $daftarMahasiswas = Mahasiswa::select('nrp', 'nama', 'user_id')->get();
                $daftarSidangs = Sidang::select('mahasiswa_id', 'judul', 'tanggal', 'pembimbing_1', 'pembimbing_2', 'penguji_1', 'penguji_2', 'ruangan_id', 'periode_id', 'konsentrasi_id', 'nomor_slot')->get();

                $curdate = date('Y-m-d');
                foreach ($daftarMahasiswas as $mahasiswa) {
                    $newHistoryMahasiswa = new HistoryMahasiswa();
                    $newHistoryMahasiswa->nrp = $mahasiswa->nrp;
                    $newHistoryMahasiswa->nama = $mahasiswa->nama;
                    $newHistoryMahasiswa->save();
                }

                foreach ($daftarSidangs as $sidang) {
                    $newHistorySidang = new HistorySidang();

                    $newHistorySidang->tanggal = $sidang->tanggal;
                    $newHistorySidang->judul = $sidang->judul;
                    $newHistorySidang->pembimbing_1 = $sidang->pembimbing_1;
                    $newHistorySidang->pembimbing_2 = $sidang->pembimbing_2;
                    $newHistorySidang->penguji_1 = $sidang->penguji_1;
                    $newHistorySidang->penguji_2 = $sidang->penguji_2;
                    $newHistorySidang->ruangan_id = $sidang->ruangan_id;
                    $newHistorySidang->konsentrasi_id = $sidang->konsentrasi_id;
                    $newHistorySidang->periode_id = $sidang->periode_id;
                    $newHistorySidang->nomor_slot = $sidang->nomor_slot;

                    $mahasiswa = Mahasiswa::find($sidang->mahasiswa_id);
                    $idHistoryMahasiswa = HistoryMahasiswa::select('id')->where('nrp', $mahasiswa->nrp)->orderBy('id', 'desc')->first();

                    $newHistorySidang->history_mahasiswa_id = $idHistoryMahasiswa->id;

                    $newHistorySidang->save();
                }

                DB::table('sidangs')->delete();
                DB::table('mahasiswas')->delete();


                DB::table('users')->where('role', '=', 'mahasiswa')->delete();
                DB::table('slot_jadwals')->delete();

                $periodeAktif->status = 'nonaktif';
                $periodeAktif->save();

                return redirect()->route('paj.dashboard')->with('status', 'Berhasil menutup periode!');
            } else {
                return redirect()->route('paj.dashboard')->with('error', 'Periode ini belum dikonfirmasi menjadi final');
            }
        } catch (\PDOException $ex) {
            return redirect()->route('paj.dashboard')->with('status', 'Gagal menutup periode!');
        }
    }
    public function pajKonfirmasi(Request $request)
    {
        $sidangBT = Sidang::where('tanggal', null)->get();

        if (count($sidangBT) == 0) {
            $idPeriode = $request->get('idPeriode');
            $periode = Periode::find($idPeriode);
            $periode->konfirmasi = 'paj';
            $periode->save();

            return redirect('paj/periode/penjadwalan')->with('status', 'Sukses konfirmasi jadwal sidang periode ini!');
        } else {
            return redirect('paj/periode/penjadwalan')->withErrors('Tidak dapat melakukan konfirmasi karena belum semua mahasiswa terjadwal!');
        }
    }

    public function kalabKonfirmasi(Request $request)
    {
        $sidangBTP = Sidang::where('penguji_1', null)->orWhere('penguji_2', null)->get();

        if (count($sidangBTP) == 0) {
            $idPeriode = $request->get('idPeriode');
            $periode = Periode::find($idPeriode);
            $periode->konfirmasi = 'scheduler';
            $periode->save();

            return redirect('dosen/scheduler/penjadwalan')->with('status', 'Sukses konfirmasi pengaturan penguji untuk periode ini!');
        } else {
            return redirect('dosen/scheduler/penjadwalan')->withErrors('Tidak dapat melakukan konfirmasi karena belum semua mahasiswa diatur pengujinya!');
        }
    }

    public function pajFinalisasiJadwal(Request $request)
    {
        $idPeriode = $request->get('idPeriode');
        $periode = Periode::find($idPeriode);
        $periode->konfirmasi = 'final';
        $periode->save();

        return redirect('paj/periode/penjadwalan')->with('status', 'Sukses finalisasi jadwal sidang periode ini! Segera kirimkan jadwal ke dosen dan mahasiswa!');
    }

    public function kirimJadwal()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            if ($periodeAktif->konfirmasi == 'final') {
                $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
                $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
                $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
                return view('paj.periode.kirimjadwal', compact('periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
            } else {
                return redirect()->back();
            }
        } else {
            return redirect()->back();
        }
    }

    public function kirimJadwalDosen(Request $request)
    {
        $message = $request->get('message');

        $periodeAktif = Periode::where('status', 'aktif')->get();
        $periodeBulanTahun = "";
        if (count($periodeAktif) != 0) {
            $bulan = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            $bulanAktif = date("n", strtotime($periodeAktif[0]->tanggal_mulai));
            $periodeBulanTahun = $bulan[$bulanAktif - 1] . " " . date("Y", strtotime($periodeAktif[0]->tanggal_mulai));
        }

        $sidangs = Sidang::orderBy('tanggal')->orderBy('nomor_slot')->get();
        $periodeAktif = $periodeAktif[0];
        foreach ($sidangs as $sidang) {
            $sidang->hari = Carbon::parse($sidang->tanggal)->isoFormat('dddd');
            $sidang->formattanggal = Carbon::parse($sidang->tanggal)->isoFormat('D MMMM Y');
            $sidang->formatslot = UtilController::getSlotJam($periodeAktif->durasi, $sidang->nomor_slot);
        }
        $data = ['periodeBulanTahun' => $periodeBulanTahun, 'sidangs' => $sidangs];
        $directory = 'jadwal/Jadwal Sidang Dosen Periode ' . $periodeBulanTahun . ' (' . date('Y-m-d') . ').pdf';
        $pdf = PDF::loadView('paj.periode.mailing.jadwaldosen', $data)->setPaper('a4', 'landscape')->save($directory);

        $dosens = Dosen::all();
        foreach ($dosens as $dosen) {
            MailController::mailJadwalDosen($dosen->user->email, $dosen->nama, $message, $directory);
        }
        return redirect()->route('periode.kirimjadwal')->with('status', 'Pengiriman Jadwal via Email ke Dosen Telah Berhasil!');
    }

    public function kirimJadwalMahasiswa(Request $request)
    {
        $message = $request->get('message');

        $periodeAktif = Periode::where('status', 'aktif')->get();
        $periodeBulanTahun = "";
        if (count($periodeAktif) != 0) {
            $bulan = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            $bulanAktif = date("n", strtotime($periodeAktif[0]->tanggal_mulai));
            $periodeBulanTahun = $bulan[$bulanAktif - 1] . " " . date("Y", strtotime($periodeAktif[0]->tanggal_mulai));
        }

        $sidangs = Sidang::orderBy('tanggal')->orderBy('nomor_slot')->get();
        $periodeAktif = $periodeAktif[0];
        foreach ($sidangs as $sidang) {
            $sidang->hari = Carbon::parse($sidang->tanggal)->isoFormat('dddd');
            $sidang->formattanggal = Carbon::parse($sidang->tanggal)->isoFormat('D MMMM Y');
            $sidang->formatslot = UtilController::getSlotJam($periodeAktif->durasi, $sidang->nomor_slot);
        }
        $data = ['periodeBulanTahun' => $periodeBulanTahun, 'sidangs' => $sidangs];
        $directory = 'jadwal/Jadwal Sidang Mahasiswa Periode ' . $periodeBulanTahun .  ' (' . date('Y-m-d') . ').pdf';
        $pdf = Pdf::loadView('paj.periode.mailing.jadwalmahasiswa', $data)->setPaper('a4', 'landscape')->save($directory);

        $mahasiswas = Mahasiswa::all();
        foreach ($mahasiswas as $mhs) {
            $email = 's' . $mhs->nrp . '@student.ubaya.ac.id';
            MailController::mailJadwalMahasiwa($email, $mhs->nama, $message, $directory);
        }

        return redirect()->route('periode.kirimjadwal')->with('status', 'Pengiriman Jadwal via Email ke Mahasiswa Telah Berhasil!');
    }

    public function downloadJadwal()
    {
        $periodeAktif = Periode::where('status', 'aktif')->get();
        $periodeBulanTahun = "";
        if (count($periodeAktif) != 0) {
            $bulan = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            $bulanAktif = date("n", strtotime($periodeAktif[0]->tanggal_mulai));
            $periodeBulanTahun = $bulan[$bulanAktif - 1] . " " . date("Y", strtotime($periodeAktif[0]->tanggal_mulai));
        }

        $sidangs = Sidang::orderBy('tanggal')->orderBy('nomor_slot')->get();
        $periodeAktif = $periodeAktif[0];
        foreach ($sidangs as $sidang) {
            $sidang->hari = Carbon::parse($sidang->tanggal)->isoFormat('dddd');
            $sidang->formattanggal = Carbon::parse($sidang->tanggal)->isoFormat('D MMMM Y');
            $sidang->formatslot = UtilController::getSlotJam($periodeAktif->durasi, $sidang->nomor_slot);
        }
        $data = ['periodeBulanTahun' => $periodeBulanTahun, 'sidangs' => $sidangs];
        $fileName = '[DOSEN] Jadwal Sidang Periode ' . $periodeBulanTahun . ' (' . date('Y-m-d') . ').pdf';
        $pdf = PDF::loadView('paj.periode.mailing.jadwaldosen', $data)->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    public function downloadJadwalMahasiswa(){
        $periodeAktif = Periode::where('status', 'aktif')->get();
        $periodeBulanTahun = "";
        if (count($periodeAktif) != 0) {
            $bulan = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            $bulanAktif = date("n", strtotime($periodeAktif[0]->tanggal_mulai));
            $periodeBulanTahun = $bulan[$bulanAktif - 1] . " " . date("Y", strtotime($periodeAktif[0]->tanggal_mulai));
        }

        $sidangs = Sidang::orderBy('tanggal')->orderBy('nomor_slot')->get();
        $periodeAktif = $periodeAktif[0];
        foreach ($sidangs as $sidang) {
            $sidang->hari = Carbon::parse($sidang->tanggal)->isoFormat('dddd');
            $sidang->formattanggal = Carbon::parse($sidang->tanggal)->isoFormat('D MMMM Y');
            $sidang->formatslot = UtilController::getSlotJam($periodeAktif->durasi, $sidang->nomor_slot);
        }
        $data = ['periodeBulanTahun' => $periodeBulanTahun, 'sidangs' => $sidangs];
        $fileName = '[MAHASISWA] Jadwal Sidang Periode ' . $periodeBulanTahun . ' (' . date('Y-m-d') . ').pdf';
        $pdf = Pdf::loadView('paj.periode.mailing.jadwalmahasiswa', $data)->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
    
    public function downloadExcel(){
        $periodeAktif = Periode::where('status', 'aktif')->get();
        $periodeBulanTahun = "";
        if (count($periodeAktif) != 0) {
            $bulan = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            $bulanAktif = date("n", strtotime($periodeAktif[0]->tanggal_mulai));
            $periodeBulanTahun = $bulan[$bulanAktif - 1] . " " . date("Y", strtotime($periodeAktif[0]->tanggal_mulai));
        }

        return Excel::download(new ExportJadwalSidang, 'Jadwal Sidang Keseluruhan Periode'.$periodeBulanTahun.'.xslx');
    }
}
