<?php

namespace App\Models;

use App\Notifications\NewSermonNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'password', 'phone_number', 'date_of_birth', 'gender',
        'address', 'marital_status', 'emergency_contact',
        'membership_status', 'date_of_membership',
        'baptism_date',
        'profile_picture', 
         'password_changed', 'created_by_admin', 'is_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed' => 'boolean',
            'created_by_admin' => 'boolean',
            'is_admin' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }
    
    /**
     * Get the full name attribute
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' . 
            ($this->middle_name ? $this->middle_name . ' ' : '') . 
            $this->last_name
        );
    }
    
    /**
     * Get the member associated with the user.
     */
    public function member()
    {
        return $this->hasOne(Member::class);
    }
    
    public function hasPermissionToStream()
    {
        // Your logic here - examples:
        return $this->role === 'broadcaster';
        // OR return $this->hasPermission('stream');
        // OR return in_array($this->id, [1, 2, 3]); // specific users
    }
    
    public function favoriteSermons()
    {
        return $this->belongsToMany(Sermon::class, 'sermon_favorites')
                    ->withTimestamps();
    }

    /**
     * Check if the user has favorited a specific sermon.
     *
     * @param int $sermonId
     * @return bool
     */
    public function hasFavoritedSermon($sermonId)
    {
        return $this->favoriteSermons()->where('sermon_id', $sermonId)->exists();
    }

    /**
     * Toggle favorite status for a sermon.
     *
     * @param int $sermonId
     * @return array
     */
    public function toggleFavoriteSermon($sermonId)
    {
        $favorites = $this->favoriteSermons();

        if ($favorites->where('sermon_id', $sermonId)->exists()) {
            $favorites->detach($sermonId);
            return [
                'status' => 'removed',
                'message' => 'Sermon removed from favorites'
            ];
        } else {
            $favorites->attach($sermonId);
            return [
                'status' => 'added',
                'message' => 'Sermon added to favorites'
            ];
        }

    // ✅ Relationship sa notifications

    }

    public function chatbotAnalytics()
    {
        return $this->hasMany(ChatbotAnalytics::class);
    }

    /**
     * Get the name attribute
     *
     * @return string
     */
public function getNameAttribute()
{
    return trim("{$this->first_name} {$this->last_name}");
}

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Check if user is a member (not admin)
     *
     * @return bool
     */
    public function isMember()
    {
        return !$this->is_admin;
    }
    

    
    /**
     * Messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    
}
