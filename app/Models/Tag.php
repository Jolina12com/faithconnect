<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the sermons for the tag.
     */
    public function sermons()
    {
        return $this->belongsToMany(Sermon::class, 'sermon_tag');
    }

    /**
     * Get the sermon count for this tag.
     *
     * @return int
     */
    public function getSermonCountAttribute()
    {
        return $this->sermons()->count();
    }
}
