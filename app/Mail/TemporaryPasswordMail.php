<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemporaryPasswordMail extends Mailable
{
    use SerializesModels;

    public $memberName;
    public $email;
    public $password;

    public function __construct($memberName, $email, $password)
    {
        $this->memberName = $memberName;
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this->view('emails.temporary-password')
                    ->subject('Welcome to Our Church - Your Login Details');
    }
}