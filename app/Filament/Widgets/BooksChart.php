<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;

class BooksChart extends ChartWidget
{
    protected ?string $heading = 'Books Created';

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $data = Book::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months with 0
        $counts = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthNum = (int) $month->format('n');
            $counts[] = $data[$monthNum] ?? 0;
            $labels[] = $month->format('M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Books Created',
                    'data' => $counts,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
