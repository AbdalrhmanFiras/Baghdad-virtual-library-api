<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

/**
 * @tags Category EndPoint
 */
class CategoryController extends Controller
{
    /**
     * Show(All) Category
     *
     * `Admin(only)`
     */
    public function index()
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return $this->responseError(null, 'There is no Category found', 404);
        }

        return $this->responseSuccess(CategoryResource::collection($categories), 'Categories fetched successfully', 200);
    }

    /**
     * Create Category
     *
     * `Admin(only)`
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $category = Category::create($data);

        return $this->responseSuccess(new CategoryResource($category), 'Category created successfully', 201);
    }

    /**
     * Show Category
     *
     * `Admin(only)`
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (! $category) {
            return $this->responseError(null, 'Category not found', 404);
        }

        return $this->responseSuccess(new CategoryResource($category), 'Category fetched successfully', 200);
    }

    /**
     * Update Category
     *
     * `Admin(only)`
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (! $category) {
            return $this->responseError(null, 'Category not found', 404);
        }
        $data = $request->validated();
        $category->update($data);

        return $this->responseSuccess(new CategoryResource($category), 'Category updated successfully', 200);
    }

    /**
     * Delete Category
     *
     * `Admin(only)`
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (! $category) {
            return $this->responseError(null, 'Category not found', 404);
        }
        $category->delete();

        return $this->responseSuccess(null, 'Category deleted successfully', 200);
    }
}
