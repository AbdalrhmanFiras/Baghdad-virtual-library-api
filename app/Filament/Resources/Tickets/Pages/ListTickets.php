<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Enums\TicketStatusEnum;
use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Query\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public function getTabs(): array
    {
        return [
            'open' => Tab::make()
                ->badge(Ticket::query()->where('status', 'open')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatusEnum::OPEN->value)),
            'in_progress' => Tab::make()
                ->badge(Ticket::query()->where('status', 'in_progress')->count())->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatusEnum::IN_PROGRESS->value)),
            'closed' => Tab::make()
                ->badge(Ticket::query()->where('status', 'closed')->count())->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatusEnum::CLOSED->value)),

        ];

    }
}
