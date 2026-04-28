@extends('components.layouts.auth-layout')
@section('form')
    <form wire:submit.prevent="login">

        <div class="form-outline mb-4">
            <label class="form-label" for="email">{{__('messages.username_label')}}</label>
            <input type="email" name="email" id="email" class="form-control"
                   placeholder="{{__('messages.email_address_placeholder')}}"
                   wire:model.live="email"/>
            @error('email') <span class="error text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-outline mb-4">
            <label class="form-label" for="password">{{__('messages.password_label')}}</label>
            <input type="password" name="password" id="password" class="form-control" wire:model.live="password"/>
            @error('password') <span class="error text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="text-center pt-1 mb-5 pb-1">
            <button type="submit" class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="button">
                {{__('messages.login_button')}}
            </button>
            <a class="text-muted" href="#!">{{__('messages.recover_password')}}</a>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-4">
            <p class="mb-0 me-2">{{__('messages.lead_to_register_text')}}</p>
            <a href="{{route('register')}}">
                <button type="button" class="btn btn-outline-danger">{{__('messages.register_button')}}</button>
            </a>
        </div>

    </form>
@endsection
