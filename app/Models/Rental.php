<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rental_date',
        'return_date',
        'expected_return_date',
        'status', // active, returned, overdue
        'rental_fee',
        'late_fee'
    ];

    protected $casts = [
        'rental_date' => 'datetime',
        'return_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'rental_fee' => 'decimal:2',
        'late_fee' => 'decimal:2'
    ];

    // ========== RELATIONSHIPS ==========

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // ========== ACCESSORS ==========

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'active' && $this->expected_return_date < now()) {
            return true;
        }
        return false;
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->is_overdue) {
            return now()->diffInDays($this->expected_return_date);
        }
        return 0;
    }

    // ========== HELPER METHODS ==========

    public function markAsReturned()
    {
        $this->status = 'returned';
        $this->return_date = now();
        
        // Calculate late fee if applicable
        if ($this->is_overdue) {
            $lateFeePerDay = 50; // Rs 50 per day late
            $this->late_fee = $this->days_overdue * $lateFeePerDay;
        }
        
        $this->save();
        
        // Increase book stock
        $this->book->increaseStock();
        
        return $this;
    }
}
