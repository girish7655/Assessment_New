<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->created_by)) {
                $category->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Keeping createdBy as an alias for backward compatibility
    public function createdBy(): BelongsTo
    {
        return $this->creator();
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
