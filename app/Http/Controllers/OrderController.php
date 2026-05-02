<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        $items = [];
        $total = 0;
        
        $bookIds = array_keys($cart);
        $books = Book::whereIn('id', $bookIds)->get();
        
        foreach ($books as $book) {
            $cartItem = $cart[$book->id];
            
            if ($cartItem['type'] == 'Rent') {
                $price = $book->price * 0.3;
            } else {
                $price = $book->price;
            }
            
            $subtotal = $cartItem['quantity'] * $price;
            
            $items[] = (object)[
                'book_ID' => $book->id,
                'title' => $book->title,
                'quantity' => $cartItem['quantity'],
                'type' => $cartItem['type'],
                'price' => $price,
                'subtotal' => $subtotal
            ];
            $total += $subtotal;
        }
        
        return view('cart.checkout', compact('items', 'total'));
    }
    
    /**
     * Process payment and create order
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'card_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required|string',
            'card_number' => 'required|string|min:16',
            'expiry' => 'required',
            'cvv' => 'required|string|min:3'
        ]);
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate total
            $total = 0;
            $orderItems = [];
            $bookIds = array_keys($cart);
            $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');
            
            foreach ($cart as $id => $item) {
                $book = $books[$id];
                $price = $item['type'] == 'Rent' ? $book->price * 0.3 : $book->price;
                $subtotal = $item['quantity'] * $price;
                $total += $subtotal;
                
                $orderItems[] = [
                    'book_id' => $id,
                    'quantity' => $item['quantity'],
                    'type' => $item['type'],
                    'price' => $price
                ];
                
                // Reduce stock
                $book->reduceStock($item['quantity']);
            }
            
            // Create order
            $order = Order::create([
                'user_id' => auth()->id() ?? null,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $total,
                'status' => 'pending',
                'shipping_address' => $request->address,
                'payment_method' => 'card',
                'payment_status' => 'paid'
            ]);
            
            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'type' => $item['type']
                ]);
            }
            
            // Clear cart
            session()->forget('cart');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'redirect' => route('payment.completed', ['order' => $order->order_number])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show payment completed page
     */
    public function paymentCompleted($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();
        
        return view('cart.payment_completed', [
            'orderNumber' => $orderNumber,
            'total' => $order->total_amount ?? 0
        ]);
    }
}