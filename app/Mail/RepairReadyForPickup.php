<?php
namespace App\Mail;

use App\Models\Repair;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RepairReadyForPickup extends Mailable
{
    use Queueable, SerializesModels;

    public $repair;

    public function __construct(Repair $repair)
    {
        $this->repair = $repair;
    }

    public function build()
    {
        return $this->subject('Your Repair is Ready for Pickup')
                    ->view('emails.repair_ready_for_pickup')
                    ->with([
                        'repair' => $this->repair,
                    ]);
    }
}