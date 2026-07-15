<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tour extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'thumbnail', 'gallery',
        'price', 'location', 'duration', 'short_description',
        'description', 'itinerary', 'facilities', 'terms', 'is_active'
    ];

    protected $casts = [
        'gallery'   => 'array',
        'is_active' => 'boolean',
        'price'     => 'integer',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
