<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RenewalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $details) {}

    public function build(): self
    {
        return $this->subject('Madurai District Chess Club — Membership renewed')
            ->view('emails.renewal');
    }
}
