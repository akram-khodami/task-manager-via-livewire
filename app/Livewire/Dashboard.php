<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Dashboard extends Component
{
    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app',
                [
                    'isRtl' => $this->isRtl(),
                    'title' => __('messages.dashboard_page')
                ]);
    }
}
