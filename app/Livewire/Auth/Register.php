<?php

namespace App\Livewire\Auth;

use App\Models\stock;
use App\Models\thailandAddr;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\vet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;

class Register extends Component
{
    public $haveVet;
    public $addr;
    public $province,$district,$tambon;
    
    public $vetId,$vetName,$stockID,$stockTotal;

    /** @var string */
    public $userCode = '';
    /** @var string */
    public $name = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $password = '';

    /** @var string */
    public $passwordConfirmation = '';

    public function register()
    {
        $validate = $this->validate([
            'userCode' => ['required','unique:users,id'],
            'name' => ['required','unique:users'],
            'email' => ['required', 'unique:users'],
            'password' => ['required', 'min:8', 'same:passwordConfirmation'],
            'vetId'=> ['nullable','exists:vets,id'],
            'vetName'=> ['requiredIf:haveVet,true'],
            'stockID'=> ['requiredIf:haveVet,true'],
            'stockTotal'=> ['requiredIf:haveVet,true'],
            'province'=> ['requiredIf:haveVet,true'],
            'district'=> ['requiredIf:haveVet,true'],
            'tambon'=> ['requiredIf:haveVet,true'],

        ]);
        $user = User::create([
            'id'=>$this->userCode,
            'email' => $this->email,
            'name' => $this->name,
            'isAdmin' => false,
            'password' => Hash::make($this->password),
        ]);
        // $vet = vet::create([
        //     'id'=>$this->userCode,
        //     'name' => $this->vetName,
        //     'vet_province' => $this->province,
        //     'vet_city' => $this->district,
        //     'vet_area' => $this->tambon,
        //     'user_id' => $this->userCode,
        //     'stock_id' => $this->stockID,
        // ]);
        // $stock = stock::create([
        //     'id'=>$this->stockID,
        //     'total_stock' => $this->stockTotal,
        //     'stock_adj' => 0,
        // ]);
        event(new Registered($user));
        dd($user);

        // Auth::login($user, true);

        return redirect()->intended(route('home'));
    }
    public function mount(User $user){
        // if($id){
        //     $this->user=User::find($id);
        // }

    }
    public function render()
    {
        return view('livewire.auth.register')->extends('layouts.auth');
    }

    public function updatedHaveVet($haveVet){
        if($haveVet){
            $this->addr=[
                'province' => thailandAddr::whereNotIn('Province',['กรุงเทพมหานคร'])->distinct('Province')->orderBy('Province')->pluck('Province'),
                'district'=>null,
                'district'=>null,
            ];
            $this->addr['province']->prepend('กรุงเทพมหานคร');
            $this->vetName=$this->name;
        }
    }
    public function updatedProvince($province){
        $this->addr['district']=thailandAddr::where('Province',$province)->distinct('district')->pluck('district');
    }
    public function updatedDistrict($district){
        $this->addr['tambon']=thailandAddr::where('Province',$this->province)->where('District',$district)->distinct('tambon')->pluck('tambon');
        // dd($this->addr['tambon']);
    }
}

