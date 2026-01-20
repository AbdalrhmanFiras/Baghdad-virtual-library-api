<?php

namespace App\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileHelper
{
    // books/images/uuid.ext
    public static function ImageUpload($file, string $type, ?string $content, $disk = 's3-private')
    {

        if (! $file) {
            return null;
        }
        $ext = $file->extension();
        $filename = (string) Str::uuid().'.'.$ext;
        if ($content) {
            $subdir = "{$type}/{$content}";
        } else {
            $subdir = "{$type}";
        }

        return $file->storeAs($subdir, $filename, $disk);
    }

    public static function UpdateImage($model, string $path, $disk = 's3-private')
    {
        if ($model->image) {
            Storage::disk($disk)->delete($model->image->url);
            $model->image->update([
                'url' => $path,
            ]);
        } else {
            $model->image()->create([
                'url' => $path,
            ]);
        }
    }

    public static function DeleteImage($model, $disk = 's3-private')
    {
        if ($model->image && $model->image->url) {
            if (Storage::disk($disk)->exists($model->image->url)) {
                Storage::disk($disk)->delete($model->image->url);
            }
        }
    }

    public static function DeleteBookStuff($model, $disk = 's3-private')
    {

        if ($model->image) {
            if ($model->image->url && Storage::disk($disk)->exists($model->image->url)) {
                Storage::disk($disk)->delete($model->image->url);
            }
        }
        if ($model->pdf_read && Storage::disk($disk)->exists($model->pdf_read)) {
            Storage::disk($disk)->delete($model->pdf_read);
        }
        if ($model->audio && Storage::disk($disk)->exists($model->audio)) {
            Storage::disk($disk)->delete($model->audio);

        }
        if ($model->pdf_download && Storage::disk($disk)->exists($model->pdf_download)) {
            Storage::disk($disk)->delete($model->pdf_download);
        }
    }

    public static function updateBookFiles($model, Request $request, $disk = 's3-private'): array
    {
        $data = [];
        if ($request->hasFile('pdf_read')) {
            if ($model->pdf_read && Storage::disk($disk)->exists($model->pdf_read)) {
                Storage::disk($disk)->delete($model->pdf_read);
            }

            $data['pdf_read'] = $request->file('pdf_read')->store('books/read', $disk);
            $data['is_readable'] = true;
        }

        if ($request->hasFile('pdf_download')) {
            if ($model->pdf_download && Storage::disk($disk)->exists($model->pdf_download)) {
                Storage::disk($disk)->delete($model->pdf_download);
            }

            $data['pdf_download'] = $request->file('pdf_download')->store('books/download', $disk);
            $data['is_downloadable'] = true;
        }

        if ($request->hasFile('audio')) {
            if ($model->audio && Storage::disk($disk)->exists($model->audio)) {
                Storage::disk($disk)->delete($model->audio);
            }

            $data['audio'] = $request->file('audio')->store('books/audio', $disk);
            $data['has_audio'] = true;
        }

        return $data;
    }

    public static function storeIfExists(Request $request, string $field, string $path, $disk = 's3-private'): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store($path, $disk);
    }

    public static function streamFile(string $path, string $contentType, string $disposition = 'inline'): StreamedResponse
    {

        if (! Storage::disk('s3-private')->exists($path)) {
            abort(404, 'File not found.');
        }

        return new StreamedResponse(function () use ($path) {
            echo Storage::disk('s3-private')->get($path);
        }, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "$disposition; filename=\"".basename($path).'"',
        ]);
    }
}
