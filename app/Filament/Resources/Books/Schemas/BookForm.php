<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

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
                        ->acceptedFileTypes(['application/pdf'])
                        ->previewable(false)
                        ->openable(false)
                        ->downloadable(true)
                        ->preserveFilenames()
                        ->columnSpanFull()
                        ->dehydrated(fn ($state) => filled($state))
                        ->saveUploadedFileUsing(function ($file, $set, $get, $record) {

                            if ($record && $record->pdf_download) {
                                Storage::disk('s3-private')->delete($record->pdf_download);
                            }

                            $path = $file->store('books/download', 's3-private');

                            $set('pdf_download', $path);

                            return $path;

                        }),

                    FileUpload::make('pdf_read')
                        ->label('Readable PDF/Image')
                        ->disk('s3-private')
                        ->directory('books/read')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf'])
                        ->previewable(false)
                        ->openable(true)
                        ->downloadable(false)
                        ->columnSpanFull()
                        ->preserveFilenames()
                        ->dehydrated(fn ($state) => filled($state))
                        ->saveUploadedFileUsing(function ($file, $set, $get, $record) {

                            if ($record && $record->pdf_read) {
                                Storage::disk('s3-private')->delete($record->pdf_read);
                            }

                            $path = $file->store('books/read', 's3-private');

                            $set('pdf_read', $path);

                            return $path;
                        }), // done
                    FileUpload::make('audio')
                        ->label('Audio')
                        ->disk('s3-private')
                        ->directory('books/audio')
                        ->visibility('private')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/wav'])
                        ->columnSpanFull()
                        ->dehydrated(fn ($state) => filled($state))
                        ->saveUploadedFileUsing(function ($file, $set, $record) {

                            if ($record && $record->audio) {
                                Storage::disk('s3-private')->delete($record->audio);
                            }
                            $path = $file->store('books/audio', 's3-private');

                            $set('audio', $path);

                            return $path;
                        }),

                    FileUpload::make('cover_image')
                        ->label('Cover Image')
                        ->image()
                        ->disk('s3-private')
                        ->directory('books/images')
                        ->preserveFilenames()
                        ->previewable(fn (string $context) => $context === 'create')->openable(true)
                        ->downloadable(false)
                        ->columnSpanFull()
                        ->dehydrated(fn ($state) => filled($state))
                        ->saveUploadedFileUsing(function ($file, $set, $get, $record) {

                            if ($record && $record->image->url) {
                                Storage::disk('s3-private')->delete($record->image->url);
                            }

                            $path = $file->store('books/images', 's3-private');
                            $set('cover_image', $path);

                            return $path;
                        }),
                    ViewField::make('current_cover_image')
                        ->label('Current Cover Image')
                        ->view('tables.columns.image')
                        ->viewData(function ($record) {
                            if (! $record || ! $record->image) {
                                return ['url' => null];
                            }

                            return [
                                'url' => Storage::disk('s3-private')->temporaryUrl($record->image->url, now()->addMinutes(10)),
                            ];
                        })
                        ->hiddenOn('create'),

                ]),

        ]);
    }
}
