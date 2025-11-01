<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Sermon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'scripture_reference',
        'video_path',
        'audio_path',
        'thumbnail_path',
        'duration',
        'date_preached',
        'featured',
        'view_count',
        'download_count',
        'speaker_name',
        'series_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_preached' => 'datetime',
        'featured' => 'boolean',
        'duration' => 'integer',
        'view_count' => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * Append computed attributes to the model.
     *
     * @var array
     */
    protected $appends = ['formatted_duration'];

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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sermon) {
            $sermon->slug = Str::slug($sermon->title);

            // Make sure slug is unique
            $count = static::whereRaw("slug REGEXP '^{$sermon->slug}(-[0-9]+)?$'")->count();

            if ($count > 0) {
                $sermon->slug = "{$sermon->slug}-{$count}";
            }
        });

        // Note: Cloudinary files are managed by Cloudinary's lifecycle policies
        // No manual cleanup needed on model deletion
    }

    /**
     * Get the formatted duration attribute.
     *
     * @return string
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return '00:00';
        }

        // Duration is stored in seconds
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Scope a query to only include featured sermons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Get the speaker that belongs to the sermon.
     */


    /**
     * Get the series that the sermon belongs to.
     */
    public function series()
    {
        return $this->belongsTo(SermonSeries::class, 'series_id');
    }

    /**
     * Get the topics for the sermon.
     */
    public function topics()
    {
        return $this->belongsToMany(SermonTopic::class, 'sermon_sermon_topic');
    }

    /**
     * Get the users who favorited this sermon.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'sermon_favorites')
                    ->withTimestamps();
    }

    /**
     * Increment the view count.
     *
     * @return void
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Increment the download count.
     *
     * @return void
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Check if a sermon has video.
     *
     * @return bool
     */
    public function hasVideo()
    {
        return !empty($this->video_path);
    }

    /**
     * Check if a sermon has audio.
     *
     * @return bool
     */
    public function hasAudio()
    {
        return !empty($this->audio_path);
    }

    /**
     * Get related sermons based on topics.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function relatedSermons($limit = 3)
    {
        $topicIds = $this->topics->pluck('id');

        return self::whereHas('topics', function ($query) use ($topicIds) {
                $query->whereIn('sermon_topics.id', $topicIds);
            })
            ->where('id', '!=', $this->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
    // In Sermon.php model



    
}
