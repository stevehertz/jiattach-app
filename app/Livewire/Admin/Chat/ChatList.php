<?php

namespace App\Livewire\Admin\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use Livewire\Component;
use Livewire\WithPagination;

class ChatList extends Component
{
    use WithPagination;

    public $search = '';
    public $activeConversationId = null;
    public $unreadCounts = [];
    public $filter = 'all'; // all, unread, students, admins
    public $totalUnreadCount = 0;

    protected $chatService;
    
    protected $listeners = [
        'messageSent' => 'refreshList',
        'conversationSelected' => 'setActiveConversation',
        'startNewChat' => 'showStudentList',
        'refreshChatList' => 'refreshList'
    ];

     public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->loadUnreadCounts();
        $this->calculateTotalUnread();
    }

    public function handleNewMessage($event)
    {
        $this->refreshList();
        
        // Play notification sound if not the active conversation
        if (isset($event['message']['conversation_id']) && $event['message']['conversation_id'] != $this->activeConversationId) {
            $this->dispatchBrowserEvent('play-notification-sound');
            $this->dispatchBrowserEvent('toastr', [
                'type' => 'info',
                'message' => 'New message from ' . ($event['message']['sender']['full_name'] ?? 'a student')
            ]);
        }
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->loadUnreadCounts();
        $this->calculateTotalUnread();
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
        $this->dispatch('conversationSelected', $conversationId);
    }

    public function showStudentList()
    {
        $this->dispatch('showStudentList');
    }

    public function startChatWithStudent($studentId)
    {
        try {
            $student = User::findOrFail($studentId);
            $conversation = Conversation::createDirectConversation(
                auth()->id(),
                $studentId,
                "Chat with {$student->full_name}"
            );
            
            $this->selectConversation($conversation->id);
            $this->refreshList();
            
            $this->dispatchBrowserEvent('toastr', [
                'type' => 'success',
                'message' => "Chat started with {$student->full_name}"
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('toastr', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
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

        // Apply filters
        if ($this->filter === 'unread') {
            $query->whereHas('messages', function($q) {
                $q->where('sender_id', '!=', auth()->id())
                  ->whereNull('read_at');
            });
        }

        // Search functionality
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
        ]);
    }
}
