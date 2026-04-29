<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    // Returns all books as JSON for Postman
    public function index() {
        return response()->json(Book::all(), 200);
    }

    // Returns a single book with its reviews
    public function show($id) {
        $book = Book::with('reviews.user')->find($id);
        
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        
        return response()->json($book, 200);
    }
}