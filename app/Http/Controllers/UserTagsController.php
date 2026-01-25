<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserTagsRequest;
use App\Http\Requests\UpdateUserTagsRequest;
use App\Models\User;
use App\Models\UserTags;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * @tags User Tags EndPoint
 */
class UserTagsController extends Controller
{
    /**
     * Get All Tags
     */
    public function index()
    {
        try {
            $tags = UserTags::all();

            return $this->responseSuccess($tags, 'Tags fetched successfully');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Add Tags
     */
    public function store(StoreUserTagsRequest $request)
    {
        try {
            $data = $request->validated();
            $existingTag = UserTags::where('name', $data['name'])->first();
            if ($existingTag) {
                return $this->responseError(null, 'Tag already exists', 409);
            }
            $tag = UserTags::create($data);

            return $this->responseSuccess($tag, 'Tag created successfully', 201);
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Update Tags
     */
    public function update(UpdateUserTagsRequest $request, $id)
    {
        try {
            $tag = UserTags::find($id);
            if (! $tag) {
                return $this->responseError(null, 'Tag not found', 404);
            }
            $tag->update($request->validated());

            return $this->responseSuccess($tag, 'Tag updated successfully');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Delete Tags
     */
    public function destroy($id)
    {
        try {
            $tag = UserTags::find($id);
            if (! $tag) {
                return $this->responseError(null, 'Tag not found', 404);
            }
            $tag->delete();

            return $this->responseSuccess(null, 'Tag deleted successfully');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Add Tag to user
     */
    public function addTagsToUser($memberId, array $tags)
    {
        try {
            $admin = Auth::user();
            $member = User::findOrFail($memberId);

            // تأكد إن العضو في نفس مجموعة الـ admin
            $adminGroupIds = $admin->groups()->pluck('groups.id')->toArray();
            $memberGroupIds = $member->groups()->pluck('groups.id')->toArray();
            if (! count(array_intersect($adminGroupIds, $memberGroupIds))) {
                return $this->responseError(null, 'Member is not in your group', 403);
            }

            $member->user_tags()->syncWithoutDetaching($tags);

            return $this->responseSuccess(['tags' => $member->user_tags], 'Tags added successfully');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Change Tag of user
     */
    public function updateTagsOfUser($memberId, array $tags)
    {
        try {
            $admin = Auth::user();
            $member = User::findOrFail($memberId);

            $adminGroupIds = $admin->groups()->pluck('groups.id')->toArray();
            $memberGroupIds = $member->groups()->pluck('groups.id')->toArray();
            if (! count(array_intersect($adminGroupIds, $memberGroupIds))) {
                return $this->responseError(null, 'Member is not in your group', 403);
            }

            $member->user_tags()->sync($tags);

            return $this->responseSuccess(['tags' => $member->user_tags], 'Tags updated successfully');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    /**
     * Remove Tag from user
     */
    public function deleteTagFromUser($memberId, $tagId)
    {
        try {
            $admin = Auth::user();
            $member = User::findOrFail($memberId);

            $adminGroupIds = $admin->groups()->pluck('groups.id')->toArray();
            $memberGroupIds = $member->groups()->pluck('groups.id')->toArray();
            if (! count(array_intersect($adminGroupIds, $memberGroupIds))) {
                return $this->responseError(null, 'Member is not in your group', 403);
            }

            $member->user_tags()->detach($tagId);

            return $this->responseSuccess(null, 'Tag removed successfully');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }
}
