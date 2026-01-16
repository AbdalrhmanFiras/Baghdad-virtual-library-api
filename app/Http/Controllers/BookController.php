<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResources;
use App\Http\Requests\StoreBookRequest;
/**
 * @tags Books EndPoint
 */
class BookController extends Controller
{
    /**
     * Create Book
     */
    public function store(StoreBookRequest $request){

    $data = $request->validated();
    if($request->hasFile('pdf_read')){
        $pdf_read_path = $request->file('pdf')->store('books/read','public');
          $data['pdf_read'] = $pdf_read_path;
    }
    if($request->hasFile('pdf_download')){
        $pdf_download_path = $request->file('pdf_download')->store('books/download', 'public');
         $data['pdf_download'] = $pdf_download_path;
    }
  
    $book = Book::create($data);
    return $this->responseSuccess(new BookResources($book), 'Book uploaded successfully', 201);

    }
}