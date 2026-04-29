<?php

namespace App\Models;

use App\Http\Resources\BookResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $primaryKey = 'book_id';

    //fields that can be added/updated
    protected $fillable = [
        "title", "author", "genre", "book_description",
        "price", "rental_fee", "stock_num", "img_url"
    ];

    /** Get individual book review ratings */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'book_id', 'book_id');
    }
    
    /** Includes average rating dynamically */
    public function scopeReviewStats($query)
    {
        return $query ->withAvg("reviews", "rating")
                      ->withCount("reviews");
    }

    /** Peforms book search */
    public function scopeBookSearch($query, $search_term)
    {
        if(!empty($search_term)) {
            $query->where(function ($q) use ($search_term) {
                $q->where("title", "LIKE", "%{$search_term}%")
                    ->orWhere("author", "LIKE", "%{$search_term}%");
            });
            return $query;
        }
        else {
            return $query;
        }
    }

    /** Peforms books filter */
    public function scopeBookFilter($query, $filter)
    {
        if(!empty($filter)) {
            return $query-> where("genre", $filter);
        }
        else {
            return $query;
        }
    }

    /** Peforms books sort */
    public function scopeBookSort($query, $sort)
    {
        $allowed_sort = [
            "title" => ["title", "asc"], 
            "author" => ["author", "asc"], 
            "price_asc" => ["price", "asc"], 
            "price_desc" => ["price", "desc"],
            "latest" => ["created_at", "asc"]
            ];

        if (array_key_exists ($sort, $allowed_sort)) {
            [$column, $direction] = $allowed_sort[$sort];
        }
        else {
            [$column, $direction] =  ["title", "asc"];
        }
        return $query -> orderBy($column, $direction);
    }

    /** Applies sort + filter */
    public function scopeBookFilterAndSort($query, $filter, $sort)
    {
        return $query ->scopeBookFilter($filter)
                    ->scopeBookSort($sort);
    }

}
