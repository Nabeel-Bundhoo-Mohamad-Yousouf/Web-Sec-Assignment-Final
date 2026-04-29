<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\FilterAndSortBookRequest;

class BookController extends Controller
{
    //FOR GUEST AND/OR CUSTOMER

    /** Display list of books */
    public function index()
    {
        $books = Book::reviewStats()
                ->simplePaginate(6);
        return view('index', compact('books'));
    }


    /**Display the specified book. */
    public function show($book_id):View
    {
        $book = Book::findOrFail($book_id);
        $reviews = $book -> reviews() ->paginate(3);
        return view("book_details", compact('book', 'reviews'));

        /* Add pagination links in book_preview.php */
    }

    /**Apply filter + sort */
    public function filterAndSort(FilterAndSortBookRequest $request)
    {
        $validated = $request->validated();
        $books = Book::bookFilterAndSort(
            $validated["txt_filter"],
            $validated["txt_sort"])
            ->reviewStats()
            ->simplePaginate(6);
        
        return view('index', compact('books'));
    }
}
