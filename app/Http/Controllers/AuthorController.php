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
     */
    public function store(StoreAuthorRequest $request){
        $data = $request->validated();
        $author = Author::create($data);
        return $this->responseSuccess($author,'Author added successfully' ,201);
    }



    public function update(UpdateAuthorRequest $request,$id){
        $data = $request->validated();
        if(!Author::where('id' , $id)->exists()){
            return $this->responseError(null,'Author not found' , 404);
        }
        $author = Author::update($data);
        return $this->responseSuccess($author,'Author updated successfully' ,200);
    }




    public function delete($id){
        $author = Author::find($id);
        if (!$author) {
        return $this->responseError(null, 'Author not found', 404);
         $author->delete();
        return $this->responseSuccess($author,'Author deleted successfully' ,200);
    }
}
}