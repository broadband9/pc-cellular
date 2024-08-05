<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Update the user password if it's set
    protected function afterSave(): void
    {
        $user = $this->record;

        // Ensure roles field is available in the form state
        if (array_key_exists('roles', $this->form->getState())) {
            $user->syncRoles($this->form->getState()['roles']);
        }

        // Hash the password if it's been updated
        if (!empty($this->form->getState()['password'])) {
            $user->password = Hash::make($this->form->getState()['password']);
            $user->save();
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Name'),

            Forms\Components\TextInput::make('email')
                ->required()
                ->email()
                ->label('Email'),

            Forms\Components\TextInput::make('password')
                ->password()
                ->label('Password')
                ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                ->afterStateUpdated(fn ($state) => $this->updatePassword($state)),

            // Assuming roles are fetched dynamically or statically
            Forms\Components\Select::make('roles')
                ->multiple()
                ->relationship('roles', 'name')
                ->label('Roles')
                ->required()
                ->options($this->getRolesOptions()),
        ];
    }

    // Method to get available roles for selection
    private function getRolesOptions(): array
    {
        return \Spatie\Permission\Models\Role::all()->pluck('name', 'id')->toArray();
    }
}
