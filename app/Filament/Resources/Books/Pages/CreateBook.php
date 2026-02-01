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
        // إنشاء الكتاب مع كل الـ paths المرفوعة
        $book = Book::create([
            'title' => $data['title'],
            'author_id' => $data['author_id'],
            'dec' => $data['dec'],
            'publish_year' => $data['publish_year'],
            'language' => $data['language'],
            'status_case' => $data['status_case'] ?? 'Draft',
            'pdf_read' => $data['pdf_read'] ?? null,
            'pdf_download' => $data['pdf_download'] ?? null,
            'audio' => $data['audio'] ?? null,
        ]);

        // ربط الفئات
        if (! empty($data['categories'])) {
            $book->categories()->sync($data['categories']);
        }

        // الصورة
        if (! empty($data['image'])) {
            $book->image()->create([
                'url' => $data['image'],
                'type' => 'books',
            ]);
        }

        return $book;
    }
}
