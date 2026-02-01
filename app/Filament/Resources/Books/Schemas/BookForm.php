<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('title')->required(),

            TextInput::make('publish_year')
                ->numeric()
                ->required(),

            Select::make('author_id')
                ->relationship('author', 'author_name')
                ->preload()
                ->required(),

            RichEditor::make('dec'),

            TextInput::make('language')->required(),

            FileUpload::make('pdf_download')
                ->disk('s3-private')
                ->directory('books/download')
                ->visibility('private')
                ->previewable(false)
                ->openable(false)
                ->downloadable(false),

            FileUpload::make('audio')
                ->disk('s3-private')
                ->directory('books/audio')
                ->visibility('private')
                ->previewable(false)
                ->openable(false)
                ->downloadable(false),

            FileUpload::make('image')
                ->disk('s3-private')
                ->directory('books/images')
                ->visibility('private')
                ->image()
                ->previewable(false)
                ->openable(false)
                ->downloadable(false),

            Select::make('categories')
                ->multiple()
                ->relationship('categories', 'name')
                ->preload(),

            Select::make('status_case')
                ->options([
                    'Draft' => 'Draft',
                    'Published' => 'Published',
                ])
                ->default('Draft')
                ->required(),
        ]);
    }
}
