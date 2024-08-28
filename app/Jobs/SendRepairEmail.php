<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CustomRepairEmail;
use App\Models\Customer;

class SendRepairEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $repair;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($repair)
    {
        $this->repair = $repair;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
{
    Log::info('Handling SendRepairEmail job', ['repair_id' => $this->repair->id]);

    $customer = Customer::find($this->repair->customer_id);

    if ($customer && !empty($customer->email)) {
        Log::info('Email sending to customer', ['email' => $customer->email]);

        try {
            $fromEmail = config('mail.from.address');
            Mail::to($customer->email)
                ->from($fromEmail)
                ->send(new CustomRepairEmail($this->repair));

            Log::info('Email sent successfully to', ['email' => $customer->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send email', ['error' => $e->getMessage()]);
        }
    } else {
        Log::info('Email not sent: no email address set for customer', [
            'customer_email' => $customer->email ?? 'N/A'
        ]);
    }
}

}
