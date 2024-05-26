<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AuthenticationApi;


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
Route::controller(UserController::class)->group(function () {
    Route::post('user/register', 'register');
    Route::post('user/login', 'login');
});
Route::middleware([AuthenticationApi::class])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('user/logout', 'logout');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::post('category/create', 'create');
        Route::post('category/list', 'list');
    });

    Route::controller(TagController::class)->group(function () {
        Route::post('tag/create', 'create');
        Route::post('tag/list', 'list');
    });

    
    Route::controller(PostController::class)->group(function(){
        Route::post('post/my_post_list','my_post_list');
        Route::post('post/create','create');
        Route::post('post/list','list');
        Route::post('post/delete_post', 'delete_post');
        Route::post('post/update_post','update_post');

        Route::post('post/post_comment','post_comment');
    });

});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
