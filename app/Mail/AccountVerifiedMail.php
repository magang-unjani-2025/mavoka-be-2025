<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;

    public function __construct($nama)
    {
        $this->nama = $nama;
    }

    public function build()
    {
        return $this->subject('Akun Anda Telah Diverifikasi')
                    ->view('emails.account_verified')
                    ->with([
                        'nama' => $this->nama
                    ]);
    }
}
