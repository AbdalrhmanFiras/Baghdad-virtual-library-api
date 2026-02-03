<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('role')
                    ->required()
                    ->default('user'),
                TextInput::make('password')
                    ->password(),
                TextInput::make('google_id'),
                Select::make('auth_provider')
                    ->options(['email' => 'Email', 'google' => 'Google'])
                    ->default('email')
                    ->required(),
            ]);
    }
}
