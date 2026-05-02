<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Remove this line if your table name is 'books' (plural)
     * Keep if your table name is different (e.g., 'book')
     */
    // protected $table = 'books';
    
    /**
     * The primary key associated with the table.
     * Use 'book_ID' if that's your primary key column
     * Use 'id' if that's your primary key column
     */
    // protected $primaryKey = 'book_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'price',
        'stock',
        'description',
        'cover_image',
        'publisher',
        'year'
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get all order items for this book
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'book_id');
    }

    /**
     * Get all rentals for this book
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'book_id');
    }

    /**
     * Get all reviews for this book
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'book_id');
    }

    /**
     * The categories that belong to this book (Many-to-Many)
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'book_id', 'category_id');
    }

    /**
     * Get all users who have this book in cart
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'book_id');
    }

    // ========== ACCESSORS (Formatters) ==========

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rs ' . number_format($this->price, 2);
    }

    /**
     * Get cover image URL
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('images/' . $this->cover_image);
        }
        return asset('images/default-cover.jpg');
    }

    /**
     * Get average rating from all reviews
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get star rating as integer (1-5)
     */
    public function getStarRatingAttribute()
    {
        return round($this->average_rating);
    }

    /**
     * Check if book is in stock
     */
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    /**
     * Get stock status text
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock > 10) {
            return 'In Stock';
        } elseif ($this->stock > 0) {
            return 'Low Stock (' . $this->stock . ' left)';
        } else {
            return 'Out of Stock';
        }
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if book is available for purchase
     */
    public function isAvailableForPurchase()
    {
        return $this->stock > 0;
    }

    /**
     * Check if book is available for rent
     */
    public function isAvailableForRent()
    {
        return $this->stock > 0;
    }

    /**
     * Reduce stock when purchased/rented
     */
    public function reduceStock($quantity = 1)
    {
        $this->stock -= $quantity;
        $this->save();
    }

    /**
     * Increase stock when returned
     */
    public function increaseStock($quantity = 1)
    {
        $this->stock += $quantity;
        $this->save();
    }

    /**
     * Get rental price (30% of selling price)
     */
    public function getRentalPriceAttribute()
    {
        return $this->price * 0.3;
    }

    /**
     * Get formatted rental price
     */
    public function getFormattedRentalPriceAttribute()
    {
        return 'Rs ' . number_format($this->rental_price, 2);
    }
}