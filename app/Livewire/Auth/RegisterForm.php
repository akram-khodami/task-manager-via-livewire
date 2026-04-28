<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterForm extends Component
{
    public $name, $email, $password, $password_confirmation;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users,email',
        'password' => 'required|string|min:8|max:16|confirmed'
    ];

    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    public function register()
    {
        $this->validate();

        $user = User::create(
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]
        );

        Auth::guard('web')->login($user);

        session()->flash('success_message', __('messages.'));

        return $this->redirect('/dashboard');
    }

    #[Title('Register')]
    public function render()
    {
        return view('livewire.auth.register-form')
            ->layout('components.layouts.app',
                [
                    'isRtl' => $this->isRtl(),
                    'title' => __('messages.register_page_title')
                ]);
    }
}
