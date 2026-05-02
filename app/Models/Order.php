<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'shipping_address',
        'payment_method',
        'payment_status',
        'shipping_cost',
        'tax',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the user who placed this order
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all items in this order
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'shipped' => 'primary'
        ];
        
        $color = $badges[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

    /**
     * Get formatted order number
     */
    public function getFormattedOrderNumberAttribute()
    {
        return '#' . $this->order_number;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rs ' . number_format($this->total_amount, 2);
    }

    /**
     * Get order summary (item count)
     */
    public function getItemCountAttribute()
    {
        return $this->items()->sum('quantity');
    }

    // ========== HELPER METHODS ==========

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . '-' . $date . '-' . $random;
    }

    /**
     * Calculate total amount from items
     */
    public function calculateTotal()
    {
        $subtotal = $this->items()->sum(\DB::raw('quantity * price'));
        $this->total_amount = $subtotal + $this->shipping_cost + $this->tax;
        $this->save();
        
        return $this->total_amount;
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Update order status
     */
    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->save();
        
        return $this;
    }
}