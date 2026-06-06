<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Bill;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;

    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }

    public function build()
    {
        // Ensure related models are loaded so blade can access product names when the
        // mailable is serialized or queued.
        $this->bill->loadMissing(['items.product', 'user']);

        return $this->subject('Your Purchase Invoice')
                    ->view('emails.invoice')
                    ->with([
                        'bill' => $this->bill,
                    ]);
    }
}
