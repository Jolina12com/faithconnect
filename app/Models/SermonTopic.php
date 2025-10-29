<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SermonTopic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            $topic->slug = Str::slug($topic->name);

            // Make sure slug is unique
            $count = static::whereRaw("slug REGEXP '^{$topic->slug}(-[0-9]+)?$'")->count();

            if ($count > 0) {
                $topic->slug = "{$topic->slug}-{$count}";
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
     * Get the sermons for the topic.
     */
    public function sermons()
    {
        return $this->belongsToMany(Sermon::class, 'sermon_sermon_topic');
    }

    /**
     * Get the topic's sermon count.
     *
     * @return int
     */
    public function getSermonCountAttribute()
    {
        return $this->sermons()->count();
    }
}
