<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'book_id',
        'quantity',
        'price',
        'type'  // 'buy' or 'rent'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the order that this item belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the book for this order item
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Get subtotal for this item (quantity × price)
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rs ' . number_format($this->subtotal, 2);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rs ' . number_format($this->price, 2);
    }

    /**
     * Get item type badge
     */
    public function getTypeBadgeAttribute()
    {
        if ($this->type === 'buy') {
            return '<span class="badge bg-primary">Purchase</span>';
        }
        return '<span class="badge bg-info">Rental</span>';
    }

    /**
     * Check if this is a rental item
     */
    public function getIsRentalAttribute()
    {
        return $this->type === 'rent';
    }

    /**
     * Check if this is a purchase item
     */
    public function getIsPurchaseAttribute()
    {
        return $this->type === 'buy';
    }

    // ========== HELPER METHODS ==========

    /**
     * Update quantity
     */
    public function updateQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;
        $this->save();
        
        // Update order total
        $this->order->calculateTotal();
        
        return $this;
    }

    /**
     * Update price
     */
    public function updatePrice($newPrice)
    {
        $this->price = $newPrice;
        $this->save();
        
        // Update order total
        $this->order->calculateTotal();
        
        return $this;
    }

    /**
     * Get rental duration (if applicable)
     * You can extend this based on your rental logic
     */
    public function getRentalDurationAttribute()
    {
        if ($this->type === 'rent') {
            // Default 14 days rental
            return 14;
        }
        return null;
    }

    /**
     * Get expected return date for rentals
     */
    public function getExpectedReturnDateAttribute()
    {
        if ($this->type === 'rent') {
            return $this->created_at->addDays($this->rental_duration);
        }
        return null;
    }
}