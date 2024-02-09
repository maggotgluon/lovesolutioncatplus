<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    /** @var string */
    public $username = '';

    /** @var string */
    public $password = '';

    /** @var bool */
    public $remember = false;

    protected $rules = [
        'username' => ['required'],
        'password' => ['required'],
    ];

    public function authenticate()
    {
        $this->validate();
        // dd(
        //     $this->username,(
        //         Auth::attempt(['id' => $this->username,'password' => $this->password], $this->remember)||
        //         Auth::attempt(['name' => $this->username,'password' => $this->password], $this->remember)||
        //         Auth::attempt(['email' => $this->username, 'password' => $this->password], $this->remember)
        //     )
        // );
        if (!
            (
                Auth::attempt(['id' => $this->username,'password' => $this->password], $this->remember)||
                Auth::attempt(['name' => $this->username,'password' => $this->password], $this->remember)||
                Auth::attempt(['email' => $this->username, 'password' => $this->password], $this->remember)
            )
            ) {
            $this->addError('username', trans('auth.failed'));

            return;
        }

        return redirect()->intended(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.login')->extends('layouts.auth');
    }
}
