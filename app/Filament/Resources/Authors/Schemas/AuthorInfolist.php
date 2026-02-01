<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Author Details')
                ->description('Basic information about the author')
                ->schema([
                    TextEntry::make('author_name')
                        ->label('Name')
                        ->icon('heroicon-o-user')
                        ->placeholder('Author Name'),

                    TextEntry::make('dec')
                        ->label('Description')
                        ->icon('heroicon-o-document-text')
                        ->formatStateUsing(fn ($state) => strip_tags($state))
                        ->placeholder('Author description goes here')
                        ->columns(1)
                        ->extraAttributes(['class' => 'break-words']),
                ])
                ->columnSpanFull(1),
        ]);
    }
}
