<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Users extends Component
{
    use WithPagination;

    public $name, $email, $password, $user_id;
    public $isOpen = 0;
    public $search = '';

    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'password' => $this->user_id ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);//ToDO:manage magic number

        return view('livewire.users',
            [
                'users' => $users,
                'isRtl' => $this->isRtl(),
            ])->layout('components.layouts.app',
            [
                'isRtl' => $this->isRtl(),
                'title' => __('messages.manage_users')
            ]);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'email', 'password', 'user_id']);
    }

    //create or update
    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        session()->flash('message',
            $this->user_id ? __('messages.user_updated') : __('messages.user_created')
        );

        $this->closeModal();
    }

    //Show Edit Form
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';

        $this->openModal();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', __('messages.user_deleted'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

}
