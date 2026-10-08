<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'tag',
        'subtitle',
        'image',
        'mobile_image',
        'tour_id',
        'link_url',
        'button_text',
        'order',
        'is_active',
        'is_popup',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_popup'   => 'boolean',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'order'      => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->latest('id');
    }

    public function getTargetUrlAttribute(): string
    {
        if ($this->tour) {
            return route('tours.show', $this->tour->slug);
        }

        if (!empty($this->link_url)) {
            return $this->link_url;
        }

        return '#';
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('images/default-banner.jpg');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function getMobileImageUrlAttribute(): string
    {
        if (!empty($this->mobile_image)) {
            if (str_starts_with($this->mobile_image, 'http://') || str_starts_with($this->mobile_image, 'https://')) {
                return $this->mobile_image;
            }
            return asset('storage/' . $this->mobile_image);
        }

        return $this->image_url;
    }
}
