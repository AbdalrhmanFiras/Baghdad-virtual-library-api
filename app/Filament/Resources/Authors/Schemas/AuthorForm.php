<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Author Details')->schema([
                    TextInput::make('author_name')
                        ->label('Name')
                        ->placeholder('Author name')->required()->maxLength(255),
                    RichEditor::make('dec')
                        ->label('Description')

                        ->placeholder('Enter description')
                        ->columnSpanFull()
                        ->required(),
                ])->columnSpanFull(),
            ]);
    }
}
