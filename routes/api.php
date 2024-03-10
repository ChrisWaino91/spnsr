<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClickController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ImpressionController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/impression', [ImpressionController::class, 'log']);
    Route::post('/impressions', [ImpressionController::class, 'logMultiple']);
    Route::post('/click', [ClickController::class, 'log']);
    Route::post('/order', [OrderController::class, 'log']);
    Route::post('/products', [ProductController::class, 'log']);
    Route::get('/campaigns', [CampaignController::class, 'get']);
});

Route::get('/test', function () {
    return 'Test endpoint reached';
});
