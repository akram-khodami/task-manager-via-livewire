<div class="row">
    <div class="col-md-12 mt-4 mb-1">
        @if (session()->has('success_message'))
            <div class="alert alert-success" role="alert">
                {{ session('success_message') }}
            </div>
        @endif
    </div>
</div>
