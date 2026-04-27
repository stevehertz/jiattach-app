<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ChatManager extends Component
{
    public $activeConversationId = null;

    protected $listeners = [
        'conversationSelected' => 'handleConversationSelected',
        'startNewChat' => 'handleStartNewChat',
    ];

    public function handleConversationSelected($conversationId)
    {
        $this->activeConversationId = $conversationId;
        // Dispatch to ChatBox
        $this->dispatch('conversationSelected', conversationId: $conversationId);
    }

    public function handleStartNewChat()
    {
        // Dispatch to ChatList to start new chat
        $this->dispatch('startNewChat');
    }

    public function render()
    {
        return view('livewire.chat.chat-manager');
    }
}