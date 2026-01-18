<?php

namespace App\Helper;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FileUploadHelper {
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
                Storage::disk('public')->delete($model->image->url);
                $model->image->update([
                    'url' => $path,
                ]);
            } else {
                $model->image()->create([
                    'url' => $path,
                ]);
            }
     }







}