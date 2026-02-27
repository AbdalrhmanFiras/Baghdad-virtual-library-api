<?php

namespace App\Http\Controllers;

use App\Enums\GroupStatusEnum;
use App\Helper\FileHelper;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\CategoryGroup;
use App\Models\Groups;
use Dedoc\Scramble\Attributes\Group;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @tags Groups Endpoint
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
            $group->load('user.profile');

            return $this->responseSuccess(['data' => new GroupResource($group)],
                'Group created Successfully', 201);
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($group)) {
                FileHelper::DeleteImage($group, 's3-private');
            }

            return $this->responseError(null, $e->getMessage(), 500);
        }

    }

    /**
     * Show my Groups
     */
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

    /**
     * Show(All) Groups
     */

    /**
     * Show (All) Groups with optional category filter
     */
    public function index(Request $request)
    {
        $groups = Groups::query()
            ->with(['image', 'category_groups'])
            ->withCount('users')
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->whereHas('category_groups', function ($q) use ($request) {
                    $q->where('id', $request->category_id);
                });
            })
            ->paginate(100);

        return $this->responseSuccess([
            'data' => GroupResource::collection($groups),
            'meta' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
                'public_count' => $this->getPublic(),
                'private_count' => $this->getPrivate(),
            ],
        ], 'Groups fetched successfully.', 200);
    }

    // public function index(Request $request)
    // {
    //     $query = Groups::with(['image', 'category_groups', 'users']);

    //     if ($request->category_id) {
    //         $query->whereHas('category_groups', function ($q) use ($request) {
    //             $q->where('category_groups.id', $request->category_id);
    //         });
    //     }

    //     $groups = $query->paginate(10);

    //     if ($groups->isEmpty()) {
    //         return $this->responseError('null', 'There is no groups found', 404);
    //     }

    //     return $this->responseSuccess([
    //         'data' => GroupResource::collection($groups),
    //         'meta' => [
    //             'current_page' => $groups->currentPage(),
    //             'last_page' => $groups->lastPage(),
    //             'per_page' => $groups->perPage(),
    //             'total' => $groups->total(),
    //             'public_count' => $this->getPublic(),
    //             'private_count' => $this->getPrivate(),
    //         ],
    //     ], 'Groups fetched successfully.', 200);
    // }

    /**
     * Update Groups
     */
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

    /**
     * join to Group
     */
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

    /**
     * Left from Group
     */
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

    /**
     * Search Group
     */
    public function search()
    {
        $groups = QueryBuilder::for(Groups::class)
            ->allowedFilters([
                AllowedFilter::partial('title'),
                AllowedFilter::callback('category_groups', function ($query, $value) {
                    if (is_array($value)) {
                        $query->whereHas('category_groups', fn ($q) => $q->whereIn('id', $value));
                    } else {
                        $query->whereHas('category_groups', fn ($q) => $q->where('id', $value));
                    }
                }),
            ])
            ->allowedSorts(['title', 'rating'])
            ->allowedIncludes(['category_groups'])
            ->paginate(10)
            ->appends(request()->query());

        return $this->responseSuccess(
            ['data' => GroupResource::collection($groups)],
            'Groups fetched successfully.',
            200
        );
    }

    /**
     * Delete Group
     */
    public function destroy($groupId)
    {
        try {
            $userId = Auth::id();

            $group = Groups::with('users', 'image', 'category_groups')
                ->where('user_id', $userId)
                ->where('id', $groupId)
                ->firstOrFail();

            DB::beginTransaction();

            $group->users()->detach();

            if ($group->image) {
                FileHelper::DeleteImage($group, 's3-private');
            }

            $group->category_groups()->detach();

            $group->delete();

            DB::commit();

            return $this->responseSuccess(null, 'Group deleted successfully', 200);

        } catch (ModelNotFoundException) {
            return $this->responseError(null, 'This group does not exist.', 404);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseError(null, $e->getMessage(), 500);
        }
    }

    protected function getPublic()
    {

        $count = Groups::where('availbility', GroupStatusEnum::Public)->count();

        return $count;
    }

    protected function getPrivate()
    {

        $count = Groups::where('availbility', GroupStatusEnum::Private)->count();

        return $count;
    }

    // protected function getCategory(): array
    // {

    //     $cate = CategoryGroup::get();

    //     $CateCount = [];
    //     foreach ($cate as $index) {
    //         $CateCount = $index->count();
    //     }

    //     return $CateCount;
    // }
}
