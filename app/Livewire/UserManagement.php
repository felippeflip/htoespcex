<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role = 'operator';
    public $isCreating = false;

    public $userId;
    public $isEditing = false;

    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->userId],
            'role' => ['required', 'in:admin,operator'],
        ];

        if ($this->isCreating) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::defaults()];
        }

        return $rules;
    }

    public function create()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role', 'userId']);
        $this->role = 'operator';
        $this->isCreating = true;
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->isCreating = true; // Reuse the form
        $this->isEditing = true;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->isEditing = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role', 'userId']);
    }

    public function store()
    {
        $this->validate();

        if ($this->isEditing) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
            session()->flash('message', 'Usuário atualizado com sucesso!');
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);
            session()->flash('message', 'Usuário criado com sucesso!');
        }

        $this->isCreating = false;
        $this->isEditing = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role', 'userId']);
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::paginate(10),
        ])->layout('layouts.app');
    }
}
