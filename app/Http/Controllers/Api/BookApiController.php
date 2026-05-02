<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    // Pagination: Returns reviews in chunks of 2
    public function indexReviews() {
        return response()->json(Review::with(['book', 'user'])->paginate(2), 200);
    }

    // One-to-Many: Show one book and all its related reviews
    public function show($id) {
        $book = Book::with('reviews.user')->findOrFail($id);
        return response()->json($book, 200);
    }

    // CRUD (Create) + Auth: Add a review using the Sanctum token
    public function storeReview(Request $request, $id) {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review = Review::create([
            'book_id' => $id,
            'user_id' => auth()->id(), 
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return response()->json($review, 201);
    }

    // Many-to-Many: Shows books rented by a user via the pivot table
    public function userRentals($userId) {
        $user = User::with('books')->findOrFail($userId);
        return response()->json($user->books, 200);
    }
}
