<?php

namespace App\Livewire\Admin\Chat;

use App\Models\Conversation;
use App\Models\User;
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
    public $showStudentList = false;
    public $studentSearch = '';

    protected $chatService;

    protected function getListeners()
    {
        $listeners = [
            'conversationSelected' => 'loadConversation',
            'messageReceived' => 'refreshMessages',
            'showStudentList' => 'toggleStudentList',
        ];

        if ($this->conversationId) {
            $listeners["echo-private:conversation.{$this->conversationId},message.sent"] = 'handleNewMessage';
            $listeners["echo-private:conversation.{$this->conversationId},message.read"] = 'handleMessageRead';
        }

        return $listeners;
    }

    protected $rules = [
        'messageBody' => 'nullable|string|max:5000',
        'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xlsx,xls'
    ];

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

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
                $this->dispatch('refreshChatList');
            }
    }

    public function toggleStudentList()
    {
        $this->showStudentList = !$this->showStudentList;
        $this->studentSearch = '';
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

            $this->loadConversation($conversation->id);

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

    public function handleNewMessage($event)
    {
            if (isset($event['message']) && $event['message']['conversation_id'] == $this->conversationId) {
                $this->refreshMessages();
                $this->chatService->markAsRead($this->conversation, auth()->user());
                $this->dispatch('refreshChatList');
            }
    }

    public function handleMessageRead($event)
    {
        $this->dispatchBrowserEvent('message-read', [
            'user_id' => $event['userId'],
            'read_at' => $event['lastReadAt']
        ]);
    }

    public function refreshMessages()
    {
        $this->resetPage();
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageBody)) && empty($this->attachments)) {
            return;
        }

        if (!$this->conversation) {
            $this->dispatchBrowserEvent('toastr', [
                'type' => 'error',
                'message' => 'No conversation selected'
            ]);
            return;
        }

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

            $this->dispatch('messageSent');
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('toastr', [
                'type' => 'error',
                'message' => 'Failed to send message: ' . $e->getMessage()
            ]);
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
