<?php

namespace App\Livewire\Client;

use App\Models\client;
use Livewire\Component;

class Profile extends Component
{
    public client $client;

    public function mount($client_code=null){
        $this->client = client::where('client_code',$client_code)->firstOrFail();
        $status="";

        $rmktClient=$this->client->rmkt->last();
        if($rmktClient){
            $rmktClientActive=$this->client->rmkt->where('active_status','activated')->last();
            if($rmktClient==$rmktClientActive){
                $status="can select";
            }else{
                $status="swap";
            }
        }else{
            if($this->client->option_2 && $this->client->option_3){
                $dateSend = $this->client->updated_at->addDay(25);
                // dd('send remider in 25 day at '.$dateSend->toDateString());
                // status can select
            }else{
                $dateSend = $this->client->updated_at->addDay(7);
                $lastSelect=$this->client->option_1??$this->client->option_2??$this->client->option_3;
                
                // dd('send remider in 7 day last select '.$dateSend.$lastSelect);
            }
        }
        // dd($status);

    }
    public function render()
    {
        return view('livewire.client.profile');
    }
}