<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class remind extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $status;
    public $banner;
    /**
     * Create a new message instance.
     */
    public function __construct($client)
    {
        $this->client = $client;
        $rmktClient=$this->client->rmkt->last();
        if($rmktClient){
            $rmktClientActive=$this->client->rmkt->where('active_status','activated')->last();
        }else{
            $lastSelect=$this->client->option_1??$this->client->option_2??$this->client->option_3;
            $thisSelect=$lastSelect==1?3:1;

            $this->banner=asset('banner/banner '.$thisSelect.' month.jpg');
            $this->status=[
                'หมายเลข '.$client->phone.'',
                'คุณมีสิทธิพิเศษเข้าร่วม',
                'โปรแกรม '.env('APP_NAME').' คงเหลือ '.$thisSelect.' เดือน',
                'ดูแลปกป้องปรสิตสําหรับน้อง '.$this->client->pet_name,
                'สามารถรับสิทธิ์ได้ที่',
                $this->client->vet->vet_name,
            ];

            // dd($this->client,$lastSelect);

        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'คูปองใกล้หมดเขต...ถึงเวลาแล้วที่น้องแมวต้องได้รับการปกป้องจากปรสิตตัวร้าย',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.remind',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
