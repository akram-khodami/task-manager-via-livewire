<?php


namespace App\Livewire;


use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MyComponent extends Component
{
    // use WithPagination;
    // use WithPagination, WithoutUrlPagination;

    public $perPage = 10;

    public $modalOpen = false;
    public $modalTitle = '';
    public $modalData = [];

    public $formComponent = '';

    public function openModal($title, $data = [])
    {
        $this->modalOpen = true;
        $this->modalTitle = $title;
        $this->modalData = $data;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->modalTitle = '';
        $this->modalData = [];
    }

    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

}
