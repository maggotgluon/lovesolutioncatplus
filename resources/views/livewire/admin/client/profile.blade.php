<div>
    client_code : {{$client->client_code}}<br>
    Name : {{$client->name}}<br>
    email : {{$client->email}}<br>
    phone : {{$client->phone}}<br>

    <hr>
    
    pet_name : {{$client->pet_name}}<br>
    pet_breed : {{$client->pet_breed}}<br>
    pet_weight : {{$client->pet_weight}}<br>
    pet_age_month : {{$client->pet_age_year}} ปี {{$client->pet_age_month}} เดือน<br>
    <hr>
    RMKT:
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
    <x-button label="back" :href="route('admin.client.index')"/>
</div>
