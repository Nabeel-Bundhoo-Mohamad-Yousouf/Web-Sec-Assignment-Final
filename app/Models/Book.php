<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    protected $fillable = ['title', 'author', 'genre', 'rental_fee', 'img_url', 'description'];

    public function reviews() { return $this->hasMany(Review::class); }
    public function renters() { return $this->belongsToMany(User::class, 'book_user')->withTimestamps(); }
}
