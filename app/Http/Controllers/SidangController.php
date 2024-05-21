<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\HistoryMahasiswa;
use App\Models\HistorySidang;
use App\Models\Jadwalkosong;
use App\Models\Konsentrasi;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\Ruangan;
use App\Models\Sidang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SidangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sidangs = Sidang::select('mahasiswas.nrp as NRP', 'mahasiswas.nama as Nama Mahasiswa', 'konsentrasis.nama as Peminatan', 'sidangs.judul as Judul', 'pembimbing1.nama as Pembimbing 1', 'pembimbing2.nama as Pembimbing 2')
            ->join('mahasiswas', 'mahasiswas.id', '=', 'sidangs.mahasiswa_id')
            ->join('dosens as pembimbing1', 'pembimbing1.id', '=', 'sidangs.pembimbing_1')
            ->join('dosens as pembimbing2', 'pembimbing2.id', '=', 'sidangs.pembimbing_2')
            ->join('konsentrasis', 'konsentrasis.id', '=', 'sidangs.konsentrasi_id')
            ->get();

        return view('paj.daftarmahasiswa', ['sidangs' => $sidangs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dosen = Dosen::where('status', '1')->get();
        $konsentrasi = Konsentrasi::where('status', '1')->get();

        return view('mahasiswa.registrasidatamajusidang', ['dosens' => $dosen, 'konsentrasis' => $konsentrasi]);
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
        $validatedData = $request->validate(
            [
                'nama' => 'required|max:255',
                'nrp' => 'required|numeric|unique:mahasiswas',
                'pembimbing1' => 'required',
                'pembimbing2' => 'required|different:pembimbing1',
                'konsentrasi' => 'required'
            ],
            [
                'nama.required' => 'Nama mahasiswa tidak boleh kosong!',
                'nrp.required' => 'NRP tidak boleh kosong!',
                'nrp.numeric' => 'NRP harus berupa angka!',
                'pembimbing2.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1',
            ]
        );

        $password = $request->get('password');
        $periode = Periode::where('status', 'aktif')->first();
        $mengulang = $request->get('mengulang');

        if ($mengulang) {
            $cekMahasiswa = HistoryMahasiswa::where('nrp', $request->get('nrp'))->get();
            if (count($cekMahasiswa) == 0) {
                return redirect()->back()->withErrors('Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! NRP tidak ditemukan pada sistem!');
            } else {
                $idHistory = HistorySidang::select('history_mahasiswa_id as id', 'history_mahasiswas.nrp as nrp', 'history_sidangs.konsentrasi_id as konsentrasi', 'history_mahasiswas.nama as nama')
                    ->join('history_mahasiswas', 'history_sidangs.history_mahasiswa_id', '=', 'history_mahasiswas.id')
                    ->where('history_mahasiswas.nrp', $request->get('nrp'))
                    ->orderBy('history_sidangs.created_at', 'desc')
                    ->first();

                $datasidang = HistorySidang::select('id', 'judul', 'pembimbing_1', 'pembimbing_2', 'penguji_1', 'penguji_2', 'konsentrasi_id')
                    ->where('history_mahasiswa_id', $idHistory->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $cekMengulang = HistorySidang::select('id')
                    ->join('history_mahasiswas', 'history_sidangs.history_mahasiswa_id', '=', 'history_mahasiswas.id')
                    ->where('history_mahasiswas.nrp', $request->get('nrp'))
                    ->where('history_sidangs.judul', $datasidang->judul)
                    ->count();

                if ($cekMengulang < 3) {
                    $pesanError = [];
                    if ($idHistory->nrp != $request->get('nrp') || $datasidang->judul != $request->get('judul') || $datasidang->pembimbing_1 != $request->get('pembimbing1') || $datasidang->pembimbing_2 != $request->get('pembimbing2') || $idHistory->konsentrasi != $request->get('konsentrasi') || $idHistory->nama != $request->get('nama')) {
                        if ($idHistory->nama != $request->get('nama')) {
                            array_push($pesanError, 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! Nama yang dimasukkan tidak sesuai dengan data mahasiswa pada sidang sebelumnya!');
                        }
                        if ($idHistory->nrp != $request->get('nrp')) {
                            array_push($pesanError, 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! NRP yang dimasukkan tidak sesuai dengan data mahasiswa pada sidang sebelumnya!');
                        }
                        if ($datasidang->judul != $request->get('judul')) {
                            array_push($pesanError, 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! Judul yang dimasukkan tidak sesuai dengan sidang sebelumnya!');
                        }
                        if ($datasidang->pembimbing_1 != $request->get('pembimbing1')) {
                            array_push($pesanError, 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! Pembimbing 1 yang dimasukkan tidak sesuai dengan sidang sebelumnya!');
                        }
                        if ($datasidang->pembimbing_2 != $request->get('pembimbing2')) {
                            array_push($pesanError, 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! Pembimbing 2 yang dimasukkan tidak sesuai dengan sidang sebelumnya!');
                        }
                        if ($idHistory->konsentrasi != $request->get('konsentrasi')) {
                            array_push($pesanError, 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! Konsentrasi yang dimasukkan tidak sesuai dengan data mahasiswa pada sidang sebelumnya!');
                        }
                        return redirect()->back()->withErrors($pesanError);
                    } else {
                        $historyMahasiswa = HistoryMahasiswa::where('nrp', $request->get('nrp'))->first();
                        $mahasiswa = new Mahasiswa();
                        $mahasiswa->nrp = $historyMahasiswa->nrp;
                        $mahasiswa->nama = $historyMahasiswa->nama;
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
                        $sidang->created_at = now("Asia/Jakarta");
                        $sidang->updated_at = now("Asia/Jakarta");
                        $sidang->save();
                    }
                } else {
                    return redirect()->route('paj.periode.mahasiswa')->with('status', 'Gagal mendaftarkan mahasiswa ' . $request->get('nama') . '! Mahasiswa telah mengulang sidang dengan judul yang sama sebanyak 3 kali!');
                }
            }
        } else {
            $mahasiswa = new Mahasiswa();
            $mahasiswa->nrp = $request->get('nrp');
            $mahasiswa->nama = $request->get('nama');
            $mahasiswa->save();

            $mahasiswa_id = Mahasiswa::select('id')->where('nrp', $request->get('nrp'))->get();
            $sidang = new Sidang();
            $sidang->mahasiswa_id = $mahasiswa_id[0]['id'];
            $sidang->judul = $request->get('judul');
            $sidang->pembimbing_1 = $request->get('pembimbing1');
            $sidang->pembimbing_2 = $request->get('pembimbing2');
            $sidang->periode_id = $periode->id;
            $sidang->konsentrasi_id = $request->get('konsentrasi');
            $sidang->created_at = now("Asia/Jakarta");
            $sidang->updated_at = now("Asia/Jakarta");
            $sidang->save();
        }
        return redirect()->route('paj.periode.mahasiswa')->with('status', 'Berhasil mendaftarkan mahasiswa ' . $request->get('nama') . '!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sidang  $sidang
     * @return \Illuminate\Http\Response
     */
    public function show(Sidang $sidang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sidang $sidang
     * @return \Illuminate\Http\Response
     */
    public function edit($nrp)
    {
        // $dosens = Dosen::where('status', '1')->get();
        // $konsentrasis = Konsentrasi::where('status', '1')->get();
        // $sidang = Sidang::where('nrp',$nrp)->first();

        // return view('mahasiswa.registrasidatamajusidang', compact('dosens', 'konsentrasis', 'sidang'));
    }

    public function editkk(Sidang $sidang)
    {
        if ($sidang->periode->konfirmasi == 'final' || $sidang->periode->konfirmasi == 'scheduler') {
            $ketuaPenguji = Dosen::where('posisi', 'like', '%Ketua%')->where('status', '1')->orderBy('nama', 'asc')->get();
            $sekretarisPenguji = Dosen::where('posisi', 'like', '%Sekretaris%')->where('status', '1')->orderBy('nama', 'asc')->get();
            $ruangans = Ruangan::all();
            $slots = UtilController::getAllSlots($sidang->periode->durasi);
            return view('paj.periode.penjadwalan.editkk', compact('sidang', 'ketuaPenguji', 'sekretarisPenguji', 'ruangans', 'slots'));
        } else {
            return redirect('paj/periode/penjadwalan')->withErrors('Tidak dapat melakukan edit kasus khusus karena jadwal belum selesai diatur oleh Scheduler!');
        }
    }

    public function editdatasidang(Sidang $sidang)
    {
        $dosens = Dosen::where('status', '1')->get();
        $konsentrasis = Konsentrasi::where('status', '1')->get();
        $dosenAktif = Dosen::where('status', '1')->get();
        return view('paj.periode.mahasiswa.editdatamahasiswa', compact('dosens', 'konsentrasis', 'dosenAktif', 'sidang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sidang  $sidang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sidang $sidang)
    {
        $sidangId = $request->get('sidangId');
        $validatedData = $request->validate(
            [
                'judul' => 'required|max:255',
                'konsentrasi' => 'required',
                'pembimbing1' => 'required',
                'pembimbing2' => 'required|different:pembimbing1',
            ],
            [
                'pembimbing2.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1',
            ]
        );
        $sidang = Sidang::find($sidangId);
        $sidang->judul = $request->get('judul');
        $sidang->konsentrasi_id = $request->get('konsentrasi');
        $sidang->pembimbing_1 = $request->get('pembimbing1');
        $sidang->pembimbing_2 = $request->get('pembimbing2');
        $sidang->validasi = 'mahasiswa';
        $sidang->save();

        return redirect()->route('mahasiswa.index');
    }

    public function updatedatasidang(Request $request, Sidang $sidang)
    {
        $validatedData = $request->validate(
            [
                'judul' => 'required|max:255',
                'konsentrasi' => 'required',
                'pembimbing1' => 'required',
                'pembimbing2' => 'required|different:pembimbing1',
            ],
            [
                'pembimbing2.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1',
            ]
        );
        $sidang->judul = $request->get('judul');
        $sidang->konsentrasi_id = $request->get('konsentrasi');
        $sidang->pembimbing_1 = $request->get('pembimbing1');
        $sidang->pembimbing_2 = $request->get('pembimbing2');
        $sidang->save();

        return redirect('paj/periode/mahasiswa')->with('status', 'Berhasil mengubah data sidang ' . $sidang->mahasiswa->nama);
    }



    public function updatekk(Request $request, Sidang $sidang)
    {
        $request->validate(['sekretaris' => 'different:ketua'], ['sekretaris.different' => 'Dosen penguji dua/sekretaris tidak boleh sama dengan dosen penguji satu/ketua.']);
        $sidang->tanggal = $request->get('date');
        $sidang->nomor_slot = $request->get('slot');
        $sidang->penguji_1 = $request->get('ketua');
        $sidang->penguji_2 = $request->get('sekretaris');
        $sidang->ruangan_id = $request->get('ruangan');
        $sidang->save();

        return redirect('paj/periode/penjadwalan')->with('status', 'Berhasil mengubah jadwal sidang ' . $sidang->mahasiswa->nama . ' sebagai Kasus Khusus!');
        ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sidang  $sidang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sidang $sidang)
    {
        //
    }

    public function hapusdatamajusidang(Sidang $sidang)
    {
        $nama = $sidang->mahasiswa->nama;
        try {
            $idMahasiswa = $sidang->mahasiswa->id;
            $sidang->delete();

            $mahasiswa = Mahasiswa::find($idMahasiswa);
            $idUser = $mahasiswa->user_id;
            $mahasiswa->delete();
            if ($idUser != null) {
                $user = User::find($idUser);
                $user->delete();
            }
            return redirect()->route('paj.periode.mahasiswa')->with('status', 'Berhasil menghapus Data Sidang mahasiswa ' . $nama . '!');
        } catch (\PDOException $e) {
            $msg = "Gagal menghapus Data Sidang mahasiswa " . $nama;

            return redirect()->route('paj.periode.mahasiswa')->with('error', $msg);
        }
    }

    public function daftarMahasiswaSidang()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if ($periodeAktif != null) {
            $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
            $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
            $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
            $mahasiswaSidang = Sidang::orderBy('validasi')->get();
            $countBelumValidasi = Sidang::where('validasi', 'belum')->get()->count();
            $dosenAktif = Dosen::where('status', '1')->get();

            return view('paj.periode.mahasiswa.daftarmahasiswa', compact('mahasiswaSidang', 'dosenAktif', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir', 'countBelumValidasi'));
        } else {
            return view('paj.periode.mahasiswa.daftarmahasiswa', compact('periodeAktif'));
        }
    }

    public function createDataMahasiswaSidang()
    {
        $periodeAktif = Periode::select('konfirmasi')->where('status', 'aktif')->first();
        if ($periodeAktif->konfirmasi == 'belum') {
            $dosens = Dosen::where('status', '1')->orderBy('nama')->get();
            $konsentrasis = Konsentrasi::where('status', '1')->orderBy('nama')->get();

            return view('paj.periode.mahasiswa.createdatamahasiswa', compact('dosens', 'konsentrasis'));
        } else {
            return redirect()->back();
        }
    }

    public function penjadwalanPAJ()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $mhsCount = Mahasiswa::select('id')->count();
        if ($mhsCount > 0) {
            $jmlhBelumValidasi = Sidang::where('validasi', 'belum')->orWhere('validasi', 'mahasiswa')->count();

            if ($jmlhBelumValidasi == 0) {
                $sidangTerjadwal = Sidang::where('tanggal', '!=', null)->where('nomor_slot', '!=', null)->orderBy('tanggal', 'asc')->orderBy('nomor_slot', 'asc')->get();
                $sidangBelumTerjadwal = Sidang::where('tanggal', null)->where('nomor_slot', null)->get();
                $sidangTidakAdaSlot = [];

                foreach ($sidangTerjadwal as $sT) {
                    $jmlhPenguji = DB::select(DB::raw("SELECT count(*) as jumlahpenguji from slot_jadwals sj inner join dosens d on sj.dosen_id = d.id inner join dosen_konsentrasi dk on dk.dosen_id = sj.dosen_id where nomor_slot='" . $sT->nomor_slot . "' and tanggal='" . $sT->tanggal . "' and dk.konsentrasi_id = '" . $sT->konsentrasi_id . "' and (d.posisi LIKE '%ketua%' and d.posisi LIKE '%sekretaris%') and sj.status='tersedia';"))[0];
                    $cekMengulang = DB::select(DB::raw("SELECT count(his.id) as 'jmlh' from history_sidangs his inner join history_mahasiswas him on his.history_mahasiswa_id=him.id where him.nrp='" . $sT->mahasiswa->nrp . "' and his.judul='" . $sT->judul . "';"))[0]->jmlh;
                    $sT['jumlah_penguji'] = $jmlhPenguji->jumlahpenguji;
                    if ($cekMengulang == 0) {
                        $sT['mengulang'] = 'no';
                    } else {
                        $sT['mengulang'] = 'yes';
                        if ($sT->penguji_1 != null && $sT->penguji_2 != null) {
                            $sT['jumlah_penguji'] = -1;
                        }
                    }
                    $sT['formatdate'] = Carbon::parse($sT->tanggal)->isoFormat('D MMMM Y');
                    $sT['slot'] = UtilController::getSlotJam($periodeAktif->durasi, $sT->nomor_slot);
                }

                foreach ($sidangBelumTerjadwal as $sBT) {
                    $slots = DB::select(DB::raw("SELECT sj1.tanggal, sj1.nomor_slot FROM slot_jadwals sj1 JOIN slot_jadwals sj2 ON sj1.tanggal = sj2.tanggal AND sj1.nomor_slot = sj2.nomor_slot WHERE sj1.dosen_id = " . $sBT['pembimbing_1'] . " AND sj2.dosen_id = " . $sBT['pembimbing_2'] . " AND sj1.status='tersedia' AND sj2.status='tersedia' ORDER BY tanggal, nomor_slot;"));
                    $sBT['slot'] = $slots;
                    foreach ($sBT['slot'] as $slot) {
                        $slot->formatdate = Carbon::parse($slot->tanggal)->isoFormat('D MMMM Y');
                        $slot->formattime = UtilController::getSlotJam($periodeAktif->durasi, $slot->nomor_slot);
                    }

                    $cekMengulang = DB::select(DB::raw("SELECT count(his.id) as 'jmlh' from history_sidangs his inner join history_mahasiswas him on his.history_mahasiswa_id=him.id where him.nrp='" . $sBT->mahasiswa->nrp . "' and his.judul='" . $sBT->judul . "';"))[0]->jmlh;
                    if ($cekMengulang == 0) {
                        $sBT['mengulang'] = 'no';
                    } else {
                        $sBT['mengulang'] = 'yes';
                    }

                    $sBT['jumlah_slot'] = count($slots);

                    if ($sBT['jumlah_slot'] == 0) {
                        $sidangTidakAdaSlot[] = $sBT;
                    }
                }

                foreach ($sidangBelumTerjadwal as $key => $value) {
                    if ($sidangBelumTerjadwal[$key]['jumlah_slot'] == 0) {
                        unset($sidangBelumTerjadwal[$key]);
                    }
                }

                $sidangBelumTerjadwal = $sidangBelumTerjadwal->sortBy('jumlah_slot');

                return view('paj.periode.penjadwalan.penjadwalan', compact('periodeAktif', 'sidangTerjadwal', 'sidangBelumTerjadwal', 'sidangTidakAdaSlot'));
            } else {
                $status = 'belumvalidasi';
                return view('paj.periode.penjadwalan.belumvalidasi', compact('periodeAktif', 'status'));
            }
        } else {
            $status = 'tidakadamahasiswa';
            return view('paj.periode.penjadwalan.belumvalidasi', compact('periodeAktif', 'status'));
        }
    }

    public function getRuanganAvailable(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $slot = $request->get('slot');

        $ruangans = DB::select(DB::raw("SELECT * from ruangans where id not in (select ruangan_id from sidangs where tanggal='" . $tanggal . "' and nomor_slot='" . $slot . "')"));
        $status = "";
        if ($ruangans == null) {
            $status = "no";
        } else {
            $status = "ok";
        }
        return response()->json(array('status' => $status, 'msg' => view('paj.periode.penjadwalan.optionruangan', compact('ruangans'))->render()), 200);
    }

    public function getRuanganAvailableKalab(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $slot = $request->get('slot');


        $ruangans = DB::select(DB::raw("SELECT * from ruangans where id not in (select ruangan_id from sidangs where tanggal='" . $tanggal . "' and nomor_slot='" . $slot . "')"));
        $status = "";
        if ($ruangans == null) {
            $status = "no";
        } else {
            $status = "ok";
        }
        return response()->json(array('status' => $status, 'msg' => view('dosen.scheduler.optionruangan', compact('ruangans'))->render()), 200);
    }

    public function setPenjadwalan1(Request $request)
    {
        $validatedData = $request->validate(
            [
                'sidangId' => 'required',
                'ruangan' => 'required',
                'slot' => 'required',
                'dosbing1' => 'required',
                'dosbing2' => 'required',
            ]
        );
        $idSidang = $request->get('sidangId');
        $slot = $request->get('slot');
        $tanggal = substr($slot, 0, 10);
        $nomor_slot = substr($slot, 11, 12);
        $ruangan = $request->get('ruangan');
        $idDosbing1 = $request->get('dosbing1');
        $idDosbing2 = $request->get('dosbing2');

        $slotDosbing1 = Jadwalkosong::where('dosen_id', $idDosbing1)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
        $slotDosbing1->status = 'terpakai';
        $slotDosbing1->save();

        $slotDosbing2 = Jadwalkosong::where('dosen_id', $idDosbing2)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
        $slotDosbing2->status = 'terpakai';
        $slotDosbing2->save();

        $dataSidang = Sidang::find($idSidang);
        $dataSidang->tanggal = $tanggal;
        $dataSidang->nomor_slot = $nomor_slot;
        $dataSidang->ruangan_id = $ruangan;
        $dataSidang->save();

        return redirect('paj/periode/penjadwalan')->with('status', 'Berhasil menyimpan jadwal sidang ' . $dataSidang->mahasiswa->nama . '!');
    }

    public function setPenjadwalan1Kalab(Request $request)
    {
        $validatedData = $request->validate(
            [
                'sidangId' => 'required',
                'ruangan' => 'required',
                'slot' => 'required',
                'dosbing1' => 'required',
                'dosbing2' => 'required',
            ]
        );
        $idSidang = $request->get('sidangId');
        $slot = $request->get('slot');
        $tanggal = substr($slot, 0, 10);
        $nomor_slot = substr($slot, 11, 12);
        $ruangan = $request->get('ruangan');
        $idDosbing1 = $request->get('dosbing1');
        $idDosbing2 = $request->get('dosbing2');

        $slotDosbing1 = Jadwalkosong::where('dosen_id', $idDosbing1)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
        $slotDosbing1->status = 'terpakai';
        $slotDosbing1->save();

        $slotDosbing2 = Jadwalkosong::where('dosen_id', $idDosbing2)->where('tanggal', $tanggal)->where('nomor_slot', $nomor_slot)->first();
        $slotDosbing2->status = 'terpakai';
        $slotDosbing2->save();

        $dataSidang = Sidang::find($idSidang);
        $dataSidang->tanggal = $tanggal;
        $dataSidang->nomor_slot = $nomor_slot;
        $dataSidang->ruangan_id = $ruangan;
        $dataSidang->save();

        return redirect('dosen/scheduler/penjadwalan')->with('status', 'Berhasil menyimpan jadwal sidang ' . $dataSidang->mahasiswa->nama . '!');
    }

    public function resetPenjadwalan1(Request $request)
    {
        $idSidang = $request->get('sidangId');
        $idDosbing1 = $request->get('dosbing1');
        $idDosbing2 = $request->get('dosbing2');

        $dataSidang = Sidang::find($idSidang);

        $slotDosbing1 = Jadwalkosong::where('dosen_id', $idDosbing1)->where('tanggal', $dataSidang->tanggal)->where('nomor_slot', $dataSidang->nomor_slot)->first();
        $slotDosbing1->status = 'tersedia';
        $slotDosbing1->save();

        $slotDosbing2 = Jadwalkosong::where('dosen_id', $idDosbing2)->where('tanggal', $dataSidang->tanggal)->where('nomor_slot', $dataSidang->nomor_slot)->first();
        $slotDosbing2->status = 'tersedia';
        $slotDosbing2->save();

        $dataSidang->tanggal = null;
        $dataSidang->nomor_slot = null;
        $dataSidang->ruangan_id = null;
        $dataSidang->save();

        return redirect('paj/periode/penjadwalan')->with('status', 'Berhasil mereset jadwal sidang ' . $dataSidang->mahasiswa->nama . '!');
        ;
    }

    public function updateDataRegistrasiMajuSidang(Request $request)
    {
        $sidangId = $request->get('sidangId');
        $validatedData = $request->validate(
            [
                'judul' => 'required|max:255',
                'konsentrasi' => 'required',
                'pembimbing1' => 'required',
                'pembimbing2' => 'required|different:pembimbing1',
            ],
            [
                'pembimbing2.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1',
            ]
        );
        $sidang = Sidang::find($sidangId);
        $sidang->judul = $request->get('judul');
        $sidang->konsentrasi_id = $request->get('konsentrasi');
        $sidang->pembimbing_1 = $request->get('pembimbing1');
        $sidang->pembimbing_2 = $request->get('pembimbing2');
        $sidang->validasi = 'mahasiswa';
        $sidang->save();

        return redirect()->route('mahasiswa.index');
    }
    public function penjadwalanKalab()
    {
        if (Auth::user()->ganti_password == 0) {
            return redirect()->route('dosen.dashboard');
        } else {
            if (str_contains(Auth::user()->dosen->posisi, 'Scheduler')) {
                $periodeAktif = Periode::where('status', 'aktif')->first();
                if ($periodeAktif != null) {
                    $sidangTerjadwalPenguji = Sidang::where('penguji_1', '!=', null)->where('penguji_2', '!=', null)->orderBy('tanggal', 'asc')->orderBy('nomor_slot', 'asc')->get();
                    $sidangBelumTerjadwalPenguji = Sidang::where('penguji_1', null)->where('penguji_2', null)->where('tanggal', '!=', null)->where('nomor_slot', '!=', null)->orderBy('tanggal', 'asc')->orderBy('nomor_slot', 'asc')->get();
                    $sidangReset = Sidang::where('tanggal', null)->where('nomor_slot', null)->get();
                    $sidangTidakAdaPenguji = [];

                    foreach ($sidangTerjadwalPenguji as $sTP) {
                        $cekMengulang = DB::select(DB::raw("SELECT count(his.id) as 'jmlh' from history_sidangs his inner join history_mahasiswas him on his.history_mahasiswa_id=him.id where him.nrp='" . $sTP->mahasiswa->nrp . "' and his.judul='" . $sTP->judul . "';"))[0]->jmlh;
                        if ($cekMengulang == 0) {
                            $sTP['mengulang'] = 'no';
                        } else {
                            $sTP['mengulang'] = 'yes';
                        }
                        $sTP['formatdate'] = Carbon::parse($sTP->tanggal)->isoFormat('D MMMM Y');
                        $sTP['slot'] = UtilController::getSlotJam($periodeAktif->durasi, $sTP->nomor_slot);
                    }

                    foreach ($sidangReset as $sR) {
                        $slots = DB::select(DB::raw("SELECT sj1.tanggal, sj1.nomor_slot
                    FROM slot_jadwals sj1
                    JOIN slot_jadwals sj2 ON sj1.tanggal = sj2.tanggal AND sj1.nomor_slot = sj2.nomor_slot
                    WHERE sj1.dosen_id = " . $sR['pembimbing_1'] . " AND sj2.dosen_id = " . $sR['pembimbing_2'] . " AND sj1.status='tersedia' AND sj2.status='tersedia' ORDER BY tanggal, nomor_slot;"));
                        $sR['slot'] = $slots;
                        foreach ($sR['slot'] as $slot) {
                            $slot->formatdate = Carbon::parse($slot->tanggal)->isoFormat('D MMMM Y');
                            $slot->formattime = UtilController::getSlotJam($periodeAktif->durasi, $slot->nomor_slot);
                        }
                        $cekMengulang = DB::select(DB::raw("SELECT count(his.id) as 'jmlh' from history_sidangs his inner join history_mahasiswas him on his.history_mahasiswa_id=him.id where him.nrp='" . $sR->mahasiswa->nrp . "' and his.judul='" . $sR->judul . "';"))[0]->jmlh;
                        if ($cekMengulang == 0) {
                            $sR['mengulang'] = 'no';
                        } else {
                            $sR['mengulang'] = 'yes';
                        }
                        $sR['jumlah_slot'] = count($slots);
                    }

                    foreach ($sidangBelumTerjadwalPenguji as $key => $sBTP) {
                        $ketuasKonsen = DB::select(DB::raw("SELECT sj.id as idSlotJadwal, sj.nomor_slot ,d.id, d.nama FROM slot_jadwals sj INNER JOIN dosens d on sj.dosen_id = d.id INNER JOIN dosen_konsentrasi dk on d.id = dk.dosen_id WHERE sj.tanggal='" . $sBTP->tanggal . "' AND sj.nomor_slot=" . $sBTP->nomor_slot . " AND sj.status= 'tersedia' AND d.posisi LIKE '%ketua%' AND dk.konsentrasi_id=" . $sBTP->konsentrasi_id . " AND d.id!=" . $sBTP->pembimbing_1 . " AND d.id!=" . $sBTP->pembimbing_2 . " ORDER BY d.nama asc;"));
                        $ketuasNonKonsen = DB::select(DB::raw("SELECT distinct(d.id), d.nama FROM slot_jadwals sj INNER JOIN dosens d on sj.dosen_id = d.id WHERE sj.tanggal='" . $sBTP->tanggal . "' AND sj.nomor_slot=" . $sBTP->nomor_slot . " AND sj.status= 'tersedia' AND d.posisi LIKE '%ketua%' AND d.id!=" . $sBTP->pembimbing_1 . " AND d.id!=" . $sBTP->pembimbing_2 . " AND d.id NOT IN(SELECT d.id FROM slot_jadwals sj INNER JOIN dosens d on sj.dosen_id = d.id INNER JOIN dosen_konsentrasi dk on d.id = dk.dosen_id WHERE sj.tanggal='" . $sBTP->tanggal . "' AND sj.nomor_slot=" . $sBTP->nomor_slot . " AND sj.status= 'tersedia' AND d.posisi LIKE '%ketua%' AND dk.konsentrasi_id=" . $sBTP->konsentrasi_id . " AND d.id!=" . $sBTP->pembimbing_1 . " AND d.id!=" . $sBTP->pembimbing_2 . ") ORDER BY d.nama asc;"));
                        $sekretarisKonsen = DB::select(DB::raw("SELECT sj.id as idSlotJadwal, sj.nomor_slot ,d.id, d.nama FROM slot_jadwals sj INNER JOIN dosens d on sj.dosen_id = d.id INNER JOIN dosen_konsentrasi dk on d.id = dk.dosen_id WHERE sj.tanggal='" . $sBTP->tanggal . "' AND sj.nomor_slot=" . $sBTP->nomor_slot . " AND sj.status= 'tersedia' AND d.posisi LIKE '%sekretaris%' AND dk.konsentrasi_id=" . $sBTP->konsentrasi_id . " AND d.id!=" . $sBTP->pembimbing_1 . " AND d.id!=" . $sBTP->pembimbing_2 . " ORDER BY d.nama asc;"));
                        $sekretarisNonKonsen = DB::select(DB::raw("SELECT distinct(d.id), d.nama FROM slot_jadwals sj INNER JOIN dosens d on sj.dosen_id = d.id INNER JOIN dosen_konsentrasi dk on d.id = dk.dosen_id WHERE sj.tanggal='" . $sBTP->tanggal . "' AND sj.nomor_slot=" . $sBTP->nomor_slot . " AND sj.status= 'tersedia' AND d.posisi LIKE '%sekretaris%' AND d.id!=" . $sBTP->pembimbing_1 . " AND d.id!=" . $sBTP->pembimbing_2 . " AND dk.konsentrasi_id!=" . $sBTP->konsentrasi_id . " AND d.id NOT IN(SELECT d.id FROM slot_jadwals sj INNER JOIN dosens d on sj.dosen_id = d.id INNER JOIN dosen_konsentrasi dk on d.id = dk.dosen_id WHERE sj.tanggal='" . $sBTP->tanggal . "' AND sj.nomor_slot=" . $sBTP->nomor_slot . " AND sj.status= 'tersedia' AND d.posisi LIKE '%sekretaris%' AND dk.konsentrasi_id=" . $sBTP->konsentrasi_id . " AND d.id!=" . $sBTP->pembimbing_1 . " AND d.id!=" . $sBTP->pembimbing_2) . ") ORDER BY d.nama asc;");
                        $ketuas = array_merge($ketuasKonsen, $ketuasNonKonsen);
                        foreach ($ketuas as $ketua) {
                            //$data = DB::select(DB::raw("SELECT COUNT(s1.id) AS ketua, COUNT(s2.id) AS sekretaris FROM dosens d1 LEFT JOIN sidangs s1 ON d1.id = s1.penguji_1 AND s1.penguji_1 = " . $ketua->id . " LEFT JOIN sidangs s2 ON d1.id = s2.penguji_2 AND s2.penguji_2 = " . $ketua->id . ";"))[0];
                            $dataKetua = DB::select(DB::raw("SELECT count(penguji_1) as 'ketua' from sidangs where penguji_1='" . $ketua->id . "'"))[0]->ketua;
                            $dataSekre = DB::select(DB::raw("SELECT count(penguji_2) as 'sekretaris' from sidangs where penguji_2='" . $ketua->id . "'"))[0]->sekretaris;
                            $ketua->jmlh_ketua = $dataKetua;
                            $ketua->jmlh_sekretaris = $dataSekre;
                        }

                        $sBTP['ketuas'] = $ketuas;
                        $sekretaris = array_merge($sekretarisKonsen, $sekretarisNonKonsen);
                        foreach ($sekretaris as $sekret) {
                            //$data = DB::select(DB::raw("SELECT COUNT(s1.id) AS ketua, COUNT(s2.id) AS sekretaris FROM dosens d1 LEFT JOIN sidangs s1 ON d1.id = s1.penguji_1 AND s1.penguji_1 = " . $sekret->id . " LEFT JOIN sidangs s2 ON d1.id = s2.penguji_2 AND s2.penguji_2 = " . $sekret->id . ";"))[0];
                            $dataKetua = DB::select(DB::raw("SELECT count(penguji_1) as 'ketua' from sidangs where penguji_1='" . $sekret->id . "'"))[0]->ketua;
                            $dataSekre = DB::select(DB::raw("SELECT count(penguji_2) as 'sekretaris' from sidangs where penguji_2='" . $sekret->id . "'"))[0]->sekretaris;
                            $sekret->jmlh_ketua = $dataKetua;
                            $sekret->jmlh_sekretaris = $dataSekre;
                        }

                        if (count($ketuas) == 1 && count($sekretaris) == 1) {
                            $sekretaris = array();
                        }

                        $sBTP['sekretaris'] = $sekretaris;
                        $sBTP['formatdate'] = Carbon::parse($sBTP->tanggal)->isoFormat('D MMMM Y');
                        $sBTP['slot'] = UtilController::getSlotJam($periodeAktif->durasi, $sBTP->nomor_slot);

                        $cekMengulang = DB::select(DB::raw("SELECT count(his.id) as 'jmlh' from history_sidangs his inner join history_mahasiswas him on his.history_mahasiswa_id=him.id where him.nrp='" . $sBTP->mahasiswa->nrp . "' and his.judul='" . $sBTP->judul . "';"))[0]->jmlh;
                        if ($cekMengulang == 0) {
                            $sBTP['mengulang'] = 'no';
                        } else {
                            $sBTP['mengulang'] = 'yes';
                        }

                        if (count($sBTP['ketuas']) == 0 || count($sBTP['sekretaris']) == 0) {
                            $sidangTidakAdaPenguji[] = $sBTP;
                            unset($sidangBelumTerjadwalPenguji[$key]);
                        }
                    }

                    return view('dosen.scheduler.penjadwalan', compact('periodeAktif', 'sidangTerjadwalPenguji', 'sidangBelumTerjadwalPenguji', 'sidangReset', 'sidangTidakAdaPenguji'));
                } else {
                    return view('dosen.scheduler.tidakadasidang');
                }
            } else {
                return redirect()->back();
            }
        }

    }

    public function setPenjadwalan2(Request $request)
    {
        if (str_contains(Auth::user()->dosen->posisi, 'Scheduler')) {
            $validatedData = $request->validate(
                [
                    'sidangId' => 'required',
                    'penguji1' => 'required|different:penguji2',
                    'penguji2' => 'required',
                ],
                [
                    'penguji1.different' => 'Penguji 2 harus berbeda dengan Penguji 1',
                ]
            );
            $idSidang = $request->get('sidangId');
            $idPenguji1 = $request->get('penguji1');
            $idPenguji2 = $request->get('penguji2');

            $sidang = Sidang::find($idSidang);

            $slotPenguji1 = Jadwalkosong::where('dosen_id', $idPenguji1)->where('tanggal', $sidang->tanggal)->where('nomor_slot', $sidang->nomor_slot)->first();
            $slotPenguji1->status = 'terpakai';
            $slotPenguji1->save();

            $slotPenguji2 = Jadwalkosong::where('dosen_id', $idPenguji2)->where('tanggal', $sidang->tanggal)->where('nomor_slot', $sidang->nomor_slot)->first();
            $slotPenguji2->status = 'terpakai';
            $slotPenguji2->save();

            $sidang->penguji_1 = $idPenguji1;
            $sidang->penguji_2 = $idPenguji2;
            $sidang->save();

            return redirect('dosen/scheduler/penjadwalan')->with('status', 'Berhasil mengatur dosen penguji sidang ' . $sidang->mahasiswa->nama . '!');
        } else {
            return redirect()->back();
        }
    }

    public function resetPenjadwalan2(Request $request)
    {
        if (str_contains(Auth::user()->dosen->posisi, 'Scheduler')) {
            $idSidang = $request->get('sidangId');
            $idPenguji1 = $request->get('penguji1');
            $idPenguji2 = $request->get('penguji2');

            $dataSidang = Sidang::find($idSidang);

            $slotPenguji1 = Jadwalkosong::where('dosen_id', $idPenguji1)->where('tanggal', $dataSidang->tanggal)->where('nomor_slot', $dataSidang->nomor_slot)->first();
            $slotPenguji1->status = 'tersedia';
            $slotPenguji1->save();

            $slotPenguji2 = Jadwalkosong::where('dosen_id', $idPenguji2)->where('tanggal', $dataSidang->tanggal)->where('nomor_slot', $dataSidang->nomor_slot)->first();
            $slotPenguji2->status = 'tersedia';
            $slotPenguji2->save();

            $dataSidang->penguji_1 = null;
            $dataSidang->penguji_2 = null;
            $dataSidang->save();

            return redirect('dosen/scheduler/penjadwalan')->with('status', 'Berhasil mereset jadwal penguji sidang ' . $dataSidang->mahasiswa->nama . '!');
        } else {
            return redirect()->back();
        }
    }

    public function resetPenjadwalan1Kalab(Request $request)
    {
        $idSidang = $request->get('sidangIdReset');
        $idDosbing1 = $request->get('dosbing1Reset');
        $idDosbing2 = $request->get('dosbing2Reset');

        $dataSidang = Sidang::find($idSidang);

        $slotDosbing1 = Jadwalkosong::where('dosen_id', $idDosbing1)->where('tanggal', $dataSidang->tanggal)->where('nomor_slot', $dataSidang->nomor_slot)->first();
        $slotDosbing1->status = 'tersedia';
        $slotDosbing1->save();

        $slotDosbing2 = Jadwalkosong::where('dosen_id', $idDosbing2)->where('tanggal', $dataSidang->tanggal)->where('nomor_slot', $dataSidang->nomor_slot)->first();
        $slotDosbing2->status = 'tersedia';
        $slotDosbing2->save();

        $dataSidang->tanggal = null;
        $dataSidang->nomor_slot = null;
        $dataSidang->ruangan_id = null;
        $dataSidang->save();

        return redirect('dosen/scheduler/penjadwalan')->with('status', 'Berhasil mereset jadwal sidang ' . $dataSidang->mahasiswa->nama . '!');
    }

    public function rekapPenguji()
    {
        if (Auth::user()->ganti_password == 0) {
            return redirect()->route('dosen.dashboard');
        } else {
            $periodeAktif = Periode::where('status', 'aktif')->first();
            if ($periodeAktif != null) {
                $bulan = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('MMMM Y');
                $tanggalMulai = Carbon::parse($periodeAktif->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
                $tanggalBerakhir = Carbon::parse($periodeAktif->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');
                $dosens = Dosen::all();
                foreach ($dosens as $dosen) {
                    $dosen->jmlh_ketua = DB::select(DB::raw("SELECT count(penguji_1) as 'ketua' from sidangs where penguji_1='" . $dosen->id . "'"))[0]->ketua;
                    $dosen->jmlh_sekretaris = DB::select(DB::raw("SELECT count(penguji_2) as 'sekretaris' from sidangs where penguji_2='" . $dosen->id . "'"))[0]->sekretaris;
                    $dosen->total_penguji = ($dosen->jmlh_ketua * 1 + $dosen->jmlh_sekretaris * 1);
                }
                return view('dosen.scheduler.rekappenguji', compact('dosens', 'periodeAktif', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
            } else {
                return view('dosen.scheduler.tidakadasidang');
            }
        }

    }
}