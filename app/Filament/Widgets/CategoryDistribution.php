<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategoryDistribution extends ChartWidget
{
    protected ?string $heading = 'Books by Category';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $categories = Category::withCount('books')
            ->orderByDesc('books_count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Books',
                    'data' => $categories->pluck('books_count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#32CD32',
                        '#8B008B',
                        '#FF4500',
                        '#2F4F4F',
                    ],
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
