<div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-primary d-flex align-items-center" role="alert">
                <h3>
                    <div class="spinner-grow text-primary" role="status">
                        <span class="visually-hidden">{{__('messages.loading_text')}}</span>
                    </div>
                    {{__('messages.wellcome_text')}} {{ auth()->user()->name }}
                </h3>
            </div>
        </div>
    </div>

    <div>
        @livewire('projects')
    </div>

</div>
