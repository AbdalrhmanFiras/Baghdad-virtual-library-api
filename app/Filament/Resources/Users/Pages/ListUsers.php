<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'user' => Tab::make()
                ->badge(User::query()->where('role', UserRoleEnum::User)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', UserRoleEnum::User)),
            'admin' => Tab::make()
                ->badge(User::query()->where('role', UserRoleEnum::Admin)->count())->modifyQueryUsing(fn (Builder $query) => $query->where('role', UserRoleEnum::Admin)),

        ];

    }
}
