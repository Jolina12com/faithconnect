<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'published_at',
        'is_pinned',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];
}
