 <?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

Route::get('/api/books/search', [BookController::class, 'search'])
    ->name('api.books.search');
