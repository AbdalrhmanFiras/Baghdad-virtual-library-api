<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Groups;
use App\Models\News;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Books', Book::count())
                ->description('All registered books')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),
            Stat::make('Total Users', User::count())
                ->description('Registered library users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total News', News::count())
                ->description('Active news entries')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('warning'),
            Stat::make('Total Groups', Groups::count())
                ->description('Active study/interest groups')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('danger'),
        ];
    }
}
