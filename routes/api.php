<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryGroupController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// //////////////////////////////////?User//////////////////////////////////////////

// **********************************/Auth/*****************************//
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
// **********************************/Profile/*****************************//
// test
Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    // **********************************/Comments Books/*****************************//
    Route::prefix('book')->group(function () {
        Route::get('/comment', [CommentController::class, 'index']);
        Route::post('/comment/{bookId}', [CommentController::class, 'store']);
        Route::get('/comment/{bookId}', [CommentController::class, 'getBookcomment']);
        Route::patch('/comment/{bookId}/{commentId}', [CommentController::class, 'update']);
        Route::delete('/comment/{bookId}/{commentId}', [CommentController::class, 'delete']);
    });
});

// //////////////////////////////////?Admin//////////////////////////////////////////
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::post('/author', [AuthorController::class, 'store']);
    Route::put('/author/{Id}', [AuthorController::class, 'update']);
    Route::delete('/author/{Id}', [AuthorController::class, 'delete']);

    // **********************************/groups/*****************************//

    Route::get('/groups', [GroupsController::class, 'index']);
    Route::post('/groups', [GroupsController::class, 'store']);
    Route::get('/groups/my', [GroupsController::class, 'showMy']);
    Route::patch('/groups/{id}', [GroupsController::class, 'update'])->whereNumber('id');
    Route::delete('/groups/{id}', [GroupsController::class, 'destroy'])->whereNumber('id');
    Route::post('/groups/{id}/join', [GroupsController::class, 'join'])->whereNumber('id');
    Route::post('/groups/{id}/leave', [GroupsController::class, 'leave'])->whereNumber('id');

    Route::prefix('categories')->group(function () {

        Route::get('/groups', [CategoryGroupController::class, 'index']);
        Route::post('/groups', [CategoryGroupController::class, 'store']);
        Route::get('/groups/{id}', [CategoryGroupController::class, 'show'])->whereNumber('id');
        Route::patch('/groups/{id}', [CategoryGroupController::class, 'update'])->whereNumber('id');
        Route::delete('/groups/{id}', [CategoryGroupController::class, 'destroy'])->whereNumber('id');

        // **********************************/Category Groups/*****************************//

        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show'])->whereNumber('id');
        Route::patch('/{id}', [CategoryController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->whereNumber('id');
    });

    Route::get('/books', [BookController::class, 'index']);
    Route::get('books/fav', [BookController::class, 'getFav']);
    Route::get('books/to-read', [BookController::class, 'getToRead']);
    Route::get('books/reading', [BookController::class, 'getReading']);
    Route::get('books/complete', [BookController::class, 'getComplete']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{bookId}', [BookController::class, 'show']);
    Route::patch('/books/{bookId}', [BookController::class, 'update']);
    Route::delete('/books/{bookId}', [BookController::class, 'delete']);
    // Route::get('books/{book}/read', [BookController::class, 'streamPdfRead']);
    Route::get('books/{book}/download', [BookController::class, 'streamPdfDownload']);
    Route::get('books/{book}/add-fav', [BookController::class, 'addBooktofav']);
    Route::get('books/{book}/add-read', [BookController::class, 'addToRead']);
    Route::post('books/{book}/pages-read', [BookController::class, 'updatePagesRead']);
    Route::post('books/{book}/remove-read', [BookController::class, 'removeToRead']);
    Route::post('books/{book}/remove-fav', [BookController::class, 'removeFav']);

    // **********************************/News/*****************************//

    Route::get('/news', [NewsController::class, 'index']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::get('/news/{newId}', [NewsController::class, 'show']);
    Route::patch('news/{newId}/', [NewsController::class, 'update']);
    Route::delete('news/{newId}/', [NewsController::class, 'delete']);
});
