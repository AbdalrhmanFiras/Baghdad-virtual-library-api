<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Main Info')
                ->description('Basic details about the book')
                ->columns(1)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('title')->label('Title')->required()->columnSpanFull(),
                    TextInput::make('publish_year')->label('Publish Year')->placeholder('Enter Year')->numeric()->required()->columnSpanFull(),
                    Select::make('author_id')
                        ->label('Author')
                        ->relationship('author', 'author_name')
                        ->preload()
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('language')->label('Language')->required()->columnSpanFull(),
                    RichEditor::make('dec')->label('Description')->placeholder('Enter Description')->columnSpanFull(),
                    Select::make('status_case')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'archived' => 'Archived',
                        ])

                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Categories & Rating')
                ->description('Select categories and set rating')
                ->columns(1)
                ->columnSpanFull()
                ->schema([
                    Select::make('categories')
                        ->label('Categories')
                        ->multiple()
                        ->relationship('categories', 'name')
                        ->preload()
                        ->columnSpanFull(),
                    TextInput::make('rating')
                        ->label('Rating (Stars)')
                        ->minValue(1)
                        ->maxValue(5)
                        ->step(0.1)
                        ->default(1)
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Files & Media')
                ->description('Upload files and cover image')
                ->columns(1)
                ->columnSpanFull()
                ->schema([
                    FileUpload::make('pdf_download')
                        ->label('Downloadable PDF/Image')
                        ->disk('s3-private')
                        ->directory('books/download')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpeg'])
                        ->previewable(false)
                        ->openable(false)
                        ->downloadable(false)
                        ->columnSpanFull(),

                    FileUpload::make('pdf_read')
                        ->label('Readable PDF/Image')
                        ->disk('s3-private')
                        ->directory('books/read')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpeg'])
                        ->previewable(false)
                        ->openable(false)
                        ->downloadable(false)
                        ->columnSpanFull(),

                    FileUpload::make('audio')
                        ->label('Audio')
                        ->disk('s3-private')
                        ->directory('books/audio')
                        ->visibility('private')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/wav'])
                        ->previewable(false)
                        ->openable(false)
                        ->downloadable(false)
                        ->columnSpanFull(),

                    FileUpload::make('cover_image')
                        ->label('Cover Image')
                        ->disk('s3-private')
                        ->directory('books/images')
                        ->image()
                        ->previewable(true)
                        ->openable(true)
                        ->downloadable(false)
                        ->columnSpanFull()
                        ->visible(fn (string $context) => in_array($context, ['create', 'view'])),

                ]),

        ]);
    }
}
