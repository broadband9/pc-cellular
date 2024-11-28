<?php
namespace App\Mail;

use App\Models\Repair;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RepairAwaitingParts extends Mailable
{
    use Queueable, SerializesModels;

    public $repair;

    public function __construct(Repair $repair)
    {
        $this->repair = $repair;
    }

    public function build()
    {
        return $this->subject('Repair Status: Awaiting Parts')
                    ->view('emails.repair_awaiting_parts')
                    ->with([
                        'repair' => $this->repair,
                    ]);
    }
}