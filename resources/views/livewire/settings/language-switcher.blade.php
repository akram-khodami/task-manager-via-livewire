@php($locale = $locale ?? app()->getLocale())

<div class="d-flex gap-1">

    <button
        wire:click="setLocale('fa')"
        class="btn btn-sm {{ $locale === 'fa' ? 'btn-info' : 'btn-dark' }}"
    >
        🇮🇷 فارسی
    </button>

    <button
        wire:click="setLocale('en')"
        class="btn btn-sm {{ $locale === 'en' ? 'btn-info' : 'btn-dark' }}"
    >
        🇬🇧 English
    </button>

</div>
