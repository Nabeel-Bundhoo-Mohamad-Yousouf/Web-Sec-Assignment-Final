<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function create(Book $book)
    {
        // Check if the logged-in user actually owns/rented this book
        $hasInteracted = auth()->user()->rentedBooks()->where('book_id', $book->id)->exists();
        
        if (!$hasInteracted) {
           return redirect()->route('books.show', $book->id)
                         ->with('error', 'You must purchase or rent this book before leaving a review.');
    } 
        return view('reviews.create', compact('book'));
    }

    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        Review::create([
            'book_id' => $book->id,
            'user_id' => auth()->id(), // Automatically grabs the logged-in user
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('books.show', $book->id)
                         ->with('success', 'Your review has been posted!');
    }

    public function destroy(Review $review)
    {
    // SECURITY CHECK: Only allow the owner to delete their own review
        if (auth()->id() !== $review->user_id) {
         abort(403, 'Unauthorized action.');
         }

        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }
}
