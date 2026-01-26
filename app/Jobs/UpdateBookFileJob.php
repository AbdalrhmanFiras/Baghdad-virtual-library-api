<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UpdateBookFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Book $book;

    public array $files;

    public string $disk;

    /**
     * Create a new job instance.
     */
    public function __construct(Book $book, array $files, string $disk = 's3-private')
    {
        $this->book = $book;
        $this->files = $files;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [];

        foreach ($this->files as $field => $file) {
            if (! $file) {
                continue;
            }

            if ($this->book->$field && Storage::disk($this->disk)->exists($this->book->$field)) {
                Storage::disk($this->disk)->delete($this->book->$field);
            }

            $path = $file->store("books/{$field}", $this->disk);
            $data[$field] = $path;

            if ($field === 'pdf_read') {
                $data['is_readable'] = true;
            }
            if ($field === 'pdf_download') {
                $data['is_downloadable'] = true;
            }
            if ($field === 'audio') {
                $data['has_audio'] = true;
            }
        }

        if (! empty($data)) {
            $this->book->update($data);
        }
    }
}
