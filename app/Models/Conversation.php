<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'title',
        'type',
        'status',
        'created_by',
        'last_message_id',
        'last_message_at',
        'metadata'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array'
    ];

     /**
     * Get the user who created the conversation
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all participants in the conversation
     */
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * Get the users through participants
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['role', 'last_read_at', 'is_muted', 'is_pinned'])
            ->withTimestamps();
    }

    /**
     * Get all messages in the conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the last message
     */
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Get unread messages count for a specific user
     */
    public function unreadCountForUser($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->where('sender_id', '!=', $userId)->count();
        }

        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('created_at', '>', $participant->last_read_at)
            ->count();
    }

    /**
     * Scope for user's conversations
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Create a direct conversation between two users
     */
    public static function createDirectConversation($user1Id, $user2Id, $title = null)
    {
        // Check if direct conversation already exists
        $existing = self::where('type', 'direct')
            ->whereHas('participants', function ($q) use ($user1Id) {
                $q->where('user_id', $user1Id);
            })
            ->whereHas('participants', function ($q) use ($user2Id) {
                $q->where('user_id', $user2Id);
            })
            ->first();

        if ($existing) {
            // Reactivate if archived
            if ($existing->status === 'archived') {
                $existing->update(['status' => 'active']);
            }
            return $existing;
        }

        $conversation = self::create([
            'title' => $title,
            'type' => 'direct',
            'created_by' => $user1Id,
            'status' => 'active'
        ]);

        $conversation->participants()->createMany([
            ['user_id' => $user1Id, 'role' => 'member'],
            ['user_id' => $user2Id, 'role' => 'member']
        ]);

        return $conversation;
    }
}
