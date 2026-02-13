<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable()->sortable()->badge()->toggleable(false),
                TextColumn::make('title')->searchable()->toggleable(false),
                TextColumn::make('status')->label('Status')->badge()->colors([
                    'success' => fn ($state) => $state === 'open',
                    'primary' => fn ($state) => $state === 'in_progress',
                    'yellow' => fn ($state) => $state === 'closed',
                ])->sortable()->toggleable(),
                TextColumn::make('type')->label('Type')->searchable()->toggleable(),
                TextColumn::make('user.name')->label('User')->sortable()->searchable()->toggleable(false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('closed')->action(fn (Ticket $record) => $record->update(['status' => 'closed'])),
                Action::make('in_progress')->action(fn (Ticket $record) => $record->update(['status' => 'in_progress'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
