<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $type;
    public $details;
    public $fileName;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($type, $details, $fileName='')
    {
        $this->type = $type;
        $this->details = $details;
        $this->fileName = $fileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->type == 'passdosen') {
            return $this->subject('Password Sementara Dosen (IF-Sidang TA)')->view('emails.passdosen');
        } else if ($this->type == 'otpregister') {
            return $this->subject('OTP Registrasi (IF-Sidang TA)')->view('emails.otpregister');
        } else if ($this->type == 'otpforgot') {
            return $this->subject('OTP Lupa Password (IF-Sidang TA)')->view('emails.otpforgot');
        } else if ($this->type == 'jadwaldosen') {
            return $this->subject('Jadwal Sidang TA Dosen (IF-Sidang TA)')->attach($this->fileName)->view('emails.jadwaldosen');
        } else if ($this->type == 'jadwalmhs') {
            return $this->subject('Jadwal Sidang TA (IF-Sidang TA)')->attach($this->fileName)->view('emails.jadwalmhs');
        }
    }
}
