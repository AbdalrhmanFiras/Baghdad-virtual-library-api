<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserTagsRequest;
use App\Http\Requests\UpdateUserTagsRequest;
use App\Models\User;
use App\Models\UserTags;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserTagsController extends Controller
{
    public function index()
    {
        try {
            $tags = UserTags::all();

            return $this->responseSuccess($tags, 'Tags fetched successfully');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

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

    public function show($id)
    {
        try {
            $tag = UserTags::find($id);
            if (! $tag) {
                return $this->responseError(null, 'Tag not found', 404);
            }

            return $this->responseSuccess($tag, 'Tag fetched successfully');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

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

    public function addTagsToUser($memberId, array $tags)
    {
        try {
            $admin = Auth::user();
            $currentGroup = $admin->groups()->first();
            if (! $currentGroup) {
                return $this->responseError(null, 'Admin is not in any group', 403);
            }

            $member = User::find($memberId);
            if (! $member) {
                return $this->responseError(null, 'Member not found', 404);
            }

            $memberGroupIds = $member->groups->pluck('id');
            if (! $memberGroupIds->contains($currentGroup->id)) {
                return $this->responseError(null, 'Member is not in the same group', 403);
            }
            $existingTags = UserTags::whereIn('id', $tags)->get();
            if ($existingTags->isEmpty()) {
                return $this->responseError(null, 'No valid tags found', 404);
            }
            $member->user_tags()->syncWithoutDetaching($existingTags->pluck('id'));

            return $this->responseSuccess([
                'member' => $member->id,
                'tags_added' => $existingTags->pluck('name'),
            ], 'Tags added successfully', 200);

        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

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

            // أضف الـ tags
            $member->user_tags()->syncWithoutDetaching($tags);

            return $this->responseSuccess(['tags' => $member->user_tags], 'Tags added successfully');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

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
