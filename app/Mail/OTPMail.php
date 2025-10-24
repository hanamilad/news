<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $purpose; // e.g. "Verify your email" or "Reset password"

    public function __construct(string $code, string $purpose = 'Verification')
    {
        $this->code = $code;
        $this->purpose = $purpose;
    }

    public function build()
    {
        return $this->subject("{$this->purpose} Code")
            ->view('emails.otp') // create a simple blade view resources/views/emails/otp.blade.php
            ->with([
                'code' => $this->code,
                'purpose' => $this->purpose,
            ]);
    }
}
