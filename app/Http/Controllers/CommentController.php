<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Book;
use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Comment Endpoint
 */
class CommentController extends Controller
{
    /**
     * Create(Add) Comment to book
     */

    // For BooK
    public function store(StoreCommentRequest $request, $bookId)
    {
        $data = $request->validated();
        $user = Auth::id();
        if (! Book::where('id', $bookId)->exists()) {
            return $this->responseError(null, 'Book not found.', 404);
        }
        $data['book_id'] = $bookId;
        $data['user_id'] = $user;
        $comment = Comment::create($data);

        return $this->responseSuccess(['data' => new CommentResource($comment)], 'Comment Added Successfully', 201);
    }

    /**
     * Get all your comments
     */
    public function index()
    {
        $user_id = Auth::id();
        $comments = Comment::where('user_id', $user_id)->paginate(5);

        return $this->responseSuccess(['data' => CommentResource::collection($comments)], $comments->count() ?
        'Comment fetched successfully' : 'not comments', 200);
    }

    /**
     * Get all post comments
     */
    // For Book
    public function getBookcomment($bookId)
    {
        $comments = Comment::where('book_id', $bookId)->with('user')
            ->paginate(100);

        if ($comments->isEmpty()) {
            return $this->responseError(null, 'no comments yet.', 404);
        }

        return $this->responseSuccess(['data' => CommentResource::collection($comments),
        ], 'comments fetched successfully', 200);
    }

    /**
     * Delete my comment
     */

    // For Book
    public function delete($commentId, $bookId)
    {
        try {
            $comment = Comment::where('id', $commentId)
                ->where('user_id', Auth::id())
                ->where('book_id', $bookId)
                ->firstOrFail();

            $comment->delete();

            return $this->responseSuccess(null, 'Comment deleted successfully', 200);
        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'comment not found', 404);
        }
    }

    /**
     * Update my comment
     */

    // For Book
    public function update(UpdateCommentRequest $request, $commentId, $bookId)
    {
        try {
            $data = $request->validated();

            $comment = Comment::where('id', $commentId)
                ->where('user_id', Auth::id())
                ->where('book_id', $bookId)
                ->firstOrFail();
            $comment->update($data);

            return $this->responseSuccess(new CommentResource($comment), 'Comment updated successfully', 200);

        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'comment not found', 404);
        }
    }
}
