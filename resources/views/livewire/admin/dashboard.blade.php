<div class="container m-auto">
    <div class="flex gap-4 flex-wrap py-2">
        <div class="p-4 px-6 rounded-xl shadow-md bg-primary-blue text-white">
            <span> Total : </span><span class="text-xl md:text-2xl font-bold"> {{$static['client']}} </span>
        </div>
        <div class="p-4 px-6 rounded-xl shadow-md bg-white text-primary-blue">
            <span> Complete : </span><span class="text-xl md:text-2xl font-bold"> {{$static['client_activated']}} </span>
        </div>
        <div class="p-4 px-6 rounded-xl shadow-md bg-white text-primary-blue">
            <span> Waiting : </span><span class="text-xl md:text-2xl font-bold"> {{$static['client_pending']}} </span>
        </div>
    </div>

    <div class="text-primary-blue grid md:grid-cols-3 gap-2 py-2 mt-4">
        <div class="flex gap-2">
            <span>
                <p>
                    รับคำปรึกษาและเข้าร่วมโปรแกรม<br> {{env('APP_NAME')}}
                </p>
                <x-badge label="(Get free consultation and a tablet)" />
            </span>
            <span class="text-2xl font-bold float-right">{{$static['client_option_1']}}</span>
        </div>
        <div class="flex gap-2">
            <span>
                <p>
                    รับสิทธิ์พิเศษเพิ่มเติม <br>- เข้าโปรแกรม 1 เดือน
                </p>
                <x-badge label="(Extra tablet sold)" />
            </span>
            <span class="text-2xl font-bold float-right">{{$static['client_option_2']}}</span>
        </div>
        <div class="flex gap-2">
            <span>
                <p>
                    รับสิทธิ์พิเศษเพิ่มเติม <br>- เข้าโปรแกรม 3 เดือน
                </p>
                <x-badge label="(Extra box sold)" />
            </span>
            <span class="text-2xl font-bold float-right">{{$static['client_option_3']}}</span>
        </div>
    </div>
    @if(env('APP_DEBUG'))
    <x-button label="{{$rmkt?'Re-Markweting Client':'Client'}}" wire:click="toggleRmkt" />
    @endif
    @if ($rmkt)
    <div class="mt-7">
        <livewire:admin.rmkt-client/>
    </div>
    @else
    {{-- {{$search['text']}} --}}
    <div class="flex gap-2 py-4 flex-wrap sm:flex-nowrap justify-between">
        <x-input label="ค้นหา" wire:model.live.debounce.700ms="search.text" hint="ค้นหา ชื่อลูกค้า หมายเลขโทรศัพท์ หรือรหัสสถานพยาบาล">
            <x-slot name="append">
                <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                    <x-button flat icon="search"/>
                </div>
            </x-slot>
        </x-input>

        <span class="sm:w-44">
        <x-native-select
            label="สถานะ"
            placeholder="แสดงทั้งหมด"
            :options="['Activated', 'Pending']"
            wire:model.live="search.status"
        />
        </span>
        <span class="sm:w-44">
        <x-native-select
            label="จำนวนที่แสดง"
            :options="[25,50,100,250]"
            wire:model.live="search.paginate"
        />
        </span>
    </div> 

    
    
    <div class="overflow-x-auto">
        <table class="table-fixed min-w-full whitespace-nowrap">
            <thead>
                <tr class="border border-primary-blue bg-primary-blue text-primary-lite text-xs">
                    <th class="w-8">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="ลำดับ"/>
                            <x-badge primary label="No"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="วันที่"/>
                            <x-badge primary label="Date"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="ชื่อคลินิก"/>
                            <x-badge primary label="Clinic/Hospital"/>
                        </div>
                    </th>
                    <th class="">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="ชื่อลูกค้า"/>
                            <x-badge primary label="Pet owner's Name"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="น้ำหนัก แมว"/>
                            <x-badge primary label="Pet's weight"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="สถานะ"/>
                            <x-badge primary label="Status"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="สิทธิ์ทั้งหมด"/>
                            <x-badge primary label="All Quota"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="สิทธิ์คงเหลือ"/>
                            <x-badge primary label="Remaining Quota"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="สิทธิ์ที่รับแล้ว"/>
                            <x-badge primary label="Redeemed"/>
                        </div>
                    </th>
                    <th class="hidden md:table-cell">
                        <div class="grid">
                            <x-button primary-blue flat class="pointer-events-none text-white hover:bg-white/20"  label="สินค้าขาด"/>
                            <x-badge primary label="Out of quota"/>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="border border-primary-blue p-2">

                </tr>
                @foreach ($clients as $client)
                    <tr class="border border-primary-blue p-2">
                        <td class="align-top border border-primary-blue p-2">
                            {{$client->client_code}}
                        </td>
                        <td class="align-top md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">วันที่</span> {{$client->created_at->toDateString()}}
                        </td>

                        @isset($client->vet)
                        <td class="align-top md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <x-button flat label="{{$vets->where('id',$client->vet_id)->first()->vet_name}}" :href="route('admin.vet',['id'=>$client->vet->id])" />
                        </td>
                        @endisset
                        <td class="align-top md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            {{$client->name}} 
                            @if (Auth::user()->isAdmin)
                            <x-button label="x" wire:click="delete({{$client}})" wire:confirm="คุณต้องการยืนยันการลบหรือไม่"/>
                            @if($client->email)
                            <x-button label="Email" wire:click="sendEmail({{$client}})" wire:confirm="คุณต้องการยืนยันการส่ง email หรือไม่"/>
                            @else
                            <x-button label="SMS" wire:click="sms({{$client}})" wire:confirm="คุณต้องการยืนยันการส่ง SMS หรือไม่"/>
                            @endif
                            <x-button label="View Email" :href="route('email.remarketing',$client->phone)" target="_blank" wire:confirm="คุณต้องการยืนยันการส่ง email หรือไม่"/>
                            @endif
                        </td>
                        <td class="align-top md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">น้ำหนัก  แมว</span>
                            {{$client->pet_weight}}
                        </td>
                        <td class="align-top md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">สถานะ</span>
                            {{$client->active_status}}
                        </td>
                        @isset($client->vet)
                        <td class="align-top text-center md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">สิทธิ์ทั้งหมด</span>
                            {{$vets->find($client->vet_id)->stock->total_stock }}
                        </td>
                        <td class="align-top text-center md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">สิทธิ์คงเหลือ</span>
                            {{$vets->find($client->vet_id)->stock->current()['remaining']}}
                        </td>
                        <td class="align-top text-center md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">สิทธิ์ที่รับแล้ว</span>
                            {{$vets->find($client->vet_id)->stock->current()['redeemed']}}
                        </td>
                        <td class="align-top text-center md:border border-primary-blue p-2 ml-2 table md:table-cell">
                            <span class="md:hidden inline-block min-w-max mr-2">สินค้าขาด</span>
                            {{$vets->find($client->vet_id)->stock->current()['outQuota']}}
                        </td>
                        @endisset
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
    <div class="my-4">{{$clients->links()}}</div>

    @endif
</div>
