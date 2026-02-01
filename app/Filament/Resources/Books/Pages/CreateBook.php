<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use App\Models\Book;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $book = Book::create($data);

        if (! empty($data['categories'])) {
            $book->categories()->sync($data['categories']);
        }

        if (! empty($data['image'])) {
            $book->image()->create([
                'url' => $data['image'],
                'type' => 'books',
            ]);
        }

        return $book;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
