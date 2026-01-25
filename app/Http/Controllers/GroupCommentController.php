<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupCommentRequest;
use App\Http\Requests\updateGroupCommentRequest;
use App\Http\Resources\CommentGroupsResource;
use App\Models\GroupComment;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Comments Group
 */
class GroupCommentController extends Controller
{
    /**
     * Add Comment(Text)
     */
    public function store(StoreGroupCommentRequest $request)
    {
        try {
            $data = $request->vaildated();
            $user = Auth::user();
            $groupId = $request->group_id;

            $userGroupIds = $user->groups->pluck('id');
            if (! $userGroupIds->contains($groupId)) {
                return $this->responseError(null, 'You are not a member of this group', 403);
            }

            $comment = GroupComment::create($data);
            $comment->load('user.profile', 'likes');

            return $this->responseSuccess(new CommentGroupsResource($comment), 'Comment posted successfully', 201);

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Update Comment(Text)
     */
    public function updateComment(updateGroupCommentRequest $request, $commentId)
    {
        try {
            $data = $request->validated();
            $user = Auth::user();

            $comment = GroupComment::find($commentId);
            if (! $comment) {
                return $this->responseError(null, 'Comment not found', 404);
            }

            if ($comment->user_id !== $user->id) {
                return $this->responseError(null, 'You are not allowed to edit this comment', 403);
            }

            $userGroupIds = $user->groups->pluck('id');
            if (! $userGroupIds->contains($comment->group_id)) {
                return $this->responseError(null, 'You are not a member of this group', 403);
            }

            $comment->update($data);

            $comment->load('user.profile', 'likes');

            return $this->responseSuccess(
                new CommentGroupsResource($comment),
                'Comment updated successfully',
                200
            );

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Get All Text
     */
    public function index($groupId)
    {
        try {
            $user = Auth::user();
            if (! $user->groups()->where('groups.id', $groupId)->exists()) {
                return $this->responseError(null, 'You are not a member of this group', 403);
            }

            $comments = GroupComment::where('group_id', $groupId)
                ->with(['user.profile', 'likes'])
                ->latest();

            return $this->responseSuccess(
                CommentGroupsResource::collection($comments),
                'Comments fetched successfully', 200
            );

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Remove my Text(comment)
     */
    public function destroy($commentId)
    {
        try {
            $user = Auth::user();

            $comment = GroupComment::find($commentId);
            if (! $comment) {
                return $this->responseError(null, 'Comment not found', 404);
            }

            if ($comment->user_id !== $user->id) {
                return $this->responseError(null, 'You are not allowed to delete this comment', 403);
            }

            if (! $user->groups()->where('groups.id', $comment->group_id)->exists()) {
                return $this->responseError(null, 'You are not a member of this group', 403);
            }

            $comment->delete();

            return $this->responseSuccess(null, 'Comment deleted successfully', 200);

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }
}
