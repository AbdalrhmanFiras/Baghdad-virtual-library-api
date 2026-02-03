<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User detalis')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')
                        ->label('Email address'),
                    TextEntry::make('email_verified_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('google_id')
                        ->placeholder('-'),
                    TextEntry::make('auth_provider')
                        ->badge(),
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('updated_at')
                        ->dateTime()
                        ->placeholder('-'),
                ])->columns(2)->columnSpanFull(),
            ]);
    }
}
