<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="card direct-chat direct-chat-primary h-100">
        <div class="card-header">
            <h3 class="card-title">Messages</h3>
            <div class="card-tools">
                <button wire:click="startNewChat" class="btn btn-tool">
                    <i class="fas fa-plus"></i> New Chat
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Search -->
            <div class="p-2 border-bottom">
                <input type="text" wire:model="search" class="form-control form-control-sm"
                    placeholder="Search conversations...">
            </div>

            <!-- Conversation List -->
            <div class="conversations-list" style="height: 60vh; overflow-y: auto;">
                @forelse($conversations as $conversation)
                    @php
                        $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                        $lastMessage = $conversation->lastMessage;
                        $unread = $unreadCounts[$conversation->id] ?? 0;
                    @endphp

                    <div class="conversation-item p-3 border-bottom cursor-pointer {{ $activeConversationId == $conversation->id ? 'bg-light' : '' }}"
                        wire:click="selectConversation({{ $conversation->id }})" style="cursor: pointer;">
                        <div class="d-flex align-items-center">
                            <!-- User Avatar -->
                            <div class="flex-shrink-0">
                                @if ($otherUser)
                                    <div class="avatar-initials bg-primary img-circle"
                                        style="width: 45px; height: 45px; line-height: 45px; text-align: center; color: white; font-weight: bold;">
                                        {{ $otherUser->initials() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Conversation Info -->
                            <div class="flex-grow-1 ml-3">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark">
                                        {{ $otherUser ? $otherUser->full_name : 'Unknown User' }}
                                    </strong>
                                    @if ($lastMessage)
                                        <small class="text-muted">{{ $lastMessage->time_ago }}</small>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted mb-0 text-truncate" style="max-width: 200px;">
                                        @if ($lastMessage)
                                            @if ($lastMessage->sender_id == auth()->id())
                                                <span class="text-muted">You: </span>
                                            @endif
                                            {{ Str::limit($lastMessage->body, 50) }}
                                        @else
                                            <em>No messages yet</em>
                                        @endif
                                    </p>
                                    @if ($unread > 0)
                                        <span class="badge badge-danger">{{ $unread }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No conversations yet</p>
                        <button wire:click="startNewChat" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Start a Conversation
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .conversation-item:hover {
                background-color: #f8f9fa;
            }

            .conversation-item.active {
                background-color: #e7f1ff;
            }

            .conversations-list::-webkit-scrollbar {
                width: 6px;
            }

            .conversations-list::-webkit-scrollbar-thumb {
                background-color: #c1c1c1;
                border-radius: 3px;
            }
        </style>
    @endpush

</div>
