<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\CategoryGroup;

class CategoryGroupController extends Controller
{
    /**
     * Show(All) Category Groups
     *
     * `Admin(only)`
     */
    public function index()
    {
        $categories = CategoryGroup::all();
        if ($categories->isEmpty()) {
            return $this->responseError(null, 'There is no Category group found', 404);
        }

        return $this->responseSuccess(CategoryResource::collection($categories), 'Categories groups fetched successfully', 200);
    }

    /**
     * Create Category Groups
     *
     * `Admin(only)`
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $category = CategoryGroup::create($data);

        return $this->responseSuccess(new CategoryResource($category), 'Category group created successfully', 201);
    }

    /**
     * Show Category Group
     *
     * `Admin(only)`
     */
    public function show($id)
    {
        $category = CategoryGroup::find($id);
        if (! $category) {
            return $this->responseError(null, 'Category group not found', 404);
        }

        return $this->responseSuccess(new CategoryResource($category), 'Category group fetched successfully', 200);
    }

    /**
     * Update Category Group
     *
     * `Admin(only)`
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = CategoryGroup::find($id);
        if (! $category) {
            return $this->responseError(null, 'Category group not found', 404);
        }
        $data = $request->validated();
        $category->update($data);

        return $this->responseSuccess(new CategoryResource($category), 'Category group updated successfully', 200);
    }

    /**
     * Delete Category Group
     *
     * `Admin(only)`
     */
    public function destroy($id)
    {
        $category = CategoryGroup::find($id);
        if (! $category) {
            return $this->responseError(null, 'Category group not found', 404);
        }
        $category->delete();

        return $this->responseSuccess(null, 'Category group deleted successfully', 200);
    }
}
