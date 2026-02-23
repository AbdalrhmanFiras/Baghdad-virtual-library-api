<?php

namespace App\Http\Controllers;

use App\Enums\BookFlagsEnum;
use App\Enums\BookStatusEnum;
use App\Enums\UserBookEnum;
use App\Helper\FileHelper;
use App\Http\Requests\AddBookFlagRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @tags Books Endpoint
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
        $data['status_case'] = BookStatusEnum::Draft->value;
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        DB::beginTransaction();

        try {

            if (! Author::find($data['author_id'])) {
                return $this->responseError(null, 'Author not found.', 404);
            }
            $existsQuery = Book::where('title', $data['title'])
                ->where('author_id', $data['author_id'])
                ->where('publish_year', $data['publish_year'] ?? null);

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
                new BookResource($book->load('categories', 'author')),
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

        $books = Book::with('flags')->paginate(10);
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
     * Get Trending Books
     */
    public function indexTrending()
    {

        $books = Book::with('flags')
            ->whereHas('flags', fn ($q) => $q->where('flag', BookFlagsEnum::Trending->value))
            ->paginate(10);
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
     * Get Recommended Books
     */
    public function indexRec()
    {

        $books = Book::with('flags')
            ->whereHas('flags', fn ($q) => $q->where('flag', BookFlagsEnum::Recommended->value))
            ->paginate(10);
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
     * Get Best Books
     */
    public function indexBest()
    {

        $books = Book::with('flags')
            ->whereHas('flags', fn ($q) => $q->where('flag', BookFlagsEnum::Best->value))
            ->paginate(10);
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
            $user = Auth::user();

            $book = Book::with('comments')->findOrFail($id);

            if ($user) {
                $userBook = $user->books()->where('books.id', $book->id)->first();
                $book->setRelation('pivot', $userBook?->pivot);
            }

            return $this->responseSuccess(
                new BookResource($book),
                'Book fetched successfully.',
                200
            );
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

            $book = Book::getBook($id)->firstOrFail();
            FileHelper::DeleteBookStuff($book);
            $book->categories()->detach(); // remove from the pivot table

            $book->delete();

            return $this->responseSuccess(null, 'Book deleted successfully.', 200);

        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'Book not found.', 404);
        }
    }

    /**
     * download the Book
     */
    public function streamPdfDownload(Book $book)
    {
        if (! $book->pdf_download) {
            abort(404, 'PDF not available.');
        }
        $book->increment('reads_count');

        return FileHelper::streamFile($book->pdf_download, 'application/pdf', 'attachment');
    }

    public function streamAudio(Book $book)
    {
        if (! $book->audio) {
            abort(404, 'Audio not available.');
        }

        return FileHelper::streamFile($book->audio, 'audio/mpeg', 'inline');
    }

    /**
     * Add the Book to Favorite
     */
    public function addBooktofav($bookId)
    {

        $user = Auth::user();

        if (! Book::where('id', $bookId)->exists()) {
            return $this->responseError(null, 'Book no longer exists', 404);
        }
        if ($user->books()->where('book_id', $bookId)->exists()) {
            $user->books()->updateExistingPivot($bookId, [
                'fav' => true,
            ]);
        } else {

            $user->books()->attach($bookId, [
                'fav' => true,
            ]);

        }

        return $this->responseSuccess(null, 'Book add to favorite', 201);
    }

    /**
     * Add the Book to To_Read list
     */
    public function addToRead($bookId)
    {
        $user = Auth::user();

        if (! Book::where('id', $bookId)->exists()) {
            return $this->responseError(null, 'Book no longer exists', 404);
        }

        if ($user->books()->where('book_id', $bookId)->exists()) {
            $user->books()->updateExistingPivot($bookId, [
                'to_read' => true,
            ]);
        } else {
            $user->books()->attach($bookId, [
                'to_read' => true,
            ]);
        }

        return $this->responseSuccess(null, 'Book added to read', 201);
    }

    /**
     * Track the Book progress
     *
     * track the number of page and handle the status of it
     */
    public function streamPdfTrack(Book $book)
    {
        $user = Auth::user();

        return FileHelper::streamFilepdf($book, $user);
    }

    /**
     * dont touch it
     *
     * `Admin(only)`
     */
    public function updatePagesRead(Book $book, $request)
    {
        $user = Auth::user();
        $pagesRead = (int) $request->input('pages_read');

        $pivot = $user->books()->find($book->id);

        if (! $pivot) {
            return response()->json(['message' => 'User has not started this book'], 400);
        }

        $totalPages = $pivot->pivot->total_pages ?? $pagesRead;

        $newPagesRead = min($pagesRead, $totalPages);

        $status = $newPagesRead >= $totalPages
            ? UserBookEnum::Completed->value
            : UserBookEnum::Reading->value;

        $user->books()->updateExistingPivot($book->id, [
            'pages_read' => $newPagesRead,
            'status' => $status,
        ]);

        return response()->json([
            'pages_read' => $newPagesRead,
            'total_pages' => $totalPages,
            'status' => $status,
        ]);
    }

    /**
     * Get Book from Favorite list
     */
    public function getFav()
    {
        $user = Auth::user();

        $books = $user->books()
            ->wherePivot('fav', true)
            ->paginate(10);

        if ($books->isEmpty()) {
            return $this->responseSuccess(null, 'No favorite books yet.', 404);
        }

        return $this->responseSuccess(['data' => BookResource::collection($books),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]], 'Favorite books fetched', 200);
    }

    /**
     * Remove Book from Favorite list
     */
    public function removeFav($bookId)
    {
        $user = Auth::user();

        if (! $user->books()
            ->wherePivot('fav', true)
            ->where('book_id', $bookId)
            ->exists()) {
            return $this->responseSuccess(null, 'Book not found.', 404);
        }

        $user->books()->updateExistingPivot($bookId, [
            'fav' => false]);

        return $this->responseSuccess(null, 'Book removed from favorite', 200);
    }

    /**
     * Get Book from To_read list
     */
    public function getToRead()
    {
        $user = Auth::user();

        $books = $user->books()
            ->wherePivot('to_read', true)  // مهم: pivot
            ->paginate(10);

        if ($books->isEmpty()) {
            return $this->responseSuccess(null, 'No books to read yet.', 404);
        }

        return $this->responseSuccess(['data' => BookResource::collection($books),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]], 'books fetched', 200);
    }

    /**
     * Remove Book from To_read list
     */
    public function removeToRead($bookId)
    {
        $user = Auth::user();

        if (! $user->books()
            ->wherePivot('to_read', true)
            ->where('book_id', $bookId)
            ->exists()) {
            return $this->responseSuccess(null, 'Book not found.', 404);
        }

        $user->books()->updateExistingPivot($bookId, [
            'to_read' => false]);

        return $this->responseSuccess(null, 'Book removed from to read list', 200);
    }

    /**
     * Get Reading Book
     */
    public function getReading()
    {
        $user = Auth::user();

        $books = $user->books()
            ->wherePivot('status', UserBookEnum::Reading->value)
            ->paginate(10);

        if ($books->isEmpty()) {
            return $this->responseSuccess(null, 'No Reading books yet.', 404);
        }

        return $this->responseSuccess(['data' => BookResource::collection($books),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]], 'Reading books fetched', 200);
    }

    /**
     * Get Completed Book
     */
    public function getComplete()
    {
        $user = Auth::user();

        $books = $user->books()
            ->wherePivot('status', UserBookEnum::Completed->value)
            ->paginate(10);

        if ($books->isEmpty()) {
            return $this->responseSuccess(null, 'No Completed books yet.', 404);
        }

        return $this->responseSuccess(['data' => BookResource::collection($books),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]], 'Completed books fetched', 200);
    }

    /**
     * Search Books
     */
    public function search()
    {

        $books = QueryBuilder::for(Book::class)
            ->allowedFilters([
                AllowedFilter::scope('author_name'),
                AllowedFilter::callback('publish_year_between', function ($query, $value) {
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween('publish_year', [$value[0], $value[1]]);
                    }
                }),
                AllowedFilter::exact('is_downloadable'),
                AllowedFilter::exact('has_audio'),
                AllowedFilter::partial('title'),
                AllowedFilter::partial('language'),
                AllowedFilter::exact('categories.id'),
                AllowedFilter::callback('rating_between', function ($query, $value) {
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween('rating', [$value[0], $value[1]]);
                    } else {
                        $query->where('rating', $value);
                    }
                }),
            ])
            ->allowedSorts(['title', 'publish_year', 'rating', 'author'])
            ->allowedIncludes(['author', 'categories'])
            ->paginate(10)
            ->appends(request()->query());

        return $this->responseSuccess(['data' => BookResource::collection($books)], 'Books fetched successfully.', 200);
    }

    /**
     * Add Flags to Book
     *
     * `Admin(only)`
     */
    public function addFlagToBook(AddBookFlagRequest $request, $bookId)
    {
        try {
            $data = $request->validated();

            $book = Book::with('flags')->find($bookId);
            if (! $book) {
                return response()->json([
                    'message' => 'Book not found',
                ], 404);
            }
            if ($book->flags()->where('flag', $data['flag'])->exists()) {
                return response()->json([
                    'message' => 'Flag already exists for this book',
                ], 409);
            }
            $book->flags()->create([
                'flag' => $data['flag'],
            ]);

            $book->load('flags');

            return $this->responseSuccess([
                'data' => new BookResource($book),
            ], 'Flag added successfully.', 201);

        } catch (\Exception $e) {
            return $this->responseError('Something went wrong', 500, $e->getMessage());
        }
    }

    /**
     * Get Reading PDF URL
     *
     * Returns a signed URL to the PDF stream endpoint (valid for 45 minutes).
     * The frontend can open this URL directly in an iframe or PDF.js —
     * no Bearer token header needed, no CORS issues.
     */
    public function Reading($id): \Illuminate\Http\JsonResponse
    {
        $book = Book::where('id', $id)->where('is_readable', true)->firstOrFail();

        if (! $book->pdf_read) {
            return response()->json([
                'url'     => null,
                'message' => 'PDF not available',
            ], 404);
        }

        // Track reading status
        $user = Auth::user();
        if ($user) {
            $userBook = $user->books()->find($book->id);
            if (! $userBook) {
                $user->books()->attach($book->id, [
                    'status'      => \App\Enums\UserBookEnum::Reading->value,
                    'pages_read'  => 0,
                    'total_pages' => $book->total_pages ?? null,
                ]);
            }
        }

        // Generate a relative signed URL (path only) — avoids http/https mismatch behind reverse proxy
        $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'books.read-stream',
            now()->addMinutes(45),
            ['book' => $book->id],
            absolute: false
        );

        return response()->json([
            'url'        => $signedUrl,
            'expires_in' => 45 * 60, // seconds
        ], 200);
    }

    /**
     * Stream PDF (used by the signed URL — no Bearer token required)
     */
    public function streamPdfRead(Book $book): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (! $book->pdf_read) {
            abort(404, 'PDF not available.');
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('s3-private');

        if (! $disk->exists($book->pdf_read)) {
            abort(404, 'File not found.');
        }

        $fileSize = $disk->size($book->pdf_read);

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($disk, $book) {
            $stream = $disk->readStream($book->pdf_read);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Length'         => $fileSize,
            'Content-Disposition'    => 'inline; filename="'.basename($book->pdf_read).'"',
            'Cache-Control'          => 'no-store, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Audio URL
     */
    public function Audio($id)
    {

        $data = Book::where('id', $id)->where('has_audio', true)->first();

        if (! $data || ! $data->audio) {
            return $this->responseError(null, 'Audio not available.', 404);
        }

        $tempUrl = Storage::disk('s3-private')->temporaryUrl($data->audio, now()->addMinutes(45));

        return $this->responseSuccess(['url' => $tempUrl], 'URL fetched successfully.', 200);
    }
}
