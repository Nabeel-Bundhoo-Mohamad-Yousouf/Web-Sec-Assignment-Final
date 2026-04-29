<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SearchBookRequest;
use App\Http\Resources\BookResource;
use Nette\Schema\ValidationException;

class BookController extends Controller
{
    /**Performs book search*/
    public function search(SearchBookRequest $request)
    {
        try {
            $validated = $request->validated();
            $books = BookResource::collection(
                Book::bookSearch($validated["txt_search"])
                    ->limit(10)
                    ->get()
            );

            return response() -> json ([
                "message" => "success",
                "data" => $books
            ], 200);

        } catch (ValidationException) {
            return response() -> json ([
                "message" => "error",
                "description" => "Book not found"
                ], 422);
        } catch (\Exception $e) {
            return response() -> json ([
                "message" => "error",
                "description" => "Server error"
                ], 500);
        }
    }
}