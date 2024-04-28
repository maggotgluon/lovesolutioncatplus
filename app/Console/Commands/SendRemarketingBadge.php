<?php

namespace App\Console\Commands;

use App\Mail\mailBadge;
use App\Models\client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRemarketingBadge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-remarketing-badge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send re marketing badge email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $clients = client::whereDate('updated_at','<=',today()->subDay(7))
            ->where('active_status','activated')
            ->whereNull('remark')->get();

            foreach ($clients as $client) {
                try {
                    Mail::to($client->email)->send(new mailBadge($client));
                    // $this->updateClient($client,'7 day remider mail');
                    // Log::info("7 day remider email sended to: ".$client->client_code.' : '.$client->name);
                    // $this->info("7 day remider email sended to: ".$client->client_code.' : '.$client->name);
                } catch (\Throwable $exception) {
                    // $this->error('client '.$client->client_code.' : '.$client->name.' | send Command 7 day failed with error: '.$exception->getMessage());
                    // Log::error($client->client_code.' : '.$client->name.$exception);

                    // return self::FAILURE;
                }
            }
            $this->info("7 day remider mail finished");
    }
}
