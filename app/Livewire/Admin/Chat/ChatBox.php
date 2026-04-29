<?php

namespace App\Livewire\Admin\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ChatBox extends Component
{
    use WithPagination, WithFileUploads;

    public $conversationId;
    public $messageBody = '';
    public $attachments = [];
    public $conversation;
    public $isLoading = true;
    public $showStudentList = false;
    public $studentSearch = '';

    protected $chatService;

    protected $rules = [
        'messageBody' => 'nullable|string|max:5000',
        'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xlsx,xls'
    ];

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    #[On('conversationSelected')]
    public function loadConversation($conversationId)
    {
        $this->conversationId = $conversationId;
        $this->conversation = Conversation::with(['participants.user', 'lastMessage'])
            ->find($conversationId);
        $this->isLoading = false;
        $this->showStudentList = false;
        $this->resetPage();

        if ($this->conversation) {
            $this->chatService->markAsRead($this->conversation, auth()->user());
            $this->dispatch('refreshChatList')->to('admin.chat.chat-list');
        }
    }

    #[On('messageReceived')]
    public function refreshMessages()
    {
        $this->resetPage();
    }

    #[On('showStudentList')]
    public function toggleStudentList()
    {
        $this->showStudentList = !$this->showStudentList;
        $this->studentSearch = '';
    }

    public function startChatWithStudent($studentId)
    {
        try {
            $student = User::findOrFail($studentId);
            
            // Check if conversation already exists
            $existingConversation = Conversation::where('type', 'direct')
                ->whereHas('participants', function ($q) use ($studentId) {
                    $q->where('user_id', $studentId);
                })
                ->whereHas('participants', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->first();

            if ($existingConversation) {
                $this->loadConversation($existingConversation->id);
                $this->dispatch('conversationSelected', conversationId: $existingConversation->id)
                    ->to('admin.chat.chat-list');
                $this->showStudentList = false;
                
                $this->dispatch('toast', 
                    type: 'info',
                    message: "Conversation with {$student->full_name} already exists"
                );
                return;
            }

            // Create new conversation
            $conversation = Conversation::create([
                'title' => "Chat with {$student->full_name}",
                'type' => 'direct',
                'created_by' => auth()->id(),
                'status' => 'active'
            ]);

            $conversation->participants()->createMany([
                ['user_id' => auth()->id(), 'role' => 'member'],
                ['user_id' => $studentId, 'role' => 'member']
            ]);

            $this->loadConversation($conversation->id);
            $this->dispatch('conversationSelected', conversationId: $conversation->id)
                ->to('admin.chat.chat-list');
            $this->showStudentList = false;

            $this->dispatch('toast', 
                type: 'success',
                message: "Chat started with {$student->full_name}"
            );
        } catch (\Exception $e) {
            $this->dispatch('toast', 
                type: 'error',
                message: 'Error: ' . $e->getMessage()
            );
        }
    }

    // Remove the echo listeners - handle real-time updates differently in Livewire 3
    public function handleNewMessage($event)
    {
        if (isset($event['message']) && $event['message']['conversation_id'] == $this->conversationId) {
            $this->refreshMessages();
            $this->chatService->markAsRead($this->conversation, auth()->user());
            $this->dispatch('refreshChatList')->to('admin.chat.chat-list');
        }
    }

    public function handleMessageRead($event)
    {
        $this->dispatch('message-read', [
            'user_id' => $event['userId'],
            'read_at' => $event['lastReadAt']
        ]);
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageBody)) && empty($this->attachments)) {
            return;
        }

        if (!$this->conversation) {
            $this->dispatch('toast', 
                type: 'error',
                message: 'No conversation selected'
            );
            return;
        }

        $this->validate();

        try {
            $message = $this->chatService->sendMessage(
                $this->conversation,
                auth()->user(),
                $this->messageBody ?? '',
                'text',
                $this->processAttachments()
            );

            $this->messageBody = '';
            $this->attachments = [];
            $this->resetPage();

            $this->dispatch('messageSent')->to('admin.chat.chat-list');
            $this->dispatch('newMessageReceived', [
                'conversation_id' => $this->conversation->id,
                'message' => $message->toArray()
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', 
                type: 'error',
                message: 'Failed to send message: ' . $e->getMessage()
            );
        }
    }

    public function removeAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    protected function processAttachments()
    {
        $uploadedFiles = [];

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('chat-attachments', 'public');
                $uploadedFiles[] = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $path,
                    'type' => $attachment->getMimeType(),
                    'size' => $attachment->getSize()
                ];
            }
        }

        return $uploadedFiles;
    }

    public function getStudentsProperty()
    {
        if (!$this->showStudentList) {
            return collect();
        }

        return User::role('student')
            ->where('is_active', true)
            ->where(function ($query) {
                if ($this->studentSearch) {
                    $query->where('first_name', 'like', "%{$this->studentSearch}%")
                        ->orWhere('last_name', 'like', "%{$this->studentSearch}%")
                        ->orWhere('email', 'like', "%{$this->studentSearch}%");
                }
            })
            ->orderBy('first_name')
            ->limit(50)
            ->get();
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
        return view('livewire.admin.chat.chat-box', [
            'messages' => $this->messages,
            'students' => $this->students,
        ]);
    }
}