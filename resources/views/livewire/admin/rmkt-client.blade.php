<div>
    {{-- The best athlete wants his opponent at his best. --}}


    <div class="overflow-x-auto">
    <table class="min-w-full table-fixed whitespace-nowrap">
        <thead>
            <tr class="border border-primary-blue bg-secondary-red text-xs">
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
                            รับคำปรึกษา<br>และเข้าร่วม <br>โปรแกรม <br>{{env('APP_NAME')}}
                        </x-button>
                            <br>
                        <x-badge primary >(Get free <br>consultation <br>and a tablet)</x-badge>
                </th>
                @endif
                @if(env('VET_OPTION_2'))
                <th class="w-20 text-primary-lite hidden md:table-cell">
                        <x-button flat white class="pointer-events-none" >
                            รับสิทธิ์พิเศษเพิ่มเติม <br> เข้าร่วมโปรแกรม<br> 1 เดือน
                        </x-button><br>
                        <x-badge primary >(Extra tablet<br> sold)</x-badge>
                    </div>
                </th>
                @endif
                @if(env('VET_OPTION_3'))
                <th class="w-20 text-primary-lite hidden md:table-cell" colspan="{{env('VET_OPTION_3_option')?'2':'1'}}">
                        <x-button flat white class="pointer-events-none" >
                            รับสิทธิ์พิเศษเพิ่มเติม <br> เข้าร่วมโปรแกรม 3 เดือน
                        </x-button><br>
                        <x-badge primary >(Extra box<br> sold)</x-badge>
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
            @foreach ($clients as $client)
            <tr class="border border-primary-blue">
                <td class="align-top md:border mx-2 border-primary-blue p-2 md:w-auto md:table-cell">{{$client->profile->client_code}}</td>
                <td class="align-top md:border mx-2 border-primary-blue p-2 block w-full md:w-auto md:table-cell">
                    {{$client->updated_at->format('d/m/y')}}
                </td>
                <td class="align-top border whitespace-nowrap border-primary-blue p-2 block md:table-cell">
                    <x-button label="{{$client->profile->name}}" :href="route('admin.client.profile',$client->profile->client_code)" />
                    <x-button xs primary label="{{$client->profile->phone}}" href="tel:{{$client->profile->phone}}"/>
                    
                    @if (Auth::user()->isAdmin)
                        <x-button xs negative icon="x" wire:click="delete({{$client}})" class="ml-auto" wire:confirm="คุณต้องการยืนยันการลบหรือไม่"/>
                    @endif
                    @isset($vet)
                    @else
                    <br>
                    <x-button :href="route('admin.vet',$client->vet->id)">
                    {{$client->vet->id}} : 
                    {{$client->vet->vet_name}}
                    </x-button>
                    @endisset
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
                    <span class="md:hidden inline-block min-w-max mx-2 mt-1">น้ำหนัก  แมว : </span> {{$client->profile->pet_weight}}
                </td>
                <td class="align-top md:border mx-2 whitespace-nowrap border-primary-blue p-2 md:text-center table w-full md:w-auto md:table-cell">
                    <span class="md:hidden inline-block min-w-max mx-2">สถานะ : </span> {{$client->active_status??'-'}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    </div>
    {{-- <div class="my-4">{{$clients->links()}}</div> --}}
</div>
