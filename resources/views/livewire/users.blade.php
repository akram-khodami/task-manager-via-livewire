<div class="row" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    <!-- Title and Add User Button -->
    <div class="col-12 d-flex justify-content-between align-items-center my-2">
        <h2 class="mb-0">{{ __('messages.users') }}</h2>

        <button wire:click="openModal()" class="btn btn-primary btn-sm">
            ➕ {{ __('messages.new_user') }}
        </button>
    </div>

    <div class="col-12">

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="alert alert-success my-2">
                {{ session('message') }}
            </div>
        @endif

    <!-- Search -->
        <div class="my-2">
            <input type="text" wire:model.live.debounce.500ms="search" class="form-control" placeholder="{{ __('messages.search_user') }}">
        </div>

        <!-- Modal Form -->
        @if($isOpen)
            <div class="row my-3">
                <div class="col-md-6">

                    <div class="card shadow-sm">
                        <div class="card-header text-white {{ $user_id ? 'bg-warning' : 'bg-success' }}">
                            {{ $user_id ? __('messages.edit_user') : __('messages.add_user') }}
                        </div>

                        <div class="card-body">

                            <form wire:submit.prevent="store">

                                <!-- Name -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.name') }}</label>
                                    <input type="text" wire:model="name" class="form-control">
                                    @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.email') }}</label>
                                    <input type="email" wire:model="email" class="form-control">
                                    @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ __('messages.password_label') }}
                                        @if($user_id)
                                            <small class="text-muted">{{ __('messages.password_hint') }}</small>
                                        @endif
                                    </label>
                                    <input type="password" wire:model="password" class="form-control">
                                    @error('password')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" wire:click="closeModal()" class="btn btn-secondary">
                                        {{ __('messages.cancel') }}
                                    </button>

                                    <button type="submit"
                                            class="btn btn-primary">
                                        {{ $user_id ? __('messages.edit') : __('messages.save') }}
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
    @endif

    <!-- Users Table -->
        <div class="table-responsive bg-white shadow-sm rounded p-2 mt-3">

            <table class="table table-bordered table-striped align-middle">
                <thead class="bg-info text-white">
                <tr>
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y/m/d') }}</td>
                        <td>

                            <button wire:click="edit({{ $user->id }})"
                                    class="btn btn-warning btn-sm">
                                ✏️ {{ __('messages.edit') }}
                            </button>

                            <button wire:click="delete({{ $user->id }})"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                🗑️ {{ __('messages.delete') }}
                            </button>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="my-2">
                {{ $users->links() }}
            </div>

        </div>

    </div>
</div>
