<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tour extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'thumbnail', 'gallery',
        'price', 'hpp_price', 'commission_percentage', 'location', 'duration',
        'short_description', 'description', 'itinerary', 'facilities', 'terms', 'is_active'
    ];

    protected $casts = [
        'gallery'               => 'array',
        'is_active'             => 'boolean',
        'price'                 => 'integer',
        'hpp_price'             => 'integer',
        'commission_percentage' => 'float',
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

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function getPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getHppPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->hpp_price ?? 0, 0, ',', '.');
    }

    public function getMarginAmountAttribute()
    {
        return max(0, $this->price - ($this->hpp_price ?? 0));
    }

    public function getMarginAmountFormattedAttribute()
    {
        return 'Rp ' . number_format($this->margin_amount, 0, ',', '.');
    }

    public function getEstimatedCommissionAttribute()
    {
        return round(($this->margin_amount * ($this->commission_percentage ?? 0)) / 100);
    }

    public function getEstimatedCommissionFormattedAttribute()
    {
        return 'Rp ' . number_format($this->estimated_commission, 0, ',', '.');
    }
}
