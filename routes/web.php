<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\HistoryMahasiswaController;
use App\Http\Controllers\HistorySidangController;
use App\Http\Controllers\JadwalkosongController;
use App\Http\Controllers\KonsentrasiController;
use App\Http\Controllers\LoginGooayaController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SidangController;
use App\Http\Controllers\TanggalMerahController;
use App\Http\Controllers\UserController;
use App\Models\HistorySidang;
use App\Models\Konsentrasi;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\TanggalMerah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('/login');
    } else {
        if (Auth::user()->role === 'admin') {
            return redirect('/paj');
        } else if (Auth::user()->role === 'dosen') {
            return redirect('/dosen');
        } else if (Auth::user()->role === 'mahasiswa') {
            Auth::logout();
            return redirect('/login');
        }
    }
});

Auth::routes(['register'=>false, 'reset'=>false, 'verify'=>false]);

Route::post('login-custom', [LoginGooayaController::class, 'logincustom'])->name('login-custom');

Route::middleware(['auth', 'admin'])->group(function () {
    // PAJ history periode dan mahasiswa
    Route::get('/paj/history/periode/{periode}', [PeriodeController::class, 'detailPeriode'])->name('periode.history');
    Route::get('/paj/history/periode/{periode}/mahasiswa', [HistoryMahasiswaController::class, 'index'])->name('periode.history.mahasiswa');

    // PAJ cari mahasiswa
    Route::get('paj/carimahasiswa', function () {
        return view('paj.carimahasiswa');
    });
    Route::post('paj/carimahasiswa/search', [MahasiswaController::class, 'daftarSeluruhMahasiswa'])->name('paj.carimahasiswa.post');

    // PAJ konsentrasi aktif nonaktif
    Route::post('paj/konsentrasi/nonaktifkan', [KonsentrasiController::class, 'nonaktifkan'])->name('konsentrasi.nonaktifkan');
    Route::post('paj/konsentrasi/aktifkan', [KonsentrasiController::class, 'aktifkan'])->name('konsentrasi.aktifkan');
    Route::resource('paj/konsentrasi', KonsentrasiController::class);

    // PAJ ruangan aktif nonaktif
    Route::post('paj/ruangan/nonaktifkan', [RuanganController::class, 'nonaktifkan'])->name('ruangan.nonaktifkan');
    Route::post('paj/ruangan/aktifkan', [RuanganController::class, 'aktifkan'])->name('ruangan.aktifkan');

    // PAJ dosen aktif nonaktif
    Route::post('paj/dosen/nonaktifkan', [DosenController::class, 'nonaktifkan'])->name('dosen.nonaktifkan');
    Route::post('paj/dosen/aktifkan', [DosenController::class, 'aktifkan'])->name('dosen.aktifkan');

    Route::resource('paj/ruangan', RuanganController::class);
    Route::resource('/paj/dosen', DosenController::class);

    // Route::get('paj/resetpass', function () {
    //     return view('paj.resetpassword');
    // })->name('paj.resetpass');
    // Route::post('paj/resetpassword', [UserController::class, 'resetPassword'])->name('paj.resetpassword');

    //Ini sementara jangan dihapus.
    Route::get('/paj', [PeriodeController::class, 'index'])->name('paj.dashboard');

    //PAJ Periode
    Route::get('paj/createperiode', [PeriodeController::class, 'create'])->name('periode.create');
    Route::post('paj/createperiode', [PeriodeController::class, 'store'])->name('periode.store');
    Route::get('paj/editperiode/{periode}', [PeriodeController::class, 'edit'])->name('periode.edit');
    Route::put('paj/editperiode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
    Route::post('paj/nonaktifkanperiode', [PeriodeController::class, 'nonaktifkan'])->name('periode.nonaktifkan');

    // PAJ Detail periode
    Route::get('paj/periode', [PeriodeController::class, 'showJadwalSidangPAJ']);

    //PAJ periode mahasiswa
    Route::get('paj/periode/mahasiswa', [SidangController::class, 'daftarMahasiswaSidang'])->name('paj.periode.mahasiswa');
    Route::post('paj/periode/mahasiswa/tambah/registercsv', [MahasiswaController::class, 'registerCSV'])->name('paj.periode.registercsv');
    Route::get('paj/periode/mahasiswa/tambah', [SidangController::class, 'createDataMahasiswaSidang'])->name('paj.periode.mahasiswa.create');
    Route::post('paj/periode/mahasiswa/validasidata', [MahasiswaController::class, 'validasiDataSidang'])->name('paj.periode.mahasiswa.validasiData');
    Route::post('paj/periode/mahasiswa/create', [SidangController::class, 'store'])->name('paj.periode.mahasiswa.store');
    Route::get('paj/periode/mahasiswa/editdatamajusidang/{sidang}', [SidangController::class, 'editdatasidang'])->name('paj.periode.mahasiswa.editdatasidang');
    Route::post('paj/periode/mahasiswa/updatedatamajusidang/{sidang}', [SidangController::class, 'updatedatasidang'])->name('paj.periode.mahasiswa.updatedatasidang');
    Route::post('paj/periode/mahasiswa/hapusdatamajusidang/{sidang}', [SidangController::class, 'hapusdatamajusidang'])->name('paj.periode.mahasiswa.hapusdatamajusidang');
    Route::resource('/paj/periode/sidang', SidangController::class);

    //PAJ jadwal kosong dosen
    Route::post('paj/periode/jadwalkosong/search', [JadwalkosongController::class, 'getAllJadwalDosenBaruPost'])->name('jadwalkosong.paj');
    Route::get('paj/periode/jadwalkosong', [JadwalkosongController::class, 'getAllJadwalDosen'])->name('jadwalkosong.paj.get');
    Route::post('paj/periode/jadwalkosong/edit', [JadwalkosongController::class, 'editPaj'])->name('jadwalkosong.paj.edit');
    Route::post('paj/periode/jadwalkosong/store', [JadwalkosongController::class, 'storefrompaj'])->name('jadwalkosong.paj.store');

    //PAJ penjadwalan pembimbing
    Route::get('paj/periode/penjadwalan', [SidangController::class, 'penjadwalanPAJ'])->name('sidang.penjadwalanpaj');
    Route::post('paj/periode/penjadwalan/getRuanganAvailable', [SidangController::class, 'getRuanganAvailable'])->name('sidang.getRuanganAvailable');
    Route::post('paj/periode/penjadwalan/setPenjadwalan1', [SidangController::class, 'setPenjadwalan1'])->name('sidang.setPenjadwalan1');
    Route::post('paj/periode/penjadwalan/resetPenjadwalan1', [SidangController::class, 'resetPenjadwalan1'])->name('sidang.resetPenjadwalan1');
    Route::get('paj/periode/penjadwalan/editkk/{sidang}', [SidangController::class, 'editkk'])->name('sidang.editKK');
    Route::put('paj/periode/penjadwalan/updatekk/{sidang}', [SidangController::class, 'updatekk'])->name('sidang.updatekk');

    //PAJ konfirmasi jadwal
    Route::post('paj/periode/pajkonfirmasi', [PeriodeController::class, 'pajKonfirmasi'])->name('periode.pajkonfirmasi');
    Route::post('paj/periode/pajfinalisasijadwal', [PeriodeController::class, 'pajFinalisasiJadwal'])->name('periode.pajfinalisasijadwal');

    //PAJ detail periode
    Route::get('paj/detailperiode/{periode}', [PeriodeController::class, 'detailPeriode'])->name('periode.detailPeriode');

    //PAJ kirim jadwal
    Route::get('paj/periode/kirimjadwal', [PeriodeController::class, 'kirimJadwal'])->name('periode.kirimjadwal');
    Route::post('paj/periode/kirimjadwaldosen', [PeriodeController::class, 'kirimJadwalDosen'])->name('periode.kirimjadwaldosen');
    Route::post('paj/periode/kirimjadwalmahasiswa', [PeriodeController::class, 'kirimJadwalMahasiswa'])->name('periode.kirimjadwalmahasiswa');
    Route::post('paj/periode/downloadjadwaldosen', [PeriodeController::class, 'downloadJadwal'])->name('periode.downloadjadwaldosen');
    Route::post('paj/periode/downloadjadwalmahasiswa', [PeriodeController::class, 'downloadJadwalMahasiswa'])->name('periode.downloadjadwalmahasiswa');

    Route::post('paj/periode/tanggalmerahhapus', [TanggalMerahController::class, 'hapusTanggalMerah'])->name('tanggalmerah.hapus');
    Route::resource('/paj/periode/tanggalmerah', TanggalMerah::class);
});
Route::middleware(['auth', 'dosen'])->group(function () {
    //Dosen
    Route::get('/dosen', [DosenController::class, 'dosenDashboard'])->name('dosen.dashboard');
    Route::get('/dosen/jadwalsidang', [DosenController::class, 'jadwalSidang'])->name('dosen.jadwalSidang');

    //jadwal kosong dosen
    Route::post('dosen/jadwalkosong/store', [JadwalkosongController::class, 'store'])->name('jadwalkosong.store');
    Route::get('dosen/jadwalkosong/{dosen}/edit', [JadwalkosongController::class, 'edit'])->name('jadwalkosong.edit');
    Route::get('dosen/jadwalkosong', [JadwalkosongController::class, 'getJadwalDosen'])->name('dosen.jadwalkosong.index');

    // Reset password dosen
    // Route::get('dosen/resetpass', [DosenController::class, 'resetPassPage'])->name('dosen.resetpass');
    // Route::post('dosen/resetpassword', [DosenController::class, 'resetPassword'])->name('dosen.resetpassword');

    //penjadwalan penguji oleh scheduler
    Route::get('dosen/scheduler/penjadwalan', [SidangController::class, 'penjadwalanKalab'])->name('sidang.penjadwalankalab');
    Route::get('dosen/scheduler/rekapPenguji', [SidangController::class, 'rekapPenguji'])->name('sidang.rekappenguji');
    Route::post('dosen/scheduler/penjadwalan/setPenjadwalan2', [SidangController::class, 'setPenjadwalan2'])->name('sidang.setPenjadwalan2');
    Route::post('dosen/scheduler/penjadwalan/resetPenjadwalan2', [SidangController::class, 'resetPenjadwalan2'])->name('sidang.resetPenjadwalan2');
    Route::post('dosen/scheduler/penjadwalan/resetPenjadwalan1Kalab', [SidangController::class, 'resetPenjadwalan1Kalab'])->name('sidang.resetPenjadwalan1Kalab');
    Route::post('dosen/scheduler/penjadwalan/getRuanganAvailableKalab', [SidangController::class, 'getRuanganAvailableKalab'])->name('sidang.getRuanganAvailableKalab');
    Route::post('dosen/scheduler/penjadwalan/setPenjadwalan1Kalab', [SidangController::class, 'setPenjadwalan1Kalab'])->name('sidang.setPenjadwalan1Kalab');
    Route::post('dosen/scheduler/penjadwalan/pajkonfirmasi', [PeriodeController::class, 'kalabKonfirmasi'])->name('periode.kalabKonfirmasi');
});

// Route::view('errorregister', 'mahasiswa.errorregister');
// Route::get('register/otp', function () {
//     return redirect('/login');
// });
// Route::post('register/otp', [MahasiswaController::class, 'validasiRegistrasi'])->name('mahasiswa.register.otp');
// Route::get('register/otpconfirm', function () {
//     return redirect('/login');
// });
// Route::get('/register', function () {
//     return view('auth.register');
// })->name('auth.register');
// Route::post('register/otpconfirm', [MahasiswaController::class, 'store'])->name('mahasiswa.otpconfirm');


// Route::get('forgotpassword', function () {
//     if (Auth::user() != null) {
//         return redirect('/');
//     } else {
//         return view('sendemailforgotpassword');
//     }
// })->name('forgotpassword');

// Route::get('newpassword', function () {
//     if (session('idUser') != null) {
//         return view('newpassword');
//     } else {
//         return redirect('login');
//     }
// })->name('newpassword');

// Route::get('forgotpassword/otp', function () {
//     return redirect('/login');
// });
// Route::get('forgotpassword/otpconfirm', function () {
//     return redirect('/login');
// });
// Route::get('forgotpassword/newpassword', function () {
//     return redirect('/login');
// });
// Route::post('forgotpassword/otp', [UserController::class, 'validasiEmailUser'])->name('forgotpassword.validasiemailuser');
// Route::post('forgotpassword/otpconfirm', [UserController::class, 'validasiOTPForgotPassword'])->name('forgotpassword.validasiotpforgotpassword');
// Route::post('forgotpassword/newpassword', [UserController::class, 'changeNewPassword'])->name('forgotpassword.changenewpassword');

// Route::middleware(['auth', 'mahasiswa'])->group(function () {
//     // Mahasiswa
//     Route::get('mahasiswa/jadwalsidang', [MahasiswaController::class, 'JadwalSidang'])->name('mahasiswa.jadwalsidang');
//     Route::get('mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');

//     //Ganti password
//     Route::get('mahasiswa/gantipassword', function () {
//         return view('mahasiswa.gantipassword');
//     })->name('mahasiswa.gantipassword');
//     Route::post('mahasiswa/gantipassword', [MahasiswaController::class, 'gantipassword'])->name('mahasiswa.gantipassword');
// });