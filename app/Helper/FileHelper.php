<?php

namespace App\Helper;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileHelper {
    //books/images/uuid.ext
    public static function ImageUpload($file,string $type , string $content= null ,$disk = 'public'){

        $ext = $file->extension();
        $filename = (string) Str::uuid() . '.' . $ext;
        if($content){
        $subdir = "{$type}/{$content}";
    }else{
        $subdir = "{$type}";
    }
        return $file->storeAs($subdir,$filename,$disk);
    }

     public static function UpdateImage($model,string $path , $disk = 'public')
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

     public static function DeleteBookStuff($model , $disk = 'public'){

        if($model->image){
            if($model->image->url && Storage::disk($disk)->exists($model->image->url)){
                Storage::disk($disk)->delete($model->image->url);
            }
        }
             if ($model->pdf_read && Storage::disk($disk)->exists($model->pdf_read)) {
            Storage::disk($disk)->delete($model->pdf_read);
        }
            if($model->audio && Storage::disk($disk)->exists($model->audio)){
                Storage::disk($disk)->delete($model->audio);

            }
               if ($model->pdf_download && Storage::disk($disk)->exists($model->pdf_download)) {
                    Storage::disk($disk)->delete($model->pdf_download);
                 }
     }


    public static function updateBookFiles($model, Request $request, $disk = 'public'): array
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

        $data['pdf_download'] = $request->file('pdf_download')->store('books/download', 'public');
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

     public static function storeIfExists(Request $request, string $field, string $path, $disk = 'public'): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }
        return $request->file($field)->store($path, $disk);
    }



}