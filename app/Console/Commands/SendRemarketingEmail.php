<?php

namespace App\Console\Commands;

use App\Mail\mailConfirmation;
use App\Mail\mailRemarketing;
use App\Models\client;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// use Mail;

class SendRemarketingEmail extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    public $token,$session;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-remarketing-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send re marketing email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $date = Carbon::create(env('RMKT_FINALDATE'));
        if($date->isCurrentDay()){
            $final = Carbon::create('1 May 2024');
            $clients = client::whereDate('updated_at','<=',$final)
            ->where('active_status','activated')->get();
            $this->info("last 10 day mail run");
            if ($clients->count() > 0) {
                Log::info("10 วันสุดท้าย remider email send list : ".$clients);
                foreach ($clients as $client) {
                    if($client->email){
                        try {
                            Mail::to($client->email)->send(new mailRemarketing($client));
                            $this->updateClient($client,'last 10 day remider');
                            Log::info("last 10 day remider email sended to: ".$client->client_code.' : '.$client->name);
                            $this->info("last 10 day remider email sended to: ".$client->client_code.' : '.$client->name);
    
                        } catch (\Throwable $exception) {
                            $this->error('client '.$client->client_code.' : '.$client->name.' | send Command last 10 day failed with error: '.$exception->getMessage());
                            Log::error($client->client_code.' : '.$client->name.$exception);
                            // return self::FAILURE;
                        }
                    }
                }

            }
            $this->info("last 10 day mail finished");
            return self::SUCCESS;
            // (ถ้ากดใช้สิทธิ์ครั้งสุดท้ายเดือนพฤษภาคม ข้อความนี้ไม่ต้องส่ง)
            // "10 วันสุดท้าย ใกล้หมดเวลาอย่าลืมไปใช้สิทธิ์ โปรแกรม LOVE Solution Cat Plus ที่คลินิกหรือโรงพยาบาลสัตว์ ที่ได้ลงทะเบียนไว้ "
        }
        //update last 30 day


        $clients = client::whereDate('updated_at','<=',today()->subDay(7))
            ->where('active_status','activated')
            ->whereNull('remark')->get();
        if ($clients->count() > 0) {
            Log::info("7 day remider email send list : ".$clients);
            $this->info("7 day remider mail run");
            foreach ($clients as $client) {
                try {
                    if($client->option_2 && $client->option_3){
                        // dd('both');
                    }else{
                        // send mail
                        Mail::to($client->email)->send(new mailRemarketing($client));
                        $this->updateClient($client,'7 day remider mail');
                        Log::info("7 day remider email sended to: ".$client->client_code.' : '.$client->name);
                        $this->info("7 day remider email sended to: ".$client->client_code.' : '.$client->name);
                        // if($client->email){
                        // }
                        // update user
                        // dd('single');
                    }
                } catch (\Throwable $exception) {
                    $this->error('client '.$client->client_code.' : '.$client->name.' | send Command 7 day failed with error: '.$exception->getMessage());
                    Log::error($client->client_code.' : '.$client->name.$exception);
        
                    // return self::FAILURE;
                }
            }
            $this->info("7 day remider mail finished");

        }

        $clients = client::whereDate('updated_at','<=',today()->subDay(25))
            ->where('active_status','activated')->get();
        if ($clients->count() > 0) {
            Log::info("25 day remider email send list : ".$clients);
            $this->info("25 day remider mail run");
            foreach ($clients as $client) {
                try {
                // $rmktClient=$client->rmkt->last();
                    Mail::to($client->email)->send(new mailRemarketing($client));

                    $this->updateClient($client,'25 day remider mail');
                    Log::info("25 day remider email sended to: ".$client->client_code.' : '.$client->name);
                    $this->info("25 day remider email sended to: ".$client->client_code.' : '.$client->name);

                } catch (\Throwable $exception) {
                    $this->error('client '.$client->client_code.' : '.$client->name.' | send Command 25 day failed with error: '.$exception->getMessage());
                    Log::error($client->client_code.' : '.$client->name.$exception);
        
                    // return self::FAILURE;
                }
            }
            $this->info("25 day remider mail finished");
        }

        return self::SUCCESS;
    }

    public function updateClient(client $client,$data=null){
        $data=[];
        $cerrent = $client->remark;
        if($cerrent){
            $last = last($cerrent)['no'];
            // dd($cerrent,$last);
            array_push($cerrent, [
                'no'=>$last+1,
                'date'=>now(),
                'data'=>$data
            ]);
            $data=$cerrent;
        }else{
            array_push($data, [
                'no'=>1,
                'date'=>now(),
                'data'=>$data
            ]);
        }
        $client->remark=$data;
        $client->updated_at=now();
        $client->save();
    }

    public function APIlogin(){
        $response = \Illuminate\Support\Facades\Http::asForm()->post(env('TAXI_URL')."/v2/user/login", [
            "api_key" => env('TAXI_APIKEY'),
            "secret_key" => env('TAXI_APISRECRET')
        ]);

        if($response->successful()){
            $body = $response->json();
            return $body['data']['session_id'];
        }else{
            return $response->transferStats->getResponse();
        }
    }

    public function sendSms($sesson,$to,$msg){
        if($this->token==null || $sesson==null){
            $this->token=$this->APIlogin();
        }
        $token=$this->token;
        $queryString = http_build_query([
            "from" => "catplus",
            "to" => $to,
            "text" => $msg
        ]);

        $response = \Illuminate\Support\Facades\Http::withToken($token)->post(env('TAXI_URL')."/v2/sms?{$queryString}", []);

        if($response->successful()){
            $body = $response->json();
            $this->info("SMS TO : ".$to." massage".$msg);
            $this->info("Response Body : ".$body);
        }else{
            Log::error("Error SMS TO : ".$to." massage".$msg);
            $this->info("Response Body : ".$$response);
        }
    }
}
