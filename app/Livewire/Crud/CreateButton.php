<?php

namespace App\Livewire\Crud;

use Livewire\Component;

class CreateButton extends Component
{
    public $model;
    public $modelTitle;

    public function create()
    {
        $this->dispatch(
            'openModal',
            formComponent: "{$this->model}.create-form",
            modalTitle: "{$this->modelTitle}" . " " . " {$this->model}",
            modalColorClass: 'bg-success',
            data: [],
        )->to('dynamic-modal');
    }

    public function render()
    {
        return view('livewire.crud.create-button');
    }
}
