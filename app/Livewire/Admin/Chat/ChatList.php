<?php

namespace App\Livewire\Admin\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ChatList extends Component
{
    use WithPagination;

    public $search = '';
    public $activeConversationId = null;
    public $unreadCounts = [];
    public $filter = 'all';
    public $totalUnreadCount = 0;

    protected $chatService;

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->loadUnreadCounts();
        $this->calculateTotalUnread();
    }

    #[On('messageSent')]
    public function refreshList()
    {
        $this->resetPage();
        $this->loadUnreadCounts();
        $this->calculateTotalUnread();
    }

    #[On('conversationSelected')]
    public function setActiveConversation($conversationId)
    {
        $this->activeConversationId = $conversationId;
        $this->loadUnreadCounts();
    }

    #[On('newMessageReceived')]
    public function handleNewMessage($data)
    {
        $this->refreshList();
        
        // Play notification sound if not the active conversation
        if (isset($data['conversation_id']) && $data['conversation_id'] != $this->activeConversationId) {
            $this->dispatch('play-notification-sound');
            
            // Get sender name for toast
            $conversation = Conversation::find($data['conversation_id']);
            if ($conversation) {
                $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                $this->dispatch('toast', 
                    type: 'info',
                    message: 'New message from ' . ($otherUser ? $otherUser->full_name : 'a student')
                );
            }
        }
    }

    public function loadUnreadCounts()
    {
        $conversations = Conversation::forUser(auth()->id())->get();
        
        foreach ($conversations as $conversation) {
            $this->unreadCounts[$conversation->id] = $conversation->unreadCountForUser(auth()->id());
        }
    }

    public function calculateTotalUnread()
    {
        $this->totalUnreadCount = array_sum($this->unreadCounts);
    }

    public function selectConversation($conversationId)
    {
        $this->activeConversationId = $conversationId;
        $this->dispatch('conversationSelected', conversationId: $conversationId)
            ->to('admin.chat.chat-box');
    }

    public function showStudentList()
    {
        $this->dispatch('showStudentList')->to('admin.chat.chat-box');
    }

    public function startChatWithStudent($studentId)
    {
        // This is handled by ChatBox component now
        $this->dispatch('startChatWithStudent', studentId: $studentId)
            ->to('admin.chat.chat-box');
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function getConversationsProperty()
    {
        $query = Conversation::forUser(auth()->id())
            ->with(['lastMessage', 'participants.user', 'creator'])
            ->orderBy('last_message_at', 'desc');

        if ($this->filter === 'unread') {
            $query->whereHas('messages', function($q) {
                $q->where('sender_id', '!=', auth()->id())
                  ->whereNull('read_at');
            });
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhereHas('participants.user', function($userQuery) {
                      $userQuery->where('first_name', 'like', "%{$this->search}%")
                               ->orWhere('last_name', 'like', "%{$this->search}%")
                               ->orWhere('email', 'like', "%{$this->search}%");
                  });
            });
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.chat.chat-list', [
            'conversations' => $this->conversations,
            'activeConversationId' => $this->activeConversationId,
            'unreadCounts' => $this->unreadCounts,
            'totalUnreadCount' => $this->totalUnreadCount,
            'filter' => $this->filter,
        ]);
    }
}