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
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                TextInput::make('publish_year')
                    ->label('Published')->numeric()
                    ->required(),
                Select::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'author_name')
                    ->preload(),
                RichEditor::make('dec')
                    ->label('Description')

                    ->placeholder('Enter book description'),
                TextInput::make('language')
                    ->required(),

                FileUpload::make('pdf_download')
                    ->label('Pdf (download)')
                    ->disk('s3-private')
                    ->directory('books/download')
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->saveUploadedFileUsing(function ($file, $set) {
                        $filename = \Illuminate\Support\Str::uuid().'.'.$file->getClientOriginalExtension();
                        $path = $file->storeAs('books/download', $filename, 's3-private');
                        $set($path);

                        return $path;
                    }),

                FileUpload::make('audio')
                    ->label('Audio (listen)')
                    ->disk('s3-private')
                    ->directory('books/audio')
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->saveUploadedFileUsing(function ($file, $set) {
                        $filename = \Illuminate\Support\Str::uuid().'.'.$file->getClientOriginalExtension();
                        $path = $file->storeAs('books/audio', $filename, 's3-private');
                        $set($path);

                        return $path;
                    }),

                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->disk('s3-private')
                    ->directory('books/images')
                    ->enableOpen()
                    ->maxSize(10240)
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->saveUploadedFileUsing(function ($file, $set) {
                        $filename = \Illuminate\Support\Str::uuid().'.'.$file->getClientOriginalExtension();
                        $path = $file->storeAs('books/images', $filename, 's3-private');
                        $set($path);

                        return $path;
                    }),
                Select::make('categories')
                    ->label('Category')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload(),
                Select::make('status_case')
                    ->label('Status')
                    ->options([
                        'Draft' => 'Draft',
                        'Published' => 'Published',
                    ])// try 1
                    ->default('Draft')
                    ->required(),

            ]);
    }
}
