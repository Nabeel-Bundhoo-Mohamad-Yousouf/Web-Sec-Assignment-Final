<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display shopping cart page
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $total = 0;
        
        if (!empty($cart)) {
            $bookIds = array_keys($cart);
            $books = Book::whereIn('id', $bookIds)->get();
            
            foreach ($books as $book) {
                $cartItem = $cart[$book->id];
                
                // Calculate price based on type (Buy or Rent)
                if ($cartItem['type'] == 'Rent') {
                    $price = $book->price * 0.3; // 30% for rent
                } else {
                    $price = $book->price;
                }
                
                $subtotal = $cartItem['quantity'] * $price;
                
                $items[] = (object)[
                    'book_ID' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'price' => $book->price,
                    'quantity' => $cartItem['quantity'],
                    'type' => $cartItem['type'],
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }
        
        return view('cart.shopcart', compact('items', 'total'));
    }
    
    /**
     * Add book to cart (AJAX)
     */
    public function add(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:Buy,Rent'
        ]);
        
        $book = Book::find($request->book_id);
        
        // Check stock
        if ($book->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $book->stock . ' left.'
            ]);
        }
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$request->book_id])) {
            $cart[$request->book_id]['quantity'] += $request->quantity;
        } else {
            $cart[$request->book_id] = [
                'quantity' => $request->quantity,
                'type' => $request->type
            ];
        }
        
        session()->put('cart', $cart);
        
        // Calculate new total for response
        $total = $this->calculateTotal();
        
        return response()->json([
            'success' => true,
            'message' => 'Book added to cart',
            'cart_count' => count($cart),
            'total' => 'Rs ' . number_format($total, 2)
        ]);
    }
    
    /**
     * Update cart - handles both quantity and type changes
     * This is the main update function for your updatecart.php
     */
    public function update(Request $request)
    {
        $cart = session()->get('cart', []);
        
        // Update each item in cart
        if ($request->has('qty')) {
            foreach ($request->qty as $id => $qty) {
                $type = $request->type[$id] ?? 'Buy';
                
                if ($qty <= 0) {
                    // Remove item if quantity is 0 or negative
                    unset($cart[$id]);
                } else {
                    // Update quantity and type
                    $cart[$id] = [
                        'qty' => (int)$qty,
                        'type' => $type
                    ];
                }
            }
        }
        
        // Handle single item update (AJAX)
        if ($request->has('book_id')) {
            $bookId = $request->book_id;
            $quantity = $request->quantity;
            $type = $request->type ?? 'Buy';
            
            if ($quantity <= 0) {
                unset($cart[$bookId]);
            } else {
                $cart[$bookId] = [
                    'quantity' => $quantity,
                    'type' => $type
                ];
            }
        }
        
        session()->put('cart', $cart);
        
        // Calculate new totals
        $items = [];
        $total = 0;
        $cartCount = count($cart);
        
        if (!empty($cart)) {
            $bookIds = array_keys($cart);
            $books = Book::whereIn('id', $bookIds)->get();
            
            foreach ($books as $book) {
                $cartItem = $cart[$book->id];
                $price = $cartItem['type'] == 'Rent' ? $book->price * 0.3 : $book->price;
                $subtotal = $cartItem['quantity'] * $price;
                $total += $subtotal;
                
                $items[] = (object)[
                    'book_ID' => $book->id,
                    'title' => $book->title,
                    'quantity' => $cartItem['quantity'],
                    'type' => $cartItem['type'],
                    'subtotal' => $subtotal
                ];
            }
        }
        
        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'total' => 'Rs ' . number_format($total, 2),
                'items' => $items
            ]);
        }
        
        // Redirect for form submission
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully');
    }
    
    /**
     * Remove single item from cart
     */
    public function remove($bookId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$bookId]);
        session()->put('cart', $cart);
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }
    
    /**
     * Clear entire cart
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared');
    }
    
    /**
     * Get cart count (for navbar badge)
     */
    public function getCount()
    {
        $cart = session()->get('cart', []);
        return response()->json(['count' => count($cart)]);
    }
    
    /**
     * Calculate cart total
     */
    private function calculateTotal()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        
        if (!empty($cart)) {
            $bookIds = array_keys($cart);
            $books = Book::whereIn('id', $bookIds)->get();
            
            foreach ($books as $book) {
                $cartItem = $cart[$book->id];
                $price = $cartItem['type'] == 'Rent' ? $book->price * 0.3 : $book->price;
                $total += $cartItem['quantity'] * $price;
            }
        }
        
        return $total;
    }
}