<?php

use App\Http\Controllers\AdminFormumController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware(['json.response'])->group(function () {

    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);

    Route::middleware(['auth:api'])->group(function(){
     
        Route::post('logout',[UserController::class,'logout']);

        
        Route::group(['prefix' => 'admin'], function () {
            Route::post('post/approve-or-reject', [PostController::class, 'approveOrReject']);
        });

        Route::post('post/store', [PostController::class, 'store']);
        Route::post('post/update', [PostController::class, 'edit']);
        Route::post('post/delete', [PostController::class, 'delete']);
        Route::get('post/get/{id}', [PostController::class, 'getPostById']);
        Route::get('post/get-all', [PostController::class, 'getAllPosts']);
        Route::post('post/comment', [PostController::class, 'postComment']);
        Route::get('post/comment/get-all', [PostController::class, 'getComments']);
    
        

    });
   
});
