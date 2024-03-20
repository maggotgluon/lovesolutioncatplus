<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'total_stock',
        'stock_adj',
    ];
    public function vet(){
        return $this->hasMany(vet::class);
    }
    public function clients(){
        return $this->hasManyThrough(client::class,vet::class);
    }
    public function rmktClients(){
        return $this->hasManyThrough(rmktClient::class,vet::class);
    }

    public function current(){
        $client=$this->clients;

        $quota = $this->total_stock;
        $redeemed = $client->where('active_status','activated')->count();
        $activate_opt_1 = $client->where('active_status','activated')->where('option_2')->count();
        $activate_opt_3 = $client->where('active_status','activated')->where('option_3')->count();
        $activate_opt = $activate_opt_1 +$activate_opt_3 ;

        $opt_1 = $client->where('option_2')->count();
        $opt_3 = $client->where('option_3')->count();
		$opt = $opt_1+$opt_3;

        $pending = $client->where('active_status','<>','activated')->count();
		
        // $rmktClient=$this->rmktClients;

        // $rmktredeemed = $rmktClient->where('active_status','activated')->count();
        // $rmktactivate_opt_1 = $rmktClient->where('active_status','activated')->where('option_2')->count();
        // $rmktactivate_opt_3 = $rmktClient->where('active_status','activated')->where('option_3')->count();
        // $rmktactivate_opt = $rmktactivate_opt_1 + $rmktactivate_opt_3 ;

        // $rmktopt_1 = $rmktClient->where('option_2')->count();
        // $rmktopt_3 = $rmktClient->where('option_3')->count();
		// $rmktopt = $rmktopt_1+$rmktopt_3;

        // $rmktpending = $rmktClient->where('active_status','<>','activated')->count();


        // $redeemed+=$rmktredeemed;
        // $activate_opt+=$rmktactivate_opt;
        // $pending+=$rmktpending;
        // $opt+=$rmktopt;

        $remaining = $quota - $redeemed;
        $outQuota = ($quota - $redeemed - $pending <= 0)?($quota - $redeemed - $pending):0;

        return [
            'client_all'=>$client->count(),
            'quota'=>$quota,
            'redeemed'=>$redeemed,
            'remaining'=>$remaining,
            'pending'=>$pending,
            'outQuota'=>$outQuota,
        ];
    }

}
