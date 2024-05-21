<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function mailPassDosen($email, $namaDosen, $passDosen){
        $details = ['dosen'=>$namaDosen, 'pass'=>$passDosen];

        Mail::to($email)->send(new SendMail('passdosen', $details));
    }

    public static function mailOTPMahasiswa($email, $namaMahasiswa, $kodeOTP){
        $details = ['mahasiswa'=>$namaMahasiswa, 'kodeOTP'=>$kodeOTP];

        Mail::to($email)->send(new SendMail('otpregister', $details));
    }

    public static function mailOTPForgotPassword($email, $namaUser, $kodeOTP){
        $details = ['user'=>$namaUser, 'kodeOTP'=>$kodeOTP];

        Mail::to($email)->send(new SendMail('otpforgot', $details));
    }

    public static function mailJadwalDosen($email, $namaDosen, $bodyEmail, $fileName){
        $details = ['dosen'=>$namaDosen, 'body'=>$bodyEmail];

        Mail::to($email)->send(new SendMail('jadwaldosen', $details, $fileName));
    }
    public static function mailJadwalMahasiwa($email, $namaMahasiswa, $bodyEmail, $fileName){
        $details = ['mahasiswa'=>$namaMahasiswa, 'body'=>$bodyEmail];

        Mail::to($email)->send(new SendMail('jadwalmhs', $details, $fileName));
    }
}
