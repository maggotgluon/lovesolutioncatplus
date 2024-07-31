<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\client;
use Illuminate\Support\Facades\Log;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email every 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $clients = client::whereDate('updated_at','<=',today())
            ->where('active_status','activated')->get();

        foreach ($clients as $client) {
            try {
                Mail::to($client->email)->send(new \App\Mail\remind($client));
                // $this->updateClient($client,'7 day remider mail');
                Log::info("email sended to: ".$client->client_code.' : '.$client->name);
                // $this->info("7 day remider email sended to: ".$client->client_code.' : '.$client->name);
            } catch (\Throwable $exception) {
                // $this->error('client '.$client->client_code.' : '.$client->name.' | send Command 7 day failed with error: '.$exception->getMessage());
                Log::error($client->client_code.' : '.$client->name.$exception);

                // return self::FAILURE;
            }
        }
        $this->info('Email sent successfully.');
    }
}
