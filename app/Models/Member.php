<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    const STATUS_NEW_MEMBER = 'new_member';
    const STATUS_ACTIVE_MEMBER = 'active_member';

    protected $fillable = [
        'user_id',
        'phone_number',
        'date_of_birth',
        'gender',
        'address',
        'marital_status',
        'emergency_contact',
        'membership_status',
        'date_of_membership',
        'baptism_date',
        'profile_picture',
    ];

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW_MEMBER => 'New Member',
            self::STATUS_ACTIVE_MEMBER => 'Member',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->membership_status) {
            self::STATUS_NEW_MEMBER => '<span class="badge bg-success text-white rounded-pill px-3 py-2"><i class="bi bi-star me-1"></i> New Member</span>',
            self::STATUS_ACTIVE_MEMBER => '<span class="badge bg-success text-white rounded-pill px-3 py-2"><i class="bi bi-person-check me-1"></i> Member</span>',
            default => '<span class="badge bg-secondary text-white rounded-pill px-3 py-2"><i class="bi bi-question-circle me-1"></i> N/A</span>',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
