<?php

namespace App\Livewire;

use Livewire\Component;

class DynamicModal extends MyComponent
{
    public $modalColorClass;

    protected $listeners = ['openModal' => 'open', 'closeModal' => 'close'];

    public function open($formComponent = '', $modalTitle = "", $modalColorClass = '', $data = [])
    {

        $this->modalOpen = true;
        $this->modalTitle = $modalTitle;
        $this->modalColorClass = $modalColorClass;
        $this->modalData = $data;
        $this->formComponent = $formComponent;
    }

    public function close()
    {
        $this->modalOpen = false;
        $this->modalTitle = '';
        $this->modalColorClass = '';
        $this->modalData = [];
        $this->formComponent = '';
    }

    public function render()
    {
        return view('livewire.dynamic-modal');
    }
}
