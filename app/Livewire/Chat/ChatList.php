<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Services\ChatService;
use Livewire\Component;
use Livewire\WithPagination;

class ChatList extends Component
{
    use WithPagination;

    public $search = '';
    public $activeConversationId = null;
    public $unreadCounts = [];

    protected $chatService;

    protected $listeners = [
        'echo-private:conversation.*,message.sent' => 'handleNewMessage',
        'message-sent' => 'refreshList',
        'conversation-selected' => 'setActiveConversation',
        'start-new-chat' => 'startNewChat'
    ];

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->loadUnreadCounts();
    }

    public function handleNewMessage($event)
    {
        $this->refreshList();

        // Play notification sound if not the active conversation
        if ($event['message']['conversation_id'] != $this->activeConversationId) {
            $this->dispatch('play-notification-sound');
        }
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->loadUnreadCounts();
    }

    public function loadUnreadCounts()
    {
        $conversations = Conversation::forUser(auth()->id())->get();

        foreach ($conversations as $conversation) {
            $this->unreadCounts[$conversation->id] = $conversation->unreadCountForUser(auth()->id());
        }
    }

    public function selectConversation($conversationId)
    {
        $this->activeConversationId = $conversationId;
        // Dispatch global event
        $this->dispatch('conversation-selected', $conversationId);
    }

    public function startNewChat()
    {
        try {

            // Create or get existing conversation with an admin
            $conversation = $this->chatService->getOrCreateStudentAdminChat(auth()->user());

            // Select the conversation
            $this->selectConversation($conversation->id);
            $this->refreshList();

            // Show success message
            $this->dispatch('toastr:success', message: 'Chat started successfully!');
        } catch (\Exception $e) {
            $this->dispatch('toastr:error', message: $e->getMessage());
        }
    }

    public function render()
    {
        $conversations = $this->chatService->getUserConversations(
            auth()->user(),
            20
        );

        return view('livewire.chat.chat-list', [
            'conversations' => $conversations,
            'activeConversationId' => $this->activeConversationId
        ]);
    }
}
