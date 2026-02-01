<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable()->sortable()->badge(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('publish_year')->label('Published')->searchable(),
                TextColumn::make('language')->label('Language')->searchable(),
                TextColumn::make('author.author_name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status_case')->label('Status')->badge()->colors([
                    'primary' => 'Draft',
                    'success' => 'Published',
                    'danger' => 'Archived',
                ])->sortable(),
                ViewColumn::make('image_preview')
                    ->label('Cover')
                    ->view('tables.columns.image-hover')
                    ->toggleable(),
            ])

            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
