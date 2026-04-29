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
    public function show(Book $book) {
        // Load the reviews and the users who wrote them
        // Also calculate average rating and sum for the details page
        $book->load(['reviews.user'])
             ->loadAvg('reviews', 'rating')
             ->loadSum('reviews', 'rating');

        return view('book_details', compact('book'));
    }

    // 3. Borrow logic
    public function borrow(Request $request) {
        $request->validate(['id' => 'required|exists:books,id']);
        
        // Attaches the book to the user in the pivot table
        auth()->user()->rentedBooks()->syncWithoutDetaching([$request->id]);
        
        return back()->with('success', 'Book borrowed successfully!');
    }
}