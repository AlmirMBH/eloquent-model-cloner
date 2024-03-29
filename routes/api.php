<?php

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthorController::class)->group(function () {
    Route::prefix('authors')->group(function() {
        Route::get('{id}/clone', 'clone')->name('cloneAuthor');
        Route::get('{id}', 'show')->name('getAuthor');
    });
});

Route::controller(PostController::class)->group(function () {
    Route::prefix('posts')->group(function() {
        Route::get('{id}/clone', 'clone')->name('clonePost');
        Route::get('{id}', 'show')->name('getPost');
    });
});
