<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // 1. Updated Index: Now includes average ratings for the stars on the homepage
    public function index(Request $request) {
        $query = Book::query();

        // Calculate average rating directly in the query for better performance
        $query->withAvg('reviews', 'rating');

        if ($request->filled('txt_search')) {
            $term = $request->txt_search;
            $query->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('author', 'like', "%{$term}%");
            });
        }

        if ($request->filled('txt_genre') && $request->txt_genre !== 'all') {
            $query->where('genre', $request->txt_genre);
        }

        $books = $query->paginate(8)->withQueryString();
        return view('index', compact('books'));
    }

    // 2. NEW: The missing show method to handle book details
// 2. UPDATED: The show method to handle book details WITH Paginated Reviews
    public function show(Book $book) {
        // Calculate average rating for the book details
        $book->loadAvg('reviews', 'rating');

        // Fetch the reviews separately and paginate them (2 per page)
        $reviews = $book->reviews()->with('user')->latest()->paginate(2);

        // Pass BOTH the book and the paginated reviews to the view
        return view('book_details', compact('book', 'reviews'));
    }

    // 3. Borrow logic
    public function borrow(Request $request) {
        $request->validate(['id' => 'required|exists:books,id']);
        
        // Attaches the book to the user in the pivot table
        auth()->user()->rentedBooks()->syncWithoutDetaching([$request->id]);
        
        return back()->with('success', 'Book borrowed successfully!');
    }
}
