<?php

namespace App\Filament\Resources\Books\Pages;

use App\Enums\BookStatusEnum;
use App\Filament\Resources\Books\BookResource;
use App\Models\Book;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge(Book::query()->count()),
            'draft' => Tab::make()
                ->badge(Book::query()->where('status_case', BookStatusEnum::Draft)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_case', BookStatusEnum::Draft)),
            'published' => Tab::make()
                ->badge(Book::query()->where('status_case', BookStatusEnum::Published)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_case', BookStatusEnum::Published)),
            'archived' => Tab::make()
                ->badge(Book::query()->where('status_case', BookStatusEnum::Archived)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_case', BookStatusEnum::Archived)),
        ];
    }
}
