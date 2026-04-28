<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class Home extends Component
{
    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    public function render()
    {
        return view('livewire.home',
            [

            ])->layout('components.layouts.app',
            [
                'isRtl' => $this->isRtl(),
                'title' => __('messages.home_page')
            ]);
    }
}
