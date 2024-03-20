<?php

namespace App\Livewire\Client;

use App\Mail\mailConfirmation;
use App\Models\client as clientModel;
use App\Models\vet;
use Livewire\Component;
use Illuminate\Support\Str;

use GuzzleHttp\Client as smsClient;
use Illuminate\Support\Facades\Mail;

class Register extends Component
{
    public $currentStep = 1;
    public $status;
    public $regClient;

    public $errorMessage;

    public $vet_list;
    public $selected_vet;


    public function mount(){
        
        $vet=vet::all();
        $this->vet_list['province'] = $vet->whereNotIn('vet_province',['กรุงเทพมหานคร'])->unique('vet_province')->sortBy('vet_province')->pluck('vet_province');
        $this->vet_list['province']->prepend('กรุงเทพมหานคร');
        $this->vet_list['name'] = $vet->pluck('vet_name','id');
        $this->regClient=[
            'firstname'=>null,
            'lastname'=>null,
            'phone'=>null,
            'email'=>null,
            'consent'=>null,
            'pet_name'=>null,
            'pet_breed'=>null,
            'pet_weight'=>null,
            'pet_age_year'=>null,
            'pet_age_month'=>null,
            'vet_id'=>null,
        ];
        // $this->regClient['consent']=true;
        if(!env('SMS_API')){
            $this->regClient=[
                'firstname'=>'owner name',
                'lastname'=>'sure name',
                'phone'=>'',
                'email'=>'',
                'consent'=>true,
                'pet_name'=>'petname',
                'pet_breed'=>'breed',
                'pet_weight'=>'1.25-2.5 กก.',
                'pet_age_year'=>'1',
                'pet_age_month'=>'1',
                'vet_id'=>null];
        }
        $this->selected_vet=[
            'province'=>null,
            'district'=>null,
            'tambon'=>null,
            'name'=>null
        ];
    }
    public function render()
    {
        return view('livewire.client.register');
    }

    public function openConsent()
    {
        $this->regClient['consent'] = 1;
        $this->currentStep =1.25;
    }
    public function firstStepSubmit(){
        // validate
        $validatedData = $this->validate([
            'regClient.firstname' => ['required', 'string', 'max:255'],
            'regClient.lastname' => ['required', 'string', 'max:255'],
            'regClient.email' => ['nullable','email', 'max:255'],
            'regClient.phone' => ['required', 'numeric','digits:10','min:10','regex:/^([0-9\s\(\)]*)$/', 'unique:App\Models\client,phone'],
            'regClient.consent' => ['required','bool'],
        ],[
            'regClient.*.required'=>'จำเป็นต้องระบุ',
            'regClient.email.email' => 'กรุณากรอก อีเมล์ ที่ถูกต้อง',
            'regClient.phone.required' => 'จำเป็นต้องระบุ หมายเลขโทรศัพท์',
            'regClient.phone.unique' => 'หมายเลขโทรศัพท์ นี้ลงทะเบียนรับสิทธิ์แล้ว',
        ]);

        // send otp
        if(env('SMS_API', false)){
            $this->sendOTP();
        }
        // go next
        if($this->getErrorBag()->isEmpty()){
            $this->currentStep = 1.5;
        }
    }
    public function otpStepSubmit(){
        // validate

        $validatedData = $this->validate([
            'regClient.pin' => ['required', 'string', 'max:255'],
        ],[
            'regClient.*.required'=>'จำเป็นต้องระบุ',
        ]);
        // send otp
        // dd('val');
        // $this->verifyOTP();
        if(env('SMS_API', false)){
            $this->verifyOTP();
        }else{

        }
        // go next
        if($this->getErrorBag()->isEmpty()){
            $this->currentStep = 2;
        }
    }

    public function petStepSubmit(){
        // validate
        $validatedData = $this->validate([
            'regClient.pet_name' => ['required', 'string', 'max:255'],
            'regClient.pet_breed' => ['required', 'string', 'max:255'],
            'regClient.pet_weight' => ['required', 'string', 'max:255'],
            'regClient.pet_age_year' => ['required', 'numeric'],
            'regClient.pet_age_month' => ['required', 'numeric'],
        ],[
            'regClient.*.required'=>'จำเป็นต้องระบุ',
        ]);
        // go next
        $this->currentStep = 3;
    }

    public function vetStepSubmit(){
        // validate
        $validatedData = $this->validate([
            'selected_vet.name'=> ['required']
        ],[
            'regClient.*.required'=>'จำเป็นต้องระบุ',
        ]);

        // create client
        
        $client = clientModel::updateOrCreate([
            'phone'=>$this->regClient['phone'],
        ],[
            'email'=>$this->regClient['email']??null,
            'name'=>$this->regClient['firstname'].' '.$this->regClient['lastname'],
            'phoneIsVerified'=>$this->regClient['pin']??"-",
            'pet_name'=>$this->regClient['pet_name'],
            'pet_breed'=>$this->regClient['pet_breed'],
            'pet_weight'=>$this->regClient['pet_weight'],
            'pet_age_month'=>$this->regClient['pet_age_month'],
            'pet_age_year'=>$this->regClient['pet_age_year'],
            'vet_id'=>$this->regClient['vet']->id,
            'active_status'=>'pending',
            'client_code'=>0,
        ]);
        $client->client_code = 'REVO'.Str::padLeft($client->id, 5, '0');
        // dd($client);
        $client->save();


        // send sms and email
        if(env('SMS_API', false)){
            $this->confirmation();
        }else{

        }
        // redirect
        redirect( route('client.login',['phone'=>$this->regClient['phone']]) );
    }

