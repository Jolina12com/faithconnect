<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SermonSeries extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sermon_series';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($series) {
            $series->slug = Str::slug($series->title);

            // Make sure slug is unique
            $count = static::whereRaw("slug REGEXP '^{$series->slug}(-[0-9]+)?$'")->count();

            if ($count > 0) {
                $series->slug = "{$series->slug}-{$count}";
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the sermons for the series.
     */
    public function sermons()
    {
        return $this->hasMany(Sermon::class, 'series_id');
    }

    /**
     * Get the series' sermon count.
     *
     * @return int
     */
    public function getSermonCountAttribute()
    {
        return $this->sermons()->count();
    }

    /**
     * Check if series is active.
     *
     * @return bool
     */
    public function isActive()
    {
        if (!$this->end_date) {
            return true;
        }

        return $this->end_date->isFuture();
    }

    /**
     * Scope a query to only include active series.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }
}
