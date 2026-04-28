<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $locale;

    public function mount()
    {
        $this->locale = Session::get('locale', 'fa');
    }

    public function setLocale($lang)
    {
        $this->locale = $lang;

        Session::put('locale', $lang);

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.settings.language-switcher');
    }
}
