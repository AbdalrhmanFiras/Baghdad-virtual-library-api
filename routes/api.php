<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryGroupController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\GroupCommentController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserTagsController;
use Illuminate\Support\Facades\Route;

// //////////////////////////////////?User//////////////////////////////////////////

// **********************************/Auth/*****************************//

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('auth/google', [AuthController::class, 'google']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

// **********************************/Profile/*****************************//

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

    // **********************************/Groups/*****************************//

    Route::get('/groups-search', [GroupsController::class, 'search']);
    Route::get('/groups', [GroupsController::class, 'index']);
    Route::post('/groups', [GroupsController::class, 'store']);
    Route::get('/groups/my', [GroupsController::class, 'showMy']);
    Route::patch('/groups/{id}', [GroupsController::class, 'update'])->whereNumber('id');
    Route::delete('/groups/{id}', [GroupsController::class, 'destroy'])->whereNumber('id');
    Route::post('/groups/{id}/join', [GroupsController::class, 'join'])->whereNumber('id');
    Route::post('/groups/{id}/leave', [GroupsController::class, 'leave'])->whereNumber('id');

    // **********************************/Books/*****************************//

    Route::get('/books-search', [BookController::class, 'search']);
    Route::get('/books/best', [BookController::class, 'indexBest']);
    Route::get('/books/recommended', [BookController::class, 'indexRec']);
    Route::get('/books/trending', [BookController::class, 'indexTrending']);
    Route::get('books/fav', [BookController::class, 'getFav']);
    Route::get('books/to-read', [BookController::class, 'getToRead']);
    Route::get('books/reading', [BookController::class, 'getReading']);
    Route::get('books/complete', [BookController::class, 'getComplete']);
    Route::get('/books/{bookId}', [BookController::class, 'show']);
    Route::get('books/{book}/download', [BookController::class, 'streamPdfDownload']);
    Route::get('books/{book}/add-fav', [BookController::class, 'addBooktofav']);
    Route::get('books/{book}/add-read', [BookController::class, 'addToRead']);
    Route::post('books/{book}/pages-read', [BookController::class, 'updatePagesRead']);
    Route::post('books/{book}/remove-read', [BookController::class, 'removeToRead']);
    Route::post('books/{book}/remove-fav', [BookController::class, 'removeFav']);
    Route::get('books/{id}/reading', [BookController::class, 'Reading']);
    Route::get('books/{id}/audio', [BookController::class, 'Audio']);

    // **********************************/News/*****************************//

    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{newId}', [NewsController::class, 'show']);

    // **********************************/Tickets/*****************************//
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{id}', [TicketController::class, 'show']);
    });
});

// //////////////////////////////////?Admin//////////////////////////////////////////

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    // **********************************/Author/*****************************//

    Route::post('/author', [AuthorController::class, 'store']);
    Route::put('/author/{Id}', [AuthorController::class, 'update']);
    Route::delete('/author/{Id}', [AuthorController::class, 'delete']);

    // **********************************/Auth/*****************************//

    Route::post('/register', [AuthController::class, 'registerAdmin'])->withoutMiddleware('auth:admin');
    Route::post('/login', [AuthController::class, 'loginAdmin'])
        ->withoutMiddleware('auth:admin');
    Route::post('/logout', [AuthController::class, 'logoutAdmin']);

    // **********************************/Category Groups/*****************************//

    Route::prefix('categories')->group(function () {

        Route::get('/groups', [CategoryGroupController::class, 'index']);
        Route::post('/groups', [CategoryGroupController::class, 'store']);
        Route::get('/groups/{id}', [CategoryGroupController::class, 'show'])->whereNumber('id');
        Route::patch('/groups/{id}', [CategoryGroupController::class, 'update'])->whereNumber('id');
        Route::delete('/groups/{id}', [CategoryGroupController::class, 'destroy'])->whereNumber('id');

        // **********************************/Category/*****************************//

        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show'])->whereNumber('id');
        Route::patch('/{id}', [CategoryController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->whereNumber('id');
    });

    // **********************************/Books/*****************************//

    Route::get('/books-search', [BookController::class, 'search']);
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/best', [BookController::class, 'indexBest']);
    Route::get('/books/recommended', [BookController::class, 'indexRec']);
    Route::get('/books/trending', [BookController::class, 'indexTrending']);
    Route::post('books/{book}/flag', [BookController::class, 'addFlagToBook']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{bookId}', [BookController::class, 'show']);
    Route::patch('/books/{bookId}', [BookController::class, 'update']);
    Route::delete('/books/{bookId}', [BookController::class, 'delete']);
    // Route::get('books/{book}/read', [BookController::class, 'streamPdfRead']);

    // **********************************/News/*****************************//

    Route::get('/news', [NewsController::class, 'index']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::get('/news/{newId}', [NewsController::class, 'show']);
    Route::patch('news/{newId}/', [NewsController::class, 'update']);
    Route::delete('news/{newId}/', [NewsController::class, 'delete']);

    // **********************************/User Tags/*****************************//

    Route::prefix('tags')->group(function () {
        Route::get('/', [UserTagsController::class, 'index']);
        Route::post('/', [UserTagsController::class, 'store']);
        Route::put('/{id}', [UserTagsController::class, 'update']);
        Route::delete('/{id}', [UserTagsController::class, 'destroy']);
    });
    Route::post('users/{userId}/tags', [UserTagsController::class, 'addTagsToUser']);
    Route::put('users/{userId}/tags', [UserTagsController::class, 'updateTagsOfUser']);
    Route::delete('users/{userId}/tags/{tagId}', [UserTagsController::class, 'deleteTagFromUser']);

    // **********************************/Groups Comments/*****************************//

    Route::post('groups/comments', [GroupCommentController::class, 'store']);
    Route::put('groups/comments/{commentId}', [GroupCommentController::class, 'updateComment']);
    Route::post('groups/comments/{commentId}/like', [CommentLikeController::class, 'store']);
    Route::delete('groups/comments/{commentId}/like', [CommentLikeController::class, 'destroy']);

    Route::get('groups/{groupID}/comments', [GroupCommentController::class, 'index']);
    Route::delete('groups/comments/{commentId}', [GroupCommentController::class, 'destroy']);

});

Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
