<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('VerifyJWTToken')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::group(['controller' => ProductController::class], function () {
        Route::get('categories', 'categories');
        Route::get('products', 'products');
        Route::get('product-detail', 'productDetail');
        Route::post('add-product', 'addProduct');
        Route::post('edit-product/{id}', 'updateProduct');
        Route::delete('delete-product/{id}', 'deleteProduct');
    });
});

