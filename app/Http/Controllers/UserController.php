<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        $userPAJ = User::find(Auth::user()->id);
        $userPAJ->password = Hash::make($request->get('passbaru'));
        $userPAJ->save();

        return redirect()->route('paj.resetpass')->with('status', 'Password berhasil diupdate');
    }

    public function validasiEmailUser(Request $request)
    {
        $kodeOTP = random_int(1000, 9999);

        date_default_timezone_set("Asia/Jakarta");
        $request->validate(['email' => 'required|email:dns|ends_with:ubaya.ac.id'], ['email.required' => 'Email tidak boleh kosong!', 'email.ends_with' => 'Email harus berupa email UBAYA!']);

        $user = User::where('email', $request->get('email'))->first();

        $nama = "";
        if ($user != null) {
            if ($user->role == "admin") {
                $nama = "if-paj";
            } else if ($user->role == "dosen") {
                $nama = $user->dosen->nama;
            } else {
                $nama = $user->mahasiswa->nama;
            }
            MailController::mailOTPForgotPassword($request->get('email'), $nama, $kodeOTP);
            session(['kodeOTPForgotPassword' => $kodeOTP, 'idUser' => $user->id]);
            $message = "Kode OTP telah dikirimkan pada email Anda!";
            return view('otpforgotpassword', compact("message"));
        } else {
            return redirect()->route('forgotpassword')->with('status', 'Mohon pastikan email Anda terdaftar pada Sistem!');
        }
    }

    public function validasiOTPForgotPassword(Request $request)
    {
        $validatedData = $request->validate(
            [
                'otp1' => 'required|numeric',
                'otp2' => 'required|numeric',
                'otp3' => 'required|numeric',
                'otp4' => 'required|numeric',
            ],
            [
                'otp1.required' => 'OTP1 tidak boleh kosong!',
                'otp2.required' => 'OTP2 tidak boleh kosong!',
                'otp3.required' => 'OTP3 tidak boleh kosong!',
                'otp4.required' => 'OTP4 tidak boleh kosong!',
                'otp1.numeric' => 'OTP1 harus dalam bentuk angka!',
                'otp2.numeric' => 'OTP2 harus dalam bentuk angka!',
                'otp3.numeric' => 'OTP3 harus dalam bentuk angka!',
                'otp4.numeric' => 'OTP4 harus dalam bentuk angka!',
            ]
        );
        $kode1 = $request->get('otp1');
        $kode2 = $request->get('otp2');
        $kode3 = $request->get('otp3');
        $kode4 = $request->get('otp4');
        $inputKodeOTP = $kode1 . $kode2 . $kode3 . $kode4;

        $kodeOTP = "" . session('kodeOTPForgotPassword') . "";


        if ($kodeOTP == $inputKodeOTP) {
            return view('newpassword');
        } else {
            $message = "Kode OTP yang anda masukkan salah, coba lagi!";
            return view('otpforgotpassword', compact('message'));
        }
    }

    public function changeNewPassword(Request $request)
    {
        $password = $request->get('password');
        $konfirmasiPassword = $request->get('konfirmasipassword');
        if ($password == $konfirmasiPassword) {
            $id = session('idUser');
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->save();
            session()->forget("kodeOTPForgotPassword");
            session()->forget("idUser");

            return redirect("/login")->with('status', 'Berhasil mengubah password!');
        }else{
            $message = "Konfirmasi password harus sama dengan password";
            return view('newpassword', compact('message'));
        }

    }
}