<?php

namespace App\Livewire\Client;

use App\Mail\mailRemarketing;
use App\Mail\mailBadge;
use App\Models\client;
use Livewire\Component;

class Badge extends Component
{
    public function render()
    {
        $c=client::first();
        return new mailBadge($c);
        return view('livewire.client.badge');
    }
}
