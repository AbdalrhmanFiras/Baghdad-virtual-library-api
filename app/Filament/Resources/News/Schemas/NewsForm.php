<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('New detalis')->schema([
                    TextInput::make('title')->placeholder('Enter title'),
                    RichEditor::make('dec')->label('Description')->placeholder('Enter Description')
                        ->columnSpanFull(),
                    FileUpload::make('image')
                        ->label('News image')
                        ->image()
                        ->visibility('private')

                        ->disk('s3-private')
                        ->directory('news')
                        ->previewable()
                        ->preserveFilenames()->required()->columnSpanFull(),
                ])->columns(2)->columnSpanFull(),
            ]);
    }
}
