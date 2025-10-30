<?php

namespace App\Mail;

use App\Models\User;
use App\Models\WaterBill;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public User $customer;
    public WaterBill $bill;

    public function __construct(User $customer, WaterBill $bill)
    {
        $this->customer = $customer;
        $this->bill = $bill;
    }

    public function build()
    {
        return $this->subject('Water Bill Updated')
            ->view('emails.bill-updated');
    }
}













