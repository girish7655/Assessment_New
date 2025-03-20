<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birth_date',
        'nationality',
        'created_by'
    ];

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($author) {
            // Additional validation or processing before creation
            static::validateUnique($author);
        });

        static::updating(function ($author) {
            // Additional validation or processing before updating
            static::validateUnique($author);
        });
    }

    /**
     * Validate that the author is unique based on name, birth_date, and nationality
     */
    private static function validateUnique($author)
    {
        $query = static::where('name', $author->name)
            ->where('birth_date', $author->birth_date)
            ->where('nationality', $author->nationality)
            ->where('created_by', auth()->id())
            ->whereNull('deleted_at');

        if ($author->exists) {
            $query->where('id', '!=', $author->id);
        }

        if ($query->exists()) {
            throw new \Exception('You already have an author with these details.');
        }
    }

    protected function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = $value ?: null;
    }
}
