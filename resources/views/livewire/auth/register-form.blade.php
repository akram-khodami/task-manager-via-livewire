@extends('components.layouts.auth-layout')
@section('form')
    <form wire:submit.prevent="register">

        <div class="form-outline">
            <label class="form-label" for="name">{{__('messages.name_label')}}</label>
            <input type="text" name="name" id="name" class="form-control"
                   placeholder="{{__('messages.full_name_placeholder')}}" wire:model="name"/>
            @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-outline">
            <label class="form-label" for="email">{{__('messages.email_label')}}</label>
            <input type="email" name="email" id="email" class="form-control"
                   placeholder="{{__('messages.email_address_placeholder')}}"
                   wire:model="email"/>
            @error('email') <span class="error text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-outline">
            <label class="form-label" for="password">{{__('messages.password_label')}}</label>
            <input type="password" name="password" id="password" class="form-control" wire:model="password"/>
            @error('password') <span class="error text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-outline">
            <label class="form-label" for="password_confirmation">{{__('messages.confirm_password_label')}}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                   wire:model="password_confirmation"/>
            @error('password_confirmation') <span class="error text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="text-center pt-1 pb-1">
            <button type="submit" class="btn btn-primary btn-block fa-lg gradient-custom-2" type="button">{{__('messages.register_button')}}
            </button>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-4">
            <p class="me-2">{{__('messages.lead_to_login_text')}}</p>
            <button type="button" class="btn btn-outline-danger">{{__('messages.login_button')}}
            </button>
        </div>

    </form>
@endsection
