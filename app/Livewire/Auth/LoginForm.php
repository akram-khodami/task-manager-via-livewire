<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginForm extends Component
{

    public $email, $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required'
    ];

    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    public function login()
    {
        $this->validate();

        if (!Auth::guard('web')->attempt(
            [
                'email' => $this->email,
                'password' => $this->password,
            ]
        )) {

            $this->addError('auth', __('messages.user_logged_in_unsuccessfully'));//ToDo:it does not work
        }

        session()->regenerate();

        session()->flash('success_message', __('messages.user_logged_in_successfully'));

        return $this->redirect('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.login-form')
            ->layout('components.layouts.app',
                [
                    'isRtl' => $this->isRtl(),
                    'title' => __('messages.login_page_title')
                ]);
    }
}
