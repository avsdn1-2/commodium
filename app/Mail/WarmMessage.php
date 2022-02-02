<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarmMessage extends Mailable
{
    use Queueable, SerializesModels;

    private $month;
    private $month_name;
    private $year;
    private $tariff_additional;
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($month,$month_name,$year,$tariff_additional,$data)
    {
        //
        $this->month = $month;
        $this->month_name = $month_name;
        $this->year = $year;
        $this->tariff_additional = $tariff_additional;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.warm',[
            'month' => $this->month,
            'month_name' => $this->month_name,
            'year' => $this->year,
            'tariff_additional' => $this->tariff_additional,
            'data' => $this->data,
            ]);
    }
}
