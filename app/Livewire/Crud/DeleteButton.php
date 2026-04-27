<?php

namespace App\Livewire\Crud;

use Livewire\Component;

class DeleteButton extends Component
{
    public $itemId;
    public $model;
    public $modelTitle;

    public function delete()
    {
        $this->dispatch(
            'openModal',
            formComponent: "{$this->model}.delete-form",
            modalTitle: "{$this->modelTitle}" . " " . " {$this->model}",
            modalColorClass: 'bg-danger',
            data: ['id' => $this->itemId]
        )->to('dynamic-modal');
    }

    public function ender()
    {
        return view('livewire.crud.delete-button');
    }
}