    public function sendOTP(){

        $SMSclient = new smsClient;

        try {
            $SMSresponse = $SMSclient->request('POST', env('OTP_URL').'/v2/otp/request', [
                'form_params' => [
                    'key' => getenv('BULKSMS_KEY'),
                    'secret' => getenv('BULKSMS_SECRET'),
                    'msisdn' => '+66' . str_replace('-', '', $this->regClient['phone'])
                ],
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            ]);
            $response=json_decode($SMSresponse->getBody()->getContents());
            $this->regClient['token'] = $response->token;
            $this->regClient['refno'] = $response->refno;
            // dd($response);
        } catch (\Exception $e) {
            $this->addError('regClient', $e->getMessage());
            $this->errorMessage = $e->getMessage();
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifyOTP(){
        $SMSclient = new smsClient;

        try {
            $SMSresponse = $SMSclient->request('POST', getenv('OTP_URL').'/v2/otp/verify', [
            'form_params' => [
                'key' => getenv('BULKSMS_KEY'),
                'secret' => getenv('BULKSMS_SECRET'),
                'token' => $this->regClient['token'],
                'pin' => $this->regClient['pin']
            ],
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            ]);
        } catch (\Exception $e) {
            // $this->errorMessage = $e->getMessage();
            // dd($e->getMessage());
            $this->addError('regClient', $e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        // dd(json_decode($response->getBody()->getContents()) , $response, $response,$this->status );

        if (json_decode($SMSresponse->getBody()->getContents())->status != 'success') {
            $this->errorMessage = 'That code is invalid, please try again.';
            $this->addError('regClient','That code is invalid, please try again.');
        }else{
            $this->errorMessage = '';
            $this->status = 'approved';
        }
        return $this->status == 'approved';
    }

    public function confirmation(){
        $details = [
            'email' => $this->regClient['email']??null,
            'phone' => $this->regClient['phone'],
            'pet_name' => $this->regClient['pet_name'],
            'vet_name' => $this->regClient['vet']->vet_name,
            'name' => $this->regClient['firstname'].' '.$this->regClient['lastname'],
        ];

        try {
            if($this->regClient['email']){
                $mail = new mailConfirmation($details);
                Mail::to($this->regClient['email'])->send($mail);
            }
            $body_sms = 'ยืนยันลงทะเบียนสำเร็จ ใช้สิทธิ์คลิก '.route('client.login');
            $SMSclient = new smsClient;
            
            $response = $SMSclient->request('POST', env('SMS_URL').'/sms', [
                'form_params' => [
                    'msisdn' => '+66' . str_replace('-', '', $this->regClient['phone']) ,
                    'message' => $body_sms,
                    'sender' => getenv('SMS_SENDER'),
                    'force'=>'corporate',
                    'shorten_url' => 'true'
                ],
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => getenv('BULKSMS_AUTH'),
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            ]);

        } catch (\Exception $e) {
            $this->addError('regClient', $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function updatedSelectedVetProvince($selected_vet_province){
        // select province update list and district
        $vet_in_province = Vet::where('vet_province',$selected_vet_province)->get();
        $this->vet_list['city']=$vet_in_province->unique('vet_city')->pluck('vet_city');
        $this->vet_list['name']=$vet_in_province->pluck('vet_name','id');
        
        $this->selected_vet['district']=null;
        $this->selected_vet['tambon']=null;
        $this->regClient['vet_id']=null;
    }
    public function updatedSelectedVetDistrict($selected_vet_district){
        // select district update list and tambon
        $vet_in_province = Vet::where('vet_city',$selected_vet_district)->get();
        $this->vet_list['area']=$vet_in_province->unique('vet_area')->pluck('vet_area');
        $this->vet_list['name']=$vet_in_province->pluck('vet_name','id');
        
        $this->selected_vet['tambon']=null;
        $this->regClient['vet_id']=null;
    }
    public function updatedSelectedVetTambon($selected_vet_tambon){
        // select tambon update list
        $vet_in_province = Vet::where('vet_area',$selected_vet_tambon)->get();
        $this->vet_list['name']=$vet_in_province->pluck('vet_name','id');
        
        $this->regClient['vet_id']=null;
    }
    public function updatedSelectedVetName($name){
        // select tambon update list
        
        $this->regClient['vet']=Vet::find($name);
        $vet = Vet::find($name);
        // dd($vet->vet_province);
        // $this->selected_vet['province']=$vet->vet_province;
        // $this->selected_vet['district']=$vet->vet_city;
        // $this->selected_vet['tambon']=$vet->vet_area;
        
        // $this->vet_list['name']=$vet_in_province->pluck('vet_name','id');
    }
    public function step($goto=null){
        $this->currentStep =$goto??$this->currentStep+1;
    }

    public function updated($propertyName)
    {
        $this->resetErrorBag();
    }
}
