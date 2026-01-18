<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Helper\FileHelper;
use App\Enum\BookStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BookResource;
use App\Http\Requests\StoreBookRequest;
use Illuminate\Support\Facades\Storage;
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
     * 
     * `Admin(only)`
     */
 public function store(StoreBookRequest $request)
{
    $data = $request->validated();
    $data['status'] = BookStatusEnum::Draft->value;
    if(!Author::find($data['author_id'])){
            return $this->responseError(null,'Author not found.' , 404);
    }
    if(Book::where('pdf_read')->exists()||Book::where('pdf_download')->exists()||Book::where('audio')->exists()){
        return $this->responseError(null,'This book already exists',200);
    }
    if ($path = FileHelper::storeIfExists($request, 'pdf_read', 'books/read','public')) {
        $data['pdf_read'] = $path;
        $data['is_readable'] = true;
    } else {
        $data['is_readable'] = false;
    }

    if ($path = FileHelper::storeIfExists($request, 'pdf_download', 'books/download','public')) {
        $data['pdf_download'] = $path;
        $data['is_downloadable'] = true;
    } else {
        $data['is_downloadable'] = false;
    }

    if ($path = FileHelper::storeIfExists($request, 'audio', 'books/audio','public')) {
        $data['audio'] = $path;
        $data['has_audio'] = true;
    } else {
        $data['has_audio'] = false;
    }
    $file = $request->hasFile('image') ? $request->file('image') : null;
    $categories = $data['categories'] ?? [];
    unset($data['categories']);
    $book = Book::create($data);
    $path = FileHelper::ImageUpload($file ,'books' , 'images', 'public');
    $book->image()->create([
        'url' => $path,
        'type' => 'books',
    ]);
    if (!empty($categories)) {
        $book->categories()->sync($categories);
    }
    return $this->responseSuccess(
        new BookResource($book->load('categories','image')),
        'Book uploaded successfully.',
        201
    );
    }

    /**
     * Update Book
     * 
     * `Admin(only)`
     */
    public function update(UpdateBookRequest $request , $id)
    {
    
        try{
        $data = $request->validated();
        $book = Book::getBook($id)->firstOrFail();
    
        DB::transaction(function () use ($request, $book, $data) {
            $fileData = FileHelper::updateBookFiles($book, $request,'public');
            $book->update(array_merge($data, $fileData));
            if(isset($data['categories'])){
                $book->categories()->sync($data['categories']);
            }

         if($request->hasFile('image')){
            $file = $request->file('image'); 
    
        $path = FileHelper::ImageUpload($file  , 'books' ,'images','public');
        FileHelper::UpdateImage($book ,$path,'public' );
         }
         });
        return response()->json([
            'message' => 'Book updated successfully',
            'data'    => new BookResource($book->fresh('image' , 'categories')),
        ], 200);
    }catch(ModelNotFoundException){
        return $this->responseError(null,'Book not found.' , 404);
    }
     }
     /**
       * Get All Books
       */
        public function index(){
        
            $books = Book::paginate(10);
            if($books->isEmpty()){
                return $this->responseError(null , 'No books yet.');
            }
           return $this->responseSuccess([
            'data' => BookResource::collection($books) , 'meta' => [
            'current_page' => $books->currentPage(),
            'last_page'    => $books->lastPage(),
            'per_page'     => $books->perPage(),
            'total'        => $books->total(),
        ]
        ],'Books fetched successfully.' , 200);
        }
      /**
       * Get(show) Book
       */
        public function show($id){
        
            try{
                $book = Book::getBook($id)->firstOrFail();
                return $this->responseSuccess(new BookResource($book),'Book fetched successfully.' , 200);
            }catch(ModelNotFoundException){
                return $this->responseError(null,'Book not found.' , 404);
            }
        }

       /**
       * Delete a Book
       * 
       * `Admin(only)`
       */
        public function delete($id){        
              try {
                // remove the pdf too 
             $book = Book::getBook($id)->firstOrFail();
                  FileHelper::DeleteBookStuff($book,'public');
                 $book->categories()->detach();// remove from the pivot table 

                $book->delete();

              return $this->responseSuccess(null, 'Book deleted successfully.', 200);

          } catch (ModelNotFoundException) {
                return $this->responseError(null, 'Book not found.', 404);
         }
        }

}