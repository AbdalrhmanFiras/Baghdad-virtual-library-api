<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Models\News;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $news = News::create(
            collect($data)->except('image')->toArray()
        );

        if (! empty($data['image'])) {
            $news->image()->create([
                'url' => $data['image'],
                'type' => 'news',
            ]);
        }

        return $news;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
