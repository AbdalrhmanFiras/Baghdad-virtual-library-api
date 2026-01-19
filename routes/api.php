<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// //////////////////////////////////?User//////////////////////////////////////////

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
});

// //////////////////////////////////?Admin//////////////////////////////////////////
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::post('/author', [AuthorController::class, 'store']);
    Route::put('/author/{Id}', [AuthorController::class, 'update']);
    Route::delete('/author/{Id}', [AuthorController::class, 'delete']);

    // **********************************/Category/*****************************//

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::patch('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // **********************************/Books/*****************************//

    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{bookId}', [BookController::class, 'show']);
    Route::patch('/books/{bookId}', [BookController::class, 'update']);
    Route::delete('/books/{bookId}', [BookController::class, 'delete']);

    // **********************************/News/*****************************//

    Route::get('/news', [NewsController::class, 'index']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::get('/news/{newId}', [NewsController::class, 'show']);
    Route::patch('news/{newId}/', [NewsController::class, 'update']);
    Route::delete('news/{newId}/', [NewsController::class, 'delete']);
});
