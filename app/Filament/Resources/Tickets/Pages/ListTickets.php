<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Enums\TicketStatusEnum;
use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public function getTabs(): array
    {
        return [
            'open' => Tab::make()
                ->badge(Ticket::query()->where('status', TicketStatusEnum::OPEN->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatusEnum::OPEN->value)),
            'in_progress' => Tab::make()
                ->badge(Ticket::query()->where('status', TicketStatusEnum::IN_PROGRESS->value)->count())->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatusEnum::IN_PROGRESS->value)),
            'closed' => Tab::make()
                ->badge(Ticket::query()->where('status', TicketStatusEnum::CLOSED->value)->count())->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatusEnum::CLOSED->value)),

        ];

    }
}
