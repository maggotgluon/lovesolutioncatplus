<?php

namespace App\Livewire\Admin;

use App\Mail\mailRemarketing;
use App\Models\client as clientModel;
use App\Models\vet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

use Illuminate\Support\Facades\Http;


use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Dashboard extends Component
{

    use Actions;
    public $search=[
        'text'=>null,
        'status'=>null,
        'paginate'=>25
    ];
    public $static;
    public $vets;
    public $rmkt=false;
    
    protected $queryString = [
        'search.text'=> ['except' => '','as'=>'q'],
        'search.status'=> ['except' => '','as'=>'s'],
        'search.paginate'=> ['except' => 25,'as'=>'p']
    ];

    use WithPagination;

    public function mount(){
        if(!Auth::user()->isAdmin){
            return redirect()->route('admin.vet' ,Auth::user()->id);
        }
        $client=clientModel::all();
        $this->static=[
            'client'=>$client->count(),
            'client_activated'=>$client->countBy('active_status')['activated']??0,
            'client_pending'=>$client->countBy('active_status')['pending']??0,
            'client_option_1'=>$client->where('option_1')->count(),
            'client_option_2'=>$client->where('option_2')->count(),
            'client_option_3'=>$client->where('option_3')->count(),
        ];
        // dd($this->static);
        $this->vets=vet::with('stock')->get();
    }
    public function render(){
        return view('livewire.admin.dashboard',[
            'clients'=>clientModel::with('vet')
                ->when($this->search['text']!=null,function($queryString){
                    // dd($queryString->get());
                    $text = $this->search['text'];
                    // dd($text);
                    return $queryString->orWhere('name','like','%'.$text.'%')
                        ->orWhere('vet_id','like','%'.$text.'%')
                        ->orWhere('phone','like','%'.$text.'%');
                })
                ->when($this->search['status']!=null,function($queryString){
                    $text = $this->search['status'];
                    return $queryString->where('active_status',$text);
                })->orderBy('updated_at','DESC')
                ->paginate($this->search['paginate'])
        ])->extends('layouts.admin');
    }
    public function updatingSearch(){
        $this->resetPage();
    }

    public function toggleRmkt(){
        $this->rmkt=!$this->rmkt;
    }
    public function sendEmail(clientModel $client){
        Mail::to($client->email)->send(new mailRemarketing($client));
    }
    public function delete (clientModel $client){
        $client->delete();
    }

    public $token;

    public function sms($client){
        $client = clientModel::find($client['id']);
        // dd($client);
        $date = Carbon::create(env('RMKT_FINALDATE'));

        if($date->isCurrentDay()){
            $final = Carbon::create('1 May 2024');
            if($client->updated_at<=$final){
                $msg = '10 วันสุดท้าย ใกล้หมดเวลาอย่าลืมไปใช้สิทธิ์ CatPLUS คลิก '.route('email.remarketing',$client->phone) ;
            }
        }else{
            if($client->updated_at<=today()->subDay(7) 
            && $client->remark==null){

                if($client->option_2 && $client->option_3){

                }else{
                    $lastSelect=$client->option_1??$client->option_2??$client->option_3;
                    $thisSelect=$lastSelect==1?3:1;
                    $msg = 'คุณยังมีสิทธิ '.$thisSelect.' เดือน อย่าลืมไปใช้สิทธิ์ โปรแกรม คลิก '.route('email.remarketing',$client->phone) ;
                }
            }else{ //($client->updated_at<=today()->subDay(25)){
                $msg = 'ครบ 1 เดือนแล้ว ถึงเวลาที่คุณต้องปกป้อง CatPLUS คลิก '.route('email.remarketing',$client->phone) ;
            }
        }
        // dd($this->APIlogin(),
        // $client->phone,
        // $msg );
        if(isset($msg)){
            $this->sendSms(
                $this->APIlogin(),
                $client->phone,
                $msg );
        }
    }

    public function APIlogin(){
        $response = \Illuminate\Support\Facades\Http::asForm()->post(env('TAXI_URL')."/v2/user/login", [
            "api_key" => env('TAXI_APIKEY'),
            "secret_key" => env('TAXI_APISRECRET')
        ]);

        if($response->successful()){
            $body = $response->json();
            $this->notification()->success(
                $title = 'success',
                $description = "LOGIN TO : ".$body['data']['session_id']
            );
            $this->token=$body['data']['session_id'];
            return $body['data']['session_id'];
        }else{
            $this->notification()->error(
                $title = 'Error !!!',
                $description = $response
            );
            return $response->transferStats->getResponse();
        }
    }

    public function sendSms($sesson,$to,$msg){
        if($this->token==null){
            $this->token=$this->APIlogin();
        }
        $token=$this->token;
        $phone = '66' . str_replace('-', '', $to);

        $response = Http::withToken($token)
        ->post(env('TAXI_URL')."/v2/sms",[
            "from" => "catplus",
            "to" => $phone,
            "text" => $msg
        ]);

        if($response->successful()){
            $body = $response->json();
            Log::info("Send SMS TO : ".$phone." massage".$msg.' body '.$body);
            $this->notification()->success(
                $title = 'success',
                $description = "Send SMS TO : ".$phone." massage".$msg.' body '.$body
            );
        }else{
            Log::error("Error SMS TO : ".$phone.' response '.$response);
            $this->notification()->error(
                $title = 'Error !!!',
                $description = "Error SMS TO : ".$phone.' response '.$response
            );
        }
    }
}
