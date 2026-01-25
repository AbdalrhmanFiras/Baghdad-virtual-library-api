<?php

namespace App\Http\Controllers;

use App\Models\CommentLike;
use App\Models\GroupComment;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Like EndPoint
 */
class CommentLikeController extends Controller
{
    /**
     * Put Like
     */
    public function store($commentId)
    {
        try {
            $user = Auth::user();

            $comment = GroupComment::find($commentId);
            if (! $comment) {
                return $this->responseError(null, 'Comment not found', 404);
            }

            $exists = CommentLike::where('user_id', $user->id)
                ->where('group_comment_id', $commentId)
                ->exists();

            if ($exists) {
                return $this->responseError(null, 'Already liked', 409);
            }

            CommentLike::create([
                'user_id' => $user->id,
                'group_comment_id' => $commentId,
            ]);

            return $this->responseSuccess([
                'likes_count' => $comment->likes()->count(),
            ], 'Liked successfully', 201);

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Remove Like
     */
    public function destroy($commentId)
    {
        try {
            $user = Auth::user();

            $like = CommentLike::where('user_id', $user->id)
                ->where('group_comment_id', $commentId)
                ->first();

            if (! $like) {
                return $this->responseError(null, 'Like not found', 404);
            }

            $like->delete();

            return $this->responseSuccess(null, 'Like removed successfully');

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }
}
