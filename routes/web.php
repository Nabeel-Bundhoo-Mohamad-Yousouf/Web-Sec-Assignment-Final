<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

//BookController Routes
Route::get('/', [BookController::class, 'index'])
->name("books.index");

Route::get('/books/{book}', [BookController::class, 'show'])
    ->name('books.show');

Route::get('/books', [BookController::class, 'filterAndSort'])
    ->name('books.arrange');

