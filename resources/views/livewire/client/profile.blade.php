<div>
    
    Name : {{$client->name}}<br>
    first time regis : {{$client->created_at}}<br>
    {{-- number of rmkt " {{$client->rmkt->count()}} times<br>
    rmkt opt 1 month : {{$client->rmkt->whereNotNull('option_2')->count()}} times<br>
    rmkt opt 3 month : {{$client->rmkt->whereNotNull('option_3')->count()}} times<br> --}}
    <div class="max-h-64 overflow-y-scroll">
    @foreach ($client->rmkt as $c)
    <div class="p-2 odd:bg-gray-200 rounded my-2">
    {{$c->vet->vet_name}} : select {{$c->option_3?'3':'1'}} month <br>
    {{-- {{$c->created_at->toDateString()}} :  --}}
    {{$c->updated_at->toDateString()}} <br>
    {{$c->active_status}} <br>
    {{-- diff {{$c->created_at->diffInMinutes($c->updated_at)}} min<br> --}}
    {{-- last update {{$c->updated_at->diffForHumans(now())}} --}}
    </div>
    @endforeach
    </div>
</div>
