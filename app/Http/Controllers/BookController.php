<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Enum\BookStatusEnum;
use Illuminate\Http\Request;
use App\Helper\FileUploadHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BookResource;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

 /**
 * @tags Books EndPoint
 */
class BookController extends Controller
{
    /**
     * Create Book
     */
 public function store(StoreBookRequest $request)
{
    $data = $request->validated();
    $data['status'] = BookStatusEnum::Draft->value;
    if(!Author::find($data['author_id'])){
            return $this->responseError(null,'Author not found ' , 404);
    }
   if ($request->hasFile('pdf_read')) {
    $data['pdf_read'] = $request->file('pdf_read')->store('books/read', 'public');
    $data['is_readable'] = true;
    } else 
    {
        $data['is_readable'] = false;
    }

    if ($request->hasFile('pdf_download')) {
        $data['pdf_download'] = $request->file('pdf_download')->store('books/download', 'public');
        $data['is_downloadable'] = true;
    } else {
        $data['is_downloadable'] = false;
    }

    if ($request->hasFile('audio')) {
        $data['audio'] = $request->file('audio')->store('books/saudio', 'public');
        $data['has_audio'] = true;
    } else {
        $data['has_audio'] = false;
    }
    $file = $request->hasFile('image') ? $request->file('image') : null;
    $categories = $data['categories'] ?? [];
    unset($data['categories']);
    $book = Book::create($data);
    $path = FileUploadHelper::ImageUpload($file ,'books' , 'images', 'public');
    $book->image()->create([
        'url' => $path,
        'type' => 'books',
    ]);
    if (!empty($categories)) {
        $book->categories()->sync($categories);
    }
    return $this->responseSuccess(
        new BookResource($book->load('categories','image')),
        'Book uploaded successfully',
        201
    );
}

    public function update(UpdateBookRequest $request , $id)
    {
        try{
        $data = $request->validated();
        $book = Book::with(['image' , 'categories'])->where('id' , $id)->firstOrFail();
    
        DB::transaction(function () use ($request, $book, $data) {
            $book->update($data);
            if(isset($data['categories'])){
                $book->categories()->sync($data['categories']);
            }

         if($request->hasFile('image')){
            $file = $request->file('image'); 
    
        $path = FileUploadHelper::ImageUpload($file  , 'books' ,'images');
        FileUploadHelper::UpdateImage($book ,$path );
         }
         });
        return response()->json([
            'message' => 'Book updated successfully',
            'data'    => new BookResource($book->fresh('image' , 'categories')),
        ], 200);
    }catch(ModelNotFoundException){
        return $this->responseError(null,'Book not found' , 404);
    }
    }

}