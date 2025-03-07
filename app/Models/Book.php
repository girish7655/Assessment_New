<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author_id',
        'publisher_id',
        'category_id',
        'isbn',
        'published_year',
        'description',
        'created_by',
        'quantity',
        'available_quantity',
        'status'
    ];

    protected $attributes = [
        'quantity' => 1,
        'available_quantity' => 1,
        'status' => 'available'
    ];

    protected $casts = [
        'published_year' => 'integer',
        'quantity' => 'integer',
        'available_quantity' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        $averageRating = cache()->remember(
            "book:{$this->id}:average_rating",
            now()->addDay(),
            fn() => $this->reviews()->avg('rating') ?? 0
        );
        
        return round($averageRating, 1);
    }

    public static function validateUnique(string $title, int $authorId, ?int $excludeId = null): void
    {
        $query = static::query()
            ->where('title', $title)
            ->where('author_id', $authorId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'title' => ['A book with this title and author already exists in the system.']
            ]);
        }
    }

    // Add this accessor
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available' && $this->available_quantity > 0;
    }

    public function markAsCheckedOut(): void
    {
        $this->available_quantity = max(0, $this->available_quantity - 1);
        $this->status = $this->available_quantity > 0 ? 'available' : 'checked_out';
        $this->save();
    }

    public function markAsReturned(): void
    {
        $this->available_quantity = min($this->quantity, $this->available_quantity + 1);
        $this->status = 'available';
        $this->save();
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(BookCheckout::class);
    }

    public function hasBeenCheckedOutByUser(User $user): bool
    {
        return $this->checkouts()
            ->where('user_id', $user->id)
            ->exists();
    }
}
