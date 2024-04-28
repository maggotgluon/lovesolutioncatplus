<?php

namespace App\Mail;

use App\Models\client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class mailBadge extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $status;
    public $banner;
    public $link=false;

    /**
     * Create a new message instance.
     */
    public function __construct($client = null)
    {
        
        $this->client = $client??client::first();

        $rmktClient=$this->client->rmkt->last();
        
        // dd($rmktClient,$this->client->rmkt->count());
        $this->banner = asset('badge/'.$this->client->rmkt->count().'.jpg');
        // Log::info('send email : '.implode(" ",$status) );
        // $this->status=$status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mail Badge',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.badge',
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
