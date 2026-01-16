<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
/**
 * @tags Author Endpoint
 */
class AuthorController extends Controller
{
    /**
     * Create Author
     * 
     * This endpoint allows you to add a new author to the system.
     * You must provide `author_name` and `dec`.
     * 
     * Admin(only).
     */
    public function store(StoreAuthorRequest $request){
        $data = $request->validated();
        $author = Author::create($data);
        return $this->responseSuccess($author, 'Author added successfully', 201);
    }
  /**
     * Update Author.
     * 
     * This endpoint allows you to update the author.
     * You must provide `author_name` and `dec`.
     * 
     * Admin(only).
     */
    public function update(UpdateAuthorRequest $request, $id){
        $author = Author::find($id);
        $data = $request->validated();
        if (!$author) {
            return $this->responseError(null, 'Author not found', 404);
        }
        $author->update($data);
        return $this->responseSuccess($author, 'Author updated successfully', 200);
    }
  /**
     * Delete Author
     * 
     * This endpoint allows you to Delete the author.
     * Admin(only).
     */
    public function delete($id){
        $author = Author::find($id);
        if (!$author) {
            return $this->responseError(null, 'Author not found', 404);
        }

        $author->delete();

        return $this->responseSuccess(null, 'Author deleted successfully', 200);
    }
}
