<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\ShopifyAuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::get('/products', [ShopifyController::class, 'getProducts'])->name('products')->middleware('auth');
Route::get('/product-detail/{product_id}', [ShopifyController::class, 'getProductDetail']);
Route::get('/delete/{product_id}', [ShopifyController::class, 'removeProduct']);


// Route for displaying the login page
Route::get('/login', [ShopifyAuthController::class, 'login'])->name('login');

// Route for handling Shopify OAuth callback
Route::get('/callback', [ShopifyAuthController::class, 'callback'])->name('callback');

// Route for dashboard or home page after successful login
Route::get('/dashboard', [ShopifyAuthController::class, 'dashboard'])->name('dashboard');

// Route for logging out
Route::get('/logout', [ShopifyAuthController::class, 'logout'])->name('logout');
