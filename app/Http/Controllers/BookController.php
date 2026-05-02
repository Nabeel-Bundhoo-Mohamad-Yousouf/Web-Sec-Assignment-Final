<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of books
     */
    public function index(Request $request)
    {
        $query = Book::with(['categories', 'reviews']);
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sort options
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->latest();
        }
        
        $books = $query->paginate(12);
        $categories = Category::withCount('books')->get();
        
        return view('books.index', compact('books', 'categories'));
    }
    
    /**
     * Display a single book
     */
    public function show($id)
    {
        $book = Book::with(['categories', 'reviews.user', 'rentals'])->findOrFail($id);
        
        // Get related books (same category)
        $relatedBooks = Book::whereHas('categories', function($q) use ($book) {
            $q->whereIn('categories.id', $book->categories->pluck('id'));
        })->where('id', '!=', $book->id)->limit(4)->get();
        
        // Calculate average rating
        $avgRating = $book->reviews->avg('rating') ?? 0;
        $ratingCount = $book->reviews->count();
        
        return view('books.show', compact('book', 'relatedBooks', 'avgRating', 'ratingCount'));
    }
    
    /**
     * Search books (AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'title', 'author', 'price', 'cover_image']);
        
        return response()->json($books);
    }
    
    /**
     * Load book details via AJAX (for your existing JSON functionality)
     */
    public function loadDetails(Request $request)
    {
        $bookId = $request->book_id;
        $book = Book::with(['reviews.user', 'categories'])->find($bookId);
        
        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found']);
        }
        
        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->formatted_price,
                'rental_price' => $book->formatted_rental_price ?? 'Rs 0',
                'description' => $book->description,
                'stock' => $book->stock,
                'in_stock' => $book->in_stock,
                'cover_url' => $book->cover_url,
                'average_rating' => $book->average_rating ?? 0,
                'reviews_count' => $book->reviews->count(),
                'reviews' => $book->reviews->take(5)
            ]
        ]);
    }
}