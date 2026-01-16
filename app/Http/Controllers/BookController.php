<?php

namespace App\Http\Controllers;

use App\Enum\BookStatusEnum;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;

use App\Http\Requests\StoreBookRequest;
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

    $data['pdf_read'] = $request->hasFile('pdf_read') 
    ? $request->file('pdf_read')->store('books/read', 'public') 
    : null;

    $data['is_readable'] = $request->hasFile('pdf_read');

    $data['pdf_download'] = $request->hasFile('pdf_download') 
    ? $request->file('pdf_download')->store('books/download', 'public') 
    : null;

    $data['is_downloadable'] = $request->hasFile('pdf_download');

    $data['audio'] = $request->hasFile('audio') 
    ? $request->file('audio')->store('books/audio', 'public') 
    : null;

    $data['has_audio'] = $request->hasFile('audio');

    $book = Book::create($data);

    return $this->responseSuccess(
        new BookResource($book),
        'Book uploaded successfully',
        201
    );
}
}