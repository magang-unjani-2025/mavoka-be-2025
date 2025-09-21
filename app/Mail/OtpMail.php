<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $nama;

    public function __construct($otp, $nama = null)
    {
        $this->otp = $otp;
        // Pastikan selalu ada nama default agar template tidak kosong
        $this->nama = $nama ?? 'Pengguna';
    }

    public function build()
    {
        return $this->subject('Kode OTP Verifikasi')
                    ->view('emails.otp')
                    ->with([
                        'otp' => $this->otp,
                        'nama' => $this->nama
                    ]);
    }
}
