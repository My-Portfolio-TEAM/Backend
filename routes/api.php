<?php

use App\Http\Controllers\Api\BackgroundPhotoController;
use App\Http\Controllers\Api\BiodataController;
use App\Http\Controllers\Api\PhotoProfileController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostUpVoteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Auth
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);
Route::post('/register', [App\Http\Controllers\Api\Auth\LoginController::class, 'register']);



Route::group(['middleware' => 'auth:api'], function() {

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
    Route::get('/me', [UserController::class, 'profile']);
    Route::put('/users', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'delete']);
    Route::get('/my-posts',[PostController::class, 'myPosts']);
    Route::post('/posts/{id}/up-vote',[PostUpVoteController::class, 'store']);
    Route::delete('/posts/{id}/down-vote',[PostUpVoteController::class, 'destroy']);


    // Api Resource
    Route::apiResource('/biodatas', BiodataController::class);
    Route::apiResource('/photos', PhotoProfileController::class);
    Route::apiResource('/backgrounds', BackgroundPhotoController::class);
    Route::apiResource('/portfolios', PortfolioController::class);
    Route::apiResource('/posts', PostController::class);



});
