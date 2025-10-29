<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'event_date', 'location', 'event_time', 'color',
        'event_type', 'groom_id', 'bride_id', 'groom_name', 'bride_name',
        'officiating_minister', 'witnesses', 'person_id', 'person_name',
        'birth_date', 'godparents', 'parents', 'is_child', 'status', 'notes'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime',
        'birth_date' => 'date',
        'is_child' => 'boolean',
    ];

    public function poll()
    {
        return $this->hasOne(EventPoll::class);
    }

    public function polls()
    {
        return $this->hasMany(EventPoll::class);
    }

    public function groom()
    {
        return $this->belongsTo(User::class, 'groom_id');
    }

    public function bride()
    {
        return $this->belongsTo(User::class, 'bride_id');
    }

    public function person()
    {
        return $this->belongsTo(User::class, 'person_id');
    }

    /**
     * Scope a query to only include wedding events.
     */
    public function scopeWeddings($query)
    {
        return $query->where('event_type', 'wedding');
    }

    /**
     * Scope a query to only include baptism events.
     */
    public function scopeBaptisms($query)
    {
        return $query->where('event_type', 'baptism');
    }

    /**
     * Scope a query to only include regular events.
     */
    public function scopeRegular($query)
    {
        return $query->where('event_type', 'regular');
    }

    /**
     * Check if the event is a wedding.
     */
    public function isWedding()
    {
        return $this->event_type === 'wedding';
    }

    /**
     * Check if the event is a baptism.
     */
    public function isBaptism()
    {
        return $this->event_type === 'baptism';
    }

    /**
     * Check if the event is a regular event.
     */
    public function isRegular()
    {
        return $this->event_type === 'regular';
    }
}
