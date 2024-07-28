<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Headers;

class mailRemarketing extends Mailable
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
        
        $date = Carbon::create(env('RMKT_FINALDATE'));
        if($date->isCurrentDay()){
            $status= "10 วันสุดท้าย ใกล้หมดเวลาอย่าลืมไปใช้สิทธิ์ โปรแกรม LOVE Solution Cat Plus ที่คลินิกหรือโรงพยาบาลสัตว์ ที่ได้ลงทะเบียนไว้ ";

            $lastSelect=$this->client->option_1??$this->client->option_2??$this->client->option_3;
            $thisSelect=$lastSelect==1?3:1;

            $this->banner=asset('rmktbanner/banner monthly.jpg');
                $status=[
                    'หมายเลข '.$client->phone.'',
                    'คุณมีสิทธิพิเศษเข้าร่วม',
                    'ถึงเวลาปกป้องปรสิตสำหรับน้อง '.$this->client->pet_name,
                    'โปรแกรม '.env('APP_NAME').' คงเหลือ ',
                    'ดูแลปกป้องปรสิตสําหรับน้อง '.$this->client->pet_name,
                    'สามารถรับสิทธิ์ได้ที่ '.$this->client->vet->vet_name,
                ];
        }elseif($rmktClient){
            $rmktClientActive=$this->client->rmkt->where('active_status','activated')->last();
            if($rmktClient==$rmktClientActive){
                $status="ครบ 1 เดือนแล้ว ถึงเวลาที่คุณต้องปกป้อง พาราไซท์  อย่าลืมปกป้อง";


                $this->banner=asset('rmktbanner/banner monthly.jpg');
                $status=[
                    'หมายเลข '.$client->phone.'',
                    'ครบระยะเวลา 1 เดือนแล้ว',
                    'ถึงเวลาปกป้องปรสิตสำหรับน้อง '.$this->client->pet_name,
                    'อย่าลืม! ดูแลอย่างต่อเนื่อง',
                    'กับ '.env('APP_NAME'),
                    env('APP_TAGLINE'),
                ];
            }else{
                $lastSelect=$this->client->option_1??$this->client->option_2??$this->client->option_3;
                $thisSelect=$lastSelect==1?3:1;
                $status="คุณยังมีสิทธิ ".$thisSelect." เดือน อย่าลืมไปใช้สิทธิ์ โปรแกรม ".env('APP_NAME')." ที่คลินิกหรือโรงพยาบาลสัตว์ ที่ได้ลงทะเบียนไว้";
                $status="ครบ 1 เดือนแล้ว ถึงเวลาที่คุณต้องปกป้อง พาราไซท์  อย่าลืมปกป้อง";


                $this->banner=asset('rmktbanner/banner monthly.jpg');
                $status=[
                    'หมายเลข '.$client->phone.'',
                    'ครบระยะเวลา 1 เดือนแล้ว',
                    'ถึงเวลาปกป้องปรสิตสำหรับน้อง '.$this->client->pet_name,
                    'อย่าลืม! ดูแลอย่างต่อเนื่อง',
                    'กับ '.env('APP_NAME'),
                    env('APP_TAGLINE'),
                ];
            }
        }else{
            if($this->client->option_2 && $this->client->option_3){
                $status="ครบ 1 เดือนแล้ว ถึงเวลาที่คุณต้องปกป้อง พาราไซท์  อย่าลืมปกป้อง";
                $this->banner=asset('rmktbanner/banner monthly.jpg');
                $status=[
                    'หมายเลข '.$client->phone.'',
                    'ครบระยะเวลา 1 เดือนแล้ว',
                    'ถึงเวลาปกป้องปรสิตสำหรับน้อง '.$this->client->pet_name,
                    'อย่าลืม! ดูแลอย่างต่อเนื่อง',
                    'กับ '.env('APP_NAME'),
                    env('APP_TAGLINE'),
                ];
            }else{
                $lastSelect=$this->client->option_1??$this->client->option_2??$this->client->option_3;
                $thisSelect=$lastSelect==1?3:1;
                $this->banner=asset('rmktbanner/banner '.$thisSelect.' month.jpg');
                $status=[
                    'หมายเลข '.$client->phone,
                    'คุณมีสิทธิพิเศษเข้าร่วม',
                    'โปรแกรม '.env('APP_NAME').' คงเหลือ '.$thisSelect.' เดือน',
                    'ดูแลปกป้องปรสิตสําหรับน้อง '.$this->client->pet_name,
                    'สามารถรับสิทธิ์ได้ที่ '.$this->client->vet->vet_name,
                ];
                // $status="คุณยังมีสิทธิ ".$thisSelect." เดือน อย่าลืมไปใช้สิทธิ์ โปรแกรม ".env('APP_NAME')." ที่คลินิกหรือโรงพยาบาลสัตว์ ที่ได้ลงทะเบียนไว้";

                // dd('send remider in 7 day last select '.$lastSelect);
            }
        }
        Log::info('send email : '.implode(" ",$status) );
        $this->status=$status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: env('APP_NAME').' : ถึงเวลาที่คุณต้องปกป้อง พาราไซท์ อย่าลืมปกป้อง',
        );
    }
    public function headers(): Headers
    {
        $c=$this->client;
        $h=new Headers(
            messageId: $c->email,
            references: [],
            text:[
                'TRANSACTIONAL_GROUP_HEADER'=>'X-TM-Transc-Group',
                'MESSAGE_ID_HEADER'=>'X-TM-Ref-Msg-Id',
                'TRANSACTIONAL_REPORT_HEADER'=>'X-TM-Webhook',
                'X-TM-From-Name'=>env('MAIL_FROM_NAME'),
                'X-TM-From'=>env('MAIL_FROM_ADDRESS'),
            ]
        ) ;
        return $h;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.remarketing',
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
