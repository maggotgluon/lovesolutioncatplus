<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\downloads;
use App\Livewire\Admin\Client\Index as AdminClientIndex;
use App\Livewire\Admin\Client\Profile as AdminClientProfile;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Passwords\Confirm;
use App\Livewire\Auth\Passwords\Email;
use App\Livewire\Auth\Passwords\Reset;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Verify;
use App\Livewire\Client\Register as ClientRegister;
use App\Livewire\Client\Login as ClientLogin;
use App\Livewire\Client\Dashboard as ClientDashboard;
use App\Livewire\Client\Rmkt as ClientRmkt;
use App\Livewire\Client\RmktSelect as ClientRmktSelect;
use App\Livewire\Client\Profile as ClientProfile;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Vets as AdminVets;
use App\Livewire\Admin\Vet as AdminVet;
use App\Livewire\Admin\RmktClient as AdminRmkt;
use App\Livewire\Management\Vet as ManagementVet;
use App\Livewire\Management\VetEdit as ManagementVetEdit;
use App\Mail\mailConfirmation;
use App\Mail\mailRemarketing;
use App\Models\client;
use App\Models\rmktClient;
use App\Models\User;
use App\Models\vet;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/dev/{id?}', function($id=null){
    if($id){
        $c=client::where('phone',$id)->firstOrFail();
    }else{
        $c=client::first();
    }

    // dd($c);
    return new mailRemarketing($c);
});
// Route::view('/', 'welcome')->name('home');
Route::middleware('auth')->name('ma.')->prefix('ma')->group(function (){
    Route::get('/vet', ManagementVet::class)->name('vet');
    Route::get('/vet/edit/{vet?}', ManagementVetEdit::class)->name('vet.edit');
    
});
Route::get('/', ClientRegister::class)->name('home');

Route::name('client.')->prefix('client')->group(function (){
    
    // $date = Carbon::create('21 Aug 2024');
    // if(now()==$date){
        Route::get('/', ClientRegister::class)->name('home');
        Route::get('/login/{phone?}', ClientLogin::class)->name('login');
        Route::get('/ticket/{phone?}', ClientDashboard::class)->name('ticket');
        
        Route::get('/rmkt/{phone?}', ClientRmkt::class)->name('rmkt');

        Route::get('/rmkt/select/{phone?}', ClientRmktSelect::class)->name('rmkt.select');
        
        Route::get('/profile/{client_code?}', ClientProfile::class)->name('profile');
        Route::get('/download/', [downloads::class,'client'])->name('download');
    // }else{
        Route::fallback(function () {
            return view('maintenance');
        });
    // }
});

Route::name('email.')->prefix('email')->group(function (){

    Route::get('rmkt/{phone?}',function($phone=null){
        if($phone){
            $c=client::where('phone',$phone)->firstOrFail();
        }else{
            $c=client::first();
        }
        // dd($c);
        return new mailRemarketing($c);
    })->name('remarketing');
});


Route::name('test.')->prefix('test')->group(function (){
    
    Route::view('email/','email.index');
    Route::view('email/confirmation/{phone?}','email.confirmation',['client'=>client::find(1)])->name('email.confirmation');
    Route::get('email/rmkt/{phone?}',function($phone=null){
        if($phone){
            $c=client::where('phone',$phone)->firstOrFail();
        }else{
            $c=client::first();
        }
        // dd($c);
        return new mailRemarketing($c);
    })->name('email.remarketing');
    // Route::get(function () {
    Route::fallback(function () {
        $c=client::whereDate('updated_at',now()->addDay(-5))->get();
        $clients = rmktClient::whereDate('updated_at',today())->distinct('client_id')->get();
        dd($clients,now()->addDay(-30));
        // dd(client::find(1)->created_at->addDay(4),client::find(1)->created_at->addDay(4)->isToday());

        if ($clients->count() > 0) {
            foreach ($clients as $client) {
                Mail::to($client)->send(new mailRemarketing($client));
            }
        }
    });

});

Route::middleware('auth')->name('admin.')->prefix('admin')->group(function (){
    Route::get('/', AdminDashboard::class)->name('home');
    Route::get('/vets', AdminVets::class)->name('vets');
    Route::get('/vet/{id?}', AdminVet::class)->name('vet');
    
    Route::get('/rmkt/{id?}', AdminRmkt::class)->name('rmkt');

    Route::get('/client/', AdminClientIndex::class)->name('client.index');
    Route::get('/client/{client_code?}', AdminClientProfile::class)->name('client.profile');
    // Route::get('/vet/{id?}', AdminDashboard::class)->name('home');
    
    Route::get('register', Register::class)->name('register');
    Route::get('edit/{id?}', Register::class)->name('edit');
    Route::get('/logout', LogoutController::class)->name('logout');

});


Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    // Route::get('register', Register::class)
    //     ->name('register');
});

// Route::get('password/reset', Email::class)
//     ->name('password.request');

// Route::get('password/reset/{token}', Reset::class)
//     ->name('password.reset');

// Route::middleware('auth')->group(function () {
//     Route::get('email/verify', Verify::class)
//         ->middleware('throttle:6,1')
//         ->name('verification.notice');

//     Route::get('password/confirm', Confirm::class)
//         ->name('password.confirm');
// });

// Route::middleware('auth')->group(function () {
//     Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)
//         ->middleware('signed')
//         ->name('verification.verify');

//     Route::post('logout', LogoutController::class)
//         ->name('logout');
// });


