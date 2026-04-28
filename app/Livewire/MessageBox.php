<?php

namespace App\Livewire;

use Livewire\Component;

class MessageBox extends Component
{
    public $message;

    protected $listeners = ['showMessage'];

    public function showMessage($message)
    {
        $this->message = $message;
    }

    public function render()
    {
        return <<<'HTML'
    <div>
        @if ($message)
            <div class="alert alert-danger">
                <span>{{ $message }}</span>
            </div>
        @endif
    </div>
    HTML;
    }
}
