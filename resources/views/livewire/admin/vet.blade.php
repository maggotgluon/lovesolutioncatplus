<div>
    <div class="flex justify-between">
        <div>
            <h2 class="text-lg text-primary-blue font-bold">
                {{ $vet->vet_name }}
            </h2>
            <p>
                {{$vet->vet_area}} {{$vet->vet_city}} {{$vet->vet_province}}
            </p>
        </div>
        <span>
        <x-badge class="rounded-2xl bg-primary-blue text-white p-4 shadow-lg" label="รหัส : {{$vet->id}}" />
        </span>
    </div>
    <hr class="border-2 border-primary-blue my-4">
    <div class="grid md:grid-cols-2 my-4 gap-4">
        <div>
            <div class="flex gap-2">
                <div class=" rounded-2xl bg-primary-blue text-primary-lite/70 p-4 shadow-lg ">
                    Total :
                    <span class="text-2xl font-bold block">
                        {{$vet->stock->current()['client_all']}}
                    </span>
                </div>
                <div class=" rounded-2xl text-black/70 p-4 shadow-lg ">
                    Complete :
                    <span class="text-2xl font-bold block">
                        {{$vet->stock->current()['redeemed']}}
                    </span>
                </div>
                <div class=" rounded-2xl text-black/70 p-4 shadow-lg ">
                    Waiting :
                    <span class="text-2xl font-bold block">
                        {{$vet->stock->current()['pending']}}
                    </span>
                </div>
            </div>
            <p class="mt-4 flex gap-2"> 
                <span>รับคำปรึกษาและเข้าร่วมโปรแกรม {{env('APP_NAME')}} <br>
                    <x-badge label="(Get free consultation and a tablet)"/></span>
                <span class="font-bold text-xl text-black/70">

                    {{$vet->stock->clients->where('option_1')->count()}}
                </span>
            </p>
            <p class="mt-2 flex gap-2">
                <span>รับสิทธิ์พิเศษเพิ่มเติม - เข้าโปรแกรม 1 เดือน <br>
                    <x-badge label="(Extra tablet sold)"/></span>
                <span class="font-bold text-xl text-black/70">
                    {{$vet->stock->clients->where('option_2')->count()}}
                </span>
            </p>
            <p class="mt-2 flex gap-2">
                <span>รับสิทธิ์พิเศษเพิ่มเติม - เข้าโปรแกรม 3 เดือน <br>
                    <x-badge label="(Extra box sold)"/></span>
                <span class="font-bold text-xl text-black/70">
                    {{$vet->stock->clients->where('option_3')->count()}}
                </span>
            </p>
        </div>
        <div>
            <div class="flex gap-2 justify-end">
                <div class=" rounded-2xl text-black/70 p-2 shadow-lg ">
                    สิทธิ์ทั้งหมด
                    <x-badge label="All Quota" />
                    <span class="text-2xl font-bold block">
                        
                        {{$vet->stock->total_stock}}
                    </span>
                </div>
                <div class=" rounded-2xl text-black/70 p-2 shadow-lg ">
                    จำนวนครั้งที่เติม
                    <x-badge label="Remaining Quota" />
                    <span class="text-2xl font-bold block">
                        
                        {{$vet->stock->stock_adj}}
                    </span>
                </div>
                <div class=" rounded-2xl text-black/70 p-2 shadow-lg ">
                    สิทธิ์คงเหลือ
                    <x-badge label="Remaining Quota" />
                    <span class="text-2xl font-bold block">
                        
                        {{$vet->stock->current()['remaining']}}
                    </span>
                </div>
                <div class=" rounded-2xl bg-red-300 text-black/70 p-2 shadow-lg ">
                    สิทธิ์ขาด
                    <x-badge label="Out of quota" />
                    <span class="text-2xl font-bold block">
                        {{$vet->stock->current()['outQuota']}}
                    </span>
                </div>
            </div>
            @can('isAdmin')
                <div class="text-right rounded-2xl text-black/70 p-4 shadow-lg ">
                    <x-inputs.number wire:model="stock_adj" label="จำนวนสินค้าที่เติม : " />
                    <x-button primary class="my-4" label="บันทึก" wire:click="add_stock_adj" />
                </div>
            @endcan
        </div>
    </div>
    @if(env('APP_DEBUG'))
    <x-button label="{{$rmkt?'Re-Markweting Client':'Client'}}" wire:click="toggleRmkt" />
    @endif
    @if ($rmkt)
    <div class="mt-7">
        <livewire:admin.rmkt-client :id='802390' />
    </div>
    @else
    <div>
        <!-- updated_at | desc -->
        <div class="mt-7 overflow-x-auto">
        @if ($vetClients->count())
            <table class="min-w-full table-fixed whitespace-nowrap">
                <thead>
                    <tr class="border border-primary-blue bg-primary-blue  text-xs">
                        <th class="w-6 md:w-24 md:table-cell">
                                <x-button flat white class="pointer-events-none" label="ลำดับ"/><br>
                                <x-badge primary label="No" />
                        </th>
                        <th class="w-24 hidden md:table-cell">
                                <x-button flat white class="pointer-events-none" label="วันที่"/><br>
                                <x-badge primary label="Date" />
                        </th>
                        <th class="w-full">
                                <x-button flat white class="pointer-events-none" label="ชื่อลูกค้า"/><br>
                                <x-badge primary label="Pet owner's Name" />
                            </div>
                        </th>
                        @if(env('VET_OPTION_1'))
                        <th class="w-20 text-primary-lite hidden md:table-cell">
                                <x-button flat white class="pointer-events-none" >
                                    รับคำปรึกษาและเข้าร่วม <br>โปรแกรม {{env('APP_NAME')}}
                                </x-button>
                                    <br>
                                <x-badge primary label="(Get free consultation and a tablet)" />
                        </th>
                        @endif
                        @if(env('VET_OPTION_2'))
                        <th class="w-20 text-primary-lite hidden md:table-cell">
                                <x-button flat white class="pointer-events-none" >
                                    รับสิทธิ์พิเศษเพิ่มเติม <br> เข้าร่วมโปรแกรม 1 เดือน
                                </x-button><br>
                                <x-badge primary label="(Extra tablet sold)" />
                            </div>
                        </th>
                        @endif
                        @if(env('VET_OPTION_3'))
                        <th class="w-20 text-primary-lite hidden md:table-cell" colspan="{{env('VET_OPTION_3_option')?'2':'1'}}">
                                <x-button flat white class="pointer-events-none" >
                                    รับสิทธิ์พิเศษเพิ่มเติม <br> เข้าร่วมโปรแกรม 3 เดือน
                                </x-button><br>
                                <x-badge primary label="(Extra box sold)" />
                        </th>
                        @endif
                        <th class="w-24 hidden md:table-cell text-primary-lite">
                                <x-button flat white class="pointer-events-none" label="น้ำหนัก  แมว"/><br>
                                <x-badge primary label="Pet's weight" />
                        </th>
                        <th class="w-24 hidden md:table-cell">
                                <x-button flat white class="pointer-events-none" label="สถานะ"/><br>
                                <x-badge primary label="Status" />
                        </th>
                    </tr>
                    
                </thead>
                <tbody>
                    @foreach ($vetClients as $client)
                    <tr class="border border-primary-blue">
                        <td class="align-top md:border mx-2 border-primary-blue p-2 md:w-auto md:table-cell">{{$client->client_code}}</td>
                        <td class="align-top md:border mx-2 border-primary-blue p-2 block w-full md:w-auto md:table-cell">
                            {{-- {{$client->updated_at->format('d/m/y')}} --}}
                            @isset($client->active_date)
							{{$client->active_date}}
							@endisset
                        </td>
                        <td class="align-top border whitespace-nowrap border-primary-blue p-2 block md:table-cell">
                            {{$client->name}}
                            <x-button xs primary label="{{$client->phone}}" href="tel:{{$client->phone}}"/>
                            
                            @if (Auth::user()->isAdmin)
                                <x-button xs negative icon="x" wire:click="delete({{$client}})" class="ml-auto" wire:confirm="คุณต้องการยืนยันการลบหรือไม่"/>
                            @endif
                        </td>
                        @if(env('VET_OPTION_1'))
                        <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 hidden w-full md:w-auto md:table-cell md:text-center ">
                            @if($client->option_1)
                            <x-badge.circle positive icon="check" class="w-5 h-5 m-auto p-2 inline-block" />
                            <span class="md:hidden inline-block min-w-max mx-2 my-1">เข้าร่วม โปรแกรม {{env('APP_NAME')}}</span>
                            @endif
                        </td>
                        @endif
                        @if(env('VET_OPTION_2'))
                        <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 hidden w-full md:w-auto md:table-cell md:text-center ">
                            @if($client->option_2 )
                            <x-badge.circle positive icon="check" class="w-5 h-5 m-auto p-2 inline-block" />
                            <span class="md:hidden inline-block min-w-max mx-2 my-1">เข้าร่วมโปรแกรม 1 เดือน</span>
                            @endif
                        </td>
                        @endif
                        @if(env('VET_OPTION_3'))
                        <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 hidden w-full md:w-auto md:table-cell md:text-center ">
                            @if($client->option_3 )
                            <x-badge.circle positive icon="check" class="w-5 h-5 m-auto p-2 inline-block" />
                            @endif
                        </td>
                        @if(env('VET_OPTION_3_option'))
                            <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 hidden w-full md:w-auto md:table-cell md:text-center ">
                                @if($client->option_3 )
                                <span class="md:hidden inline-block min-w-max mx-2 my-1">เข้าร่วมโปรแกรม </span>{{ $client->option_3 }} เดือน
                                @endif

                            </td>
                        @endif
                        @endif
                        <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 md:text-center table w-full md:w-auto md:table-cell">
                            <ul class="md:hidden">
                                @if($client->option_1)
                                <li><x-badge.circle positive icon="check" class="w-5 h-5 m-auto p-2 inline-block" /> <span class="md:hidden inline-block min-w-max mx-2 my-1">เข้าร่วม โปรแกรม {{env('APP_NAME')}}</span></li>
                                @endif
                                @if($client->option_2)
                                <li><x-badge.circle positive icon="check" class="w-5 h-5 m-auto p-2 inline-block" /> <span class="md:hidden inline-block min-w-max mx-2 my-1">เข้าร่วมโปรแกรม 1 เดือน</span></li>
                                @endif
                                @if($client->option_3)
                                <li><x-badge.circle positive icon="check" class="w-5 h-5 m-auto p-2 inline-block" /> <span class="md:hidden inline-block min-w-max mx-2 my-1">เข้าร่วมโปรแกรม </span>{{ $client->option_3 }} เดือน</span></li>
                                @endif
                            </ul>
                            <span class="md:hidden inline-block min-w-max mx-2 mt-1">น้ำหนัก  แมว : </span> {{$client->pet_weight}}
                        </td>
                        <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 md:text-center table w-full md:w-auto md:table-cell">
                            <span class="md:hidden inline-block min-w-max mx-2">สถานะ : </span> {{$client->active_status??'-'}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="my-4">
            {{$vetClients->links()}}
        </div>
        @else
        <div class="text-center p-4">No client regis yet</div>
        @endif
    </div>
    @endif
</div>
