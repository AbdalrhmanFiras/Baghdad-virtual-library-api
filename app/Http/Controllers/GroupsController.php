<?php

namespace App\Http\Controllers;

use App\Helper\FileHelper;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Groups;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @tags Groups EndPoint
 */
class GroupsController extends Controller
{
    /**
     * Create Groups
     */
    public function store(StoreGroupRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $categories = $data['categories'] ?? [];
            unset($data['categories']);
            if (Groups::where('title', $data['title'])->where('user_id', Auth::id())->exists()) {
                return $this->responseError(null, 'This groups already exists', 200);
            }
            DB::beginTransaction();
            $group = Groups::create($data);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = FileHelper::ImageUpload($file, 'groups', 'images');
                if (! $path) {
                    throw new Exception('Image upload failed');
                }
                $group->image()->create([
                    'url' => $path,
                    'type' => 'books',
                ]);
            }
            if (! empty($categories)) {
                $group->category_groups()->sync($categories);
            }
            DB::commit();

            return $this->responseSuccess(['data' => new GroupResource($group)->load('user')],
                'Group created Successfully', 201);
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($group)) {
                FileHelper::DeleteImage($group, 's3-private');
            }

            return $this->responseError(null, $e->getMessage(), 500);
        }

    }

    public function showMy()
    {
        $groups = Groups::with(['image', 'category_groups', 'users'])->where('user_id', Auth::id())->paginate(5);
        if ($groups->isEmpty()) {
            return $this->responseError('null', 'There is no groups found', 404);
        }

        return $this->responseSuccess(['data' => GroupResource::collection($groups), 'meta' => [
            'current_page' => $groups->currentPage(),
            'last_page' => $groups->lastPage(),
            'per_page' => $groups->perPage(),
            'total' => $groups->total(),
        ]], 'Groups fetched successfully.', 200);
    }

    public function index()
    {
        $groups = Groups::with(['image', 'category_groups', 'users'])->paginate(5);
        if ($groups->isEmpty()) {
            return $this->responseError('null', 'There is no groups found', 404);
        }

        return $this->responseSuccess(['data' => GroupResource::collection($groups), 'meta' => [
            'current_page' => $groups->currentPage(),
            'last_page' => $groups->lastPage(),
            'per_page' => $groups->perPage(),
            'total' => $groups->total(),
        ]], 'Groups fetched successfully.', 200);
    }

    public function update(UpdateGroupRequest $request, $groupId)
    {
        try {
            $userId = Auth::id();
            $data = $request->validated();
            DB::beginTransaction();
            $group = Groups::with(['image', 'category_groups'])->where('user_id', $userId)->where('id', $groupId)->firstOrFail();
            if (! $userId === $group->user_id) {
                return $this->responseError('null', 'You dont have permission to update', 200);
            }// in case
            $group->update($data);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = FileHelper::ImageUpload($file, 'groups', 'image');
                FileHelper::UpdateImage($group, $path);
            }
            DB::commit();

            return $this->responseSuccess(['data' => new GroupResource($group)], 'Group updated successfully', 200);
        } catch (ModelNotFoundException) {
            return $this->responseError('null', 'This group is not longer exists.', 404);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseError(null, $e->getMessage(), 500);

        }
    }

    public function join($groupId)
    {
        try {
            $userId = Auth::id();
            $group = Groups::where('id', $groupId)->firstOrFail();
            if ($group->users()->where('user_id', $userId)->exists()) {
                return $this->responseError(null, 'You are already joined to this group.', 400);
            }
            $group->users()->attach($userId);

            return $this->responseSuccess(null, 'Joined to group successfully', 200);
        } catch (ModelNotFoundException) {
            return $this->responseError('null', 'This group is not longer exists.', 404);

        } catch (Exception $e) {

            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    public function leave($groupId)
    {
        try {
            $userId = Auth::id();
            $group = Groups::where('id', $groupId)->firstOrFail();
            if (! $group->users()->where('user_id', $userId)->exists()) {
                return $this->responseError(null, 'You are not member of this group.', 400);
            }
            $group->users()->detach($userId);

            return $this->responseSuccess(null, 'Left the group successfully', 200);
        } catch (ModelNotFoundException) {
            return $this->responseError('null', 'This group is not longer exists.', 404);
        } catch (Exception $e) {

            return $this->responseError(null, $e->getMessage(), 500);
        }
    }
}
