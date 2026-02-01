<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable()->sortable()->badge()->toggleable(),
                TextColumn::make('title')->searchable()->toggleable(),
                TextColumn::make('publish_year')->label('Published')->searchable()->toggleable(),
                TextColumn::make('language')->label('Language')->searchable()->toggleable(),
                TextColumn::make('author.author_name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status_case')->label('Status')->badge()->colors([
                    'primary' => 'Draft',
                    'success' => 'Published',
                    'danger' => 'Archived',
                ])->sortable()->toggleable(),
                ViewColumn::make('image_preview')
                    ->label('Cover')
                    ->view('tables.columns.image-hover')
                    ->toggleable()->alignLeft(),

                IconColumn::make('pdf_read')
                    ->label('Readable')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->colors([
                        'success' => fn ($record) => ! empty($record->pdf_read),
                    ])->toggleable()->alignCenter(),

                IconColumn::make('pdf_download')
                    ->label('Downloadable')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-down-tray')
                    ->colors([
                        'success' => fn ($record) => ! empty($record->pdf_download),
                    ])->toggleable()->alignCenter(),

                IconColumn::make('audio')
                    ->label('Audoi')
                    ->boolean()
                    ->trueIcon('heroicon-o-speaker-wave')
                    ->colors([
                        'success' => fn ($record) => ! empty($record->audio),
                    ])->toggleable()->alignCenter(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(function ($state) {
                        $fullStars = floor($state);
                        $halfStar = ($state - $fullStars) >= 0.5 ? 1 : 0;
                        $emptyStars = 5 - $fullStars - $halfStar;

                        return str_repeat('⭐', $fullStars)
                            .($halfStar ? '✬' : '')
                            .str_repeat('☆', $emptyStars);
                    })
                    ->badge()
                    ->colors([
                        'danger' => fn ($state) => $state <= 2,
                        'warning' => fn ($state) => $state == 3,
                        'success' => fn ($state) => $state > 3,
                    ])->alignCenter()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('dec')
                    ->label('Description')->limit(50)->wrap()->alignCenter()->alignLeft()
                    ->formatStateUsing(fn ($state) => strip_tags($state))
                    ->tooltip(fn ($record) => strip_tags($record->dec))->toggleable(),

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
