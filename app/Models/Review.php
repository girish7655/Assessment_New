<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'message',
        'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            // Clear any cached average ratings
            cache()->forget("book:{$review->book_id}:average_rating");
        });

        static::updated(function ($review) {
            cache()->forget("book:{$review->book_id}:average_rating");
        });

        static::deleted(function ($review) {
            cache()->forget("book:{$review->book_id}:average_rating");
        });
    }

    public function validateRating(): bool
    {
        return $this->rating >= 1 && $this->rating <= 5;
    }
}