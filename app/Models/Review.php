<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ["rating", "title", "review_description"];

    /**Get individual book review ratings */
    public function books (): BelongsTo
    {
        return $this->belongsTo(Book::class, "book_id", "book_id");
    }
}
