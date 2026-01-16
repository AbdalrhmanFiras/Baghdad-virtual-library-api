<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
/**
 * @tags Category EndPoint
 */
class CategoryController extends Controller
{
    /**
     * Show(All) Category
     * 
     * Admin(only).
     */
     public function index()
    {
        $categories = Category::all();
        if($categories->isEmpty()){
                return $this->responseError(null, 'There is no Category found', 404);
        }
        return $this->responseSuccess($categories, 'Categories fetched successfully', 200);
    }

     /**
     * Create Category
     * 
     * Admin(only).
     */
    public function store(StoreCategoryRequest $request)
    {
       $data = $request->validated();
        $category = Category::create($data);
        return $this->responseSuccess($category, 'Category created successfully', 201);
    }

     /**
     * Show Category
     * 
     * Admin(only).
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->responseError(null, 'Category not found', 404);
        }
        return $this->responseSuccess($category, 'Category fetched successfully', 200);
    }

     /**
     * Update Category
     * 
     * Admin(only).
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->responseError(null, 'Category not found', 404);
        }
        $data = $request->validated();
        $category->update($data);
        return $this->responseSuccess($category, 'Category updated successfully', 200);
    }

     /**
     * Delete Category
     * 
     * Admin(only).
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->responseError(null, 'Category not found', 404);
        }
        $category->delete();
        return $this->responseSuccess(null, 'Category deleted successfully', 200);
    }
}
