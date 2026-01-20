<?php

namespace App\Http\Controllers;

use App\Enum\BookStatusEnum;
use App\Helper\FileHelper;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

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
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        DB::beginTransaction();

        try {
            if (! Author::find($data['author_id'])) {
                return $this->responseError(null, 'Author not found.', 404);
            }

            $existsQuery = Book::where('title', $data['title'])
                ->where('author_id', $data['author_id'])
                ->where('publish_date', $data['publish_date'] ?? null);

            if ($existsQuery->exists()) {
                return $this->responseError(null, 'This book already exists', 200);
            }

            if ($path = FileHelper::storeIfExists($request, 'pdf_read', 'books/read', 's3-private')) {
                $data['pdf_read'] = $path;
                $data['is_readable'] = true;
            } else {
                $data['is_readable'] = false;
            }

            if ($path = FileHelper::storeIfExists($request, 'pdf_download', 'books/download', 's3-private')) {
                $data['pdf_download'] = $path;
                $data['is_downloadable'] = true;
            } else {
                $data['is_downloadable'] = false;
            }

            if ($path = FileHelper::storeIfExists($request, 'audio', 'books/audio', 's3-private')) {
                $data['audio'] = $path;
                $data['has_audio'] = true;
            } else {
                $data['has_audio'] = false;
            }

            $book = Book::create($data);

            $file = $request->file('image') ?? null;
            if ($file) {
                $path = FileHelper::ImageUpload($file, 'books', 'images', 's3-private');

                if (! $path) {
                    throw new \Exception('Image upload failed');
                }

                $book->image()->create([
                    'url' => $path,
                    'type' => 'books',
                ]);
            }

            if (! empty($categories)) {
                $book->categories()->sync($categories);
            }
            DB::commit();

            return $this->responseSuccess(
                new BookResource($book->load('categories')),
                'Book uploaded successfully.',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($book)) {
                FileHelper::DeleteBookStuff($book, 's3-private');
            }

            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Update Book
     *
     * `Admin(only)`
     */
    public function update(UpdateBookRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $book = Book::getBook($id)->firstOrFail();

            DB::transaction(function () use ($request, $book, $data) {
                $fileData = FileHelper::updateBookFiles($book, $request);
                $book->update(array_merge($data, $fileData));
                if (isset($data['categories'])) {
                    $book->categories()->sync($data['categories']);
                }

                if ($request->hasFile('image')) {
                    $file = $request->file('image');

                    $path = FileHelper::ImageUpload($file, 'books', 'images');
                    FileHelper::UpdateImage($book, $path);
                }
            });

            return response()->json([
                'message' => 'Book updated successfully',
                'data' => new BookResource($book->fresh('image', 'categories')),
            ], 200);
        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'Book not found.', 404);
        }
    }

    /**
     * Get All Books
     */
    public function index()
    {

        $books = Book::paginate(10);
        if ($books->isEmpty()) {
            return $this->responseError(null, 'No books yet.');
        }

        return $this->responseSuccess([
            'data' => BookResource::collection($books), 'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ], 'Books fetched successfully.', 200);
    }

    /**
     * Get(show) Book
     */
    public function show($id)
    {

        try {
            $book = Book::with('comments')->getBook($id)->firstOrFail();

            return $this->responseSuccess(new BookResource($book), 'Book fetched successfully.', 200);
        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'Book not found.', 404);
        }
    }

    /**
     * Delete a Book
     *
     * `Admin(only)`
     */
    public function delete($id)
    {
        try {
            // remove the pdf too
            $book = Book::getBook($id)->firstOrFail();
            FileHelper::DeleteBookStuff($book);
            $book->categories()->detach(); // remove from the pivot table

            $book->delete();

            return $this->responseSuccess(null, 'Book deleted successfully.', 200);

        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'Book not found.', 404);
        }
    }
}
