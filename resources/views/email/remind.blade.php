@extends('layouts.email')

@section('content')
    <div class="container m-auto mt-4 mb-10">
        <x-logo class="h-20 m-auto"/>
        <div class="p-4 rounded-xl shadow-lg text-center">
            <h1 class="bg-primary-blue text-white text-2xl font-bold pt-4 text-center">
                {{env('APP_NAME')}}
            </h1>
            <div class="bg-primary-blue text-white font-bold pb-2 text-center">
                {{env('APP_TAGLINE')}}
            </div>
            
            <img src="{{$banner}}" class="m-auto w-full"/>
            <p class="max-w-sm m-auto text-center my-4">
                @foreach ( $status as $str )
                    {{$str}}<br>
                @endforeach
            </p>
            <x-button lg primary  
            class="m-4 bg-gradient-to-br from-gradient-start to-gradient-end rounded-2xl"
            :href="route('client.rmkt',['phone'=>$client->phone])">
            <div>
            <div class="text-3xl">คลิก!</div>
            รับสิทธิ์
            </div>
            </x-button>
            <p class="max-w-sm m-auto text-center my-4">
                กรุณากดรับสิทธิ์ขณะอยู่ที่คลินิกหรือ โรงพยาบาลสัตว์<br>
                ที่ลงทะเบียนเท่านั้น<br>
            </p>

        </div>
    </div>
@endsection