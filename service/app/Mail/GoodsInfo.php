<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GoodsInfo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('购物车报价')
        ->view('testMail')->attach(
            public_path('/export/data/cartOffer/cart_191216.xls'),
            ['as' => '191216_购物车.xls','mime' => 'application/xls',]
        );
    }


}
