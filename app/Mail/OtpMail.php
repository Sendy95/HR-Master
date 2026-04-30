<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $companyName;
    public $lang;

    public function __construct($otp, $user, $lang = 'id')
    {
        $this->otp = $otp;
        $this->lang = $lang;

        /** * Sekarang $user->company_name sudah berisi "CIS" atau "ASL" 
         * karena hasil JOIN di controller tadi.
         */
        $this->companyName = !empty($user->company_name) ? $user->company_name : 'Deparment';
    }

    public function envelope(): Envelope
    {
        // Nama pengirim akan menjadi "HR - CIS" atau "HR - ASL"
        $dynamicFromName = "HR - " . $this->companyName;

        $subject = ($this->lang === 'en') 
            ? "[{$this->companyName} Employee DU] - OTP Verification Code"
            : "[PDM Karyawan {$this->companyName}] - Kode Verifikasi OTP";

        return new Envelope(
            from: new Address(config('mail.from.address'), $dynamicFromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp'         => $this->otp,
                'companyName' => $this->companyName,
                'lang'        => $this->lang,
            ],
        );
    }
}