<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'status',
        'firebase_msg_id',
        'file_url',
        'file_name',
        'file_type',
        'deleted',
        'deleted_by',
        'deleted_at',
        'original_message',
        'hidden_for_user',
        'hidden_at'
    ];

    protected $casts = [
        'deleted' => 'boolean',
        'deleted_at' => 'datetime',
        'hidden_at' => 'datetime'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('h:i A');
    }
}
