<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomRepairEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $repair;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair)
    {
        $this->repair = $repair;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Repair Notification')
            ->view('emails.custom_repair_email')
            ->with([
                'repair' => $this->repair,
            ]);
    }
}