<?php

namespace App\Mail;

use App\Models\Repair;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $repair;
    public $customMessage;

    public function __construct(Repair $repair)
    {
        $this->repair = $repair;
    }

    public function build()
    {
        return $this->subject('Repair Created: ' . $this->repair->repair_number)
                    ->markdown('emails.welcome_email')
                    ->with(['customMessage' => $this->customMessage]);
    }

    public function with($customMessage)
    {
        $this->customMessage = $customMessage;
        return $this;
    }
}