<?php
namespace App\Services;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Get or create direct conversation between student and admin
     */
    public function getOrCreateStudentAdminChat(User $student)
    {
        // Find any active admin to assign
        $admin = User::role(['admin', 'super-admin'])->first();

        if (!$admin) {
            throw new \Exception('No admin available for chat');
        }

        return Conversation::createDirectConversation(
            $student->id,
            $admin->id,
            "Support Chat - {$student->full_name}"
        );
    }

    /**
     * Send a message
     */
    public function sendMessage(Conversation $conversation, User $sender, string $body, $type = 'text', $attachments = [])
    {
        if (!$conversation->participants()->where('user_id', $sender->id)->exists()) {
            throw new \Exception('User is not a participant in this conversation');
        }

        return DB::transaction(function () use ($conversation, $sender, $body, $type, $attachments) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'body' => $body,
                'type' => $type,
                'attachments' => $attachments,
                'delivered_at' => now(),
            ]);

            // Update conversation's last message
            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
            ]);

            // Broadcast the message
            broadcast(new MessageSent($message))->toOthers();

            // Log activity
            activity_log(
                "Message sent in conversation #{$conversation->id}",
                'message_sent',
                ['conversation_id' => $conversation->id, 'message_id' => $message->id],
                'chat'
            );

            return $message;
        });
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead(Conversation $conversation, User $user)
    {
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if ($participant) {
            $participant->markAsRead();

            // Mark all messages as read
            Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Broadcast read event
            broadcast(new MessageRead($conversation->id, $user->id))->toOthers();
        }
    }

    /**
     * Get conversations for a user
     */
    public function getUserConversations(User $user, $perPage = 20)
    {
        return Conversation::forUser($user->id)
            ->with(['lastMessage', 'participants.user'])
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at');
            }])
            ->orderBy('last_message_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get messages for a conversation
     */
    public function getConversationMessages(Conversation $conversation, $perPage = 50)
    {
        return $conversation->messages()
            ->with(['sender', 'reactions'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get available admins for chat
     */
    public function getAvailableAdmins()
    {
        return User::role(['admin', 'super-admin'])
            ->where('is_active', true)
            ->get();
    }
}
