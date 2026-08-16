<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp, $userName = 'Quý khách')
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mã OTP xác thực đặt lại mật khẩu - Chill Chill Coffee')
                    ->view('emails.reset_password_otp');
    }
}
