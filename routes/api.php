<?php

use App\Http\Controllers\Api\BookApiController;
use Illuminate\Support\Facades\Route;

// Public API: Anyone can see the book list
Route::get('/books', [BookApiController::class, 'index']);
Route::get('/books/{id}', [BookApiController::class, 'show']);
Route::get('/reviews', [BookApiController::class, 'indexReviews']);

// Protected API: Only "Urshita" with a Token can post reviews
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books/{id}/reviews', [BookApiController::class, 'storeReview']);
    Route::get('/users/{id}/rentals', [BookApiController::class, 'userRentals']);
});
