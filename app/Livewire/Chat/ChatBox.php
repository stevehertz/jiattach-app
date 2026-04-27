<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Services\ChatService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ChatBox extends Component
{
    use WithPagination, WithFileUploads;

    public $conversationId;
    public $messageBody = '';
    public $attachments = [];
    public $conversation;
    public $isLoading = true;

    protected $chatService;

    // Changed this - use a method to get the dynamic channel name
    protected function getListeners()
    {
        $listeners = [
            'conversation-selected' => 'loadConversation',
            'messageReceived' => 'refreshMessages',
            'startNewChat' => 'startNewChat',
        ];

        // Only add echo listener if we have a conversation
        if ($this->conversationId) {
            $channel = 'conversation.' . $this->conversationId;
            $listeners["echo-private:{$channel},message.sent"] = 'handleNewMessage';
            $listeners["echo-private:{$channel},message.read"] = 'handleMessageRead';
        }

        return $listeners;
    }


    protected $rules = [
        'messageBody' => 'required|string|max:5000',
        'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx'
    ];

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function loadConversation($conversationId)
    {
        $this->conversationId = $conversationId;
        $this->conversation = Conversation::with(['participants.user'])->find($conversationId);
        $this->isLoading = false;
        $this->resetPage();

        // Mark as read
        if ($this->conversation) {
            $this->chatService->markAsRead($this->conversation, auth()->user());
        }
    }

    public function startNewChat()
    {
        // Dispatch global event for ChatList to handle
        $this->dispatch('start-new-chat');
    }

    public function handleNewMessage($event)
    {
        if (isset($event['message']) && $event['message']['conversation_id'] == $this->conversationId) {
            $this->refreshMessages();
            $this->chatService->markAsRead($this->conversation, auth()->user());
        }
    }

    public function receiveMessage($event)
    {
        if ($event['message']['conversation_id'] == $this->conversationId) {
            $this->resetPage();

            // Mark as read if this is the active conversation
            $this->chatService->markAsRead($this->conversation, auth()->user());
        }
    }

    public function handleMessageRead($event)
    {
        // Update read receipts in UI if needed
        $this->dispatch('message-read', userId: $event['userId'], readAt: $event['lastReadAt']);
    }

    public function refreshMessages()
    {
        $this->resetPage();
    }


    public function sendMessage()
    {
        $this->validate([
            'messageBody' => 'required|string|max:5000',
        ]);

        if (!$this->conversation) {
            session()->flash('error', 'No conversation selected');
            return;
        }

        try {

            $message = $this->chatService->sendMessage(
                $this->conversation,
                auth()->user(),
                $this->messageBody,
                'text',
                $this->processAttachments()
            );

            $this->messageBody = '';
            $this->attachments = [];
            $this->resetPage();

            // Dispatch global event to update chat list
            $this->dispatch('message-sent');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    protected function processAttachments()
    {
        $uploadedFiles = [];

        foreach ($this->attachments as $attachment) {
            $path = $attachment->store('chat-attachments', 'public');
            $uploadedFiles[] = [
                'name' => $attachment->getClientOriginalName(),
                'path' => $path,
                'type' => $attachment->getMimeType(),
                'size' => $attachment->getSize()
            ];
        }

        return $uploadedFiles;
    }

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function getMessagesProperty()
    {
        if (!$this->conversation) {
            return collect();
        }

        return $this->chatService->getConversationMessages($this->conversation, 50);
    }

    public function render()
    {
        return view('livewire.chat.chat-box', [
            'messages' => $this->messages
        ]);
    }
}
