<?php

namespace App\Livewire\Crud;

use Livewire\Component;

class EditButton extends Component
{
    public $itemId;
    public $model;
    public $modelTitle;

    public function edit()
    {
        $this->dispatch(
            'openModal',
            formComponent: "{$this->model}.edit-form",
            modalTitle: "{$this->modelTitle}" . " " . " {$this->model}",
            modalColorClass: 'bg-warning',
            data: ['id' => $this->itemId]
        )->to('dynamic-modal');
    }

    public function render()
    {
        return view('livewire.crud.edit-button');
    }
}
