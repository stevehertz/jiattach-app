<div>
    {{-- Success is as dangerous as failure. --}}
    <div class="card direct-chat direct-chat-primary h-100">
        <div class="card-header bg-primary">
            <h3 class="card-title text-white">
                <i class="fas fa-comments mr-2"></i>Conversations
                @if ($totalUnreadCount > 0)
                    <span class="badge badge-danger ml-2">{{ $totalUnreadCount }}</span>
                @endif
            </h3>
            <div class="card-tools">
                <button wire:click="showStudentList" class="btn btn-tool text-white" title="New Chat">
                    <i class="fas fa-plus"></i> New Chat
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Search & Filters -->
            <div class="p-3 border-bottom">
                <div class="input-group input-group-sm">
                    <input type="text" wire:model.debounce.300ms="search" class="form-control"
                        placeholder="Search students or conversations...">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="btn-group btn-group-sm mt-2 w-100" role="group">
                    <button wire:click="setFilter('all')"
                        class="btn btn-outline-secondary {{ $filter == 'all' ? 'active' : '' }}">
                        All
                    </button>
                    <button wire:click="setFilter('unread')"
                        class="btn btn-outline-warning {{ $filter == 'unread' ? 'active' : '' }}">
                        <i class="fas fa-circle text-warning mr-1" style="font-size: 8px;"></i>
                        Unread
                    </button>
                </div>
            </div>

            <!-- Conversation List -->
            <div class="conversations-list" style="height: 60vh; overflow-y: auto;">
                @forelse($conversations as $conversation)
                    @php
                        $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                        $lastMessage = $conversation->lastMessage;
                        $unread = $unreadCounts[$conversation->id] ?? 0;
                        $isOnline =
                            $otherUser &&
                            $otherUser->last_login_at &&
                            $otherUser->last_login_at->diffInMinutes(now()) < 5;
                    @endphp

                    <div class="conversation-item p-3 border-bottom {{ $activeConversationId == $conversation->id ? 'active-conversation' : '' }}"
                        wire:click="selectConversation({{ $conversation->id }})" style="cursor: pointer;">
                        <div class="d-flex align-items-center">
                            <!-- User Avatar with Online Status -->
                            <div class="flex-shrink-0 position-relative">
                                @if ($otherUser)
                                    @php
                                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                        $colorIndex = crc32($otherUser->email ?? 'default') % count($colors);
                                        $avatarColor = $colors[$colorIndex];
                                    @endphp
                                    <div class="avatar-initials bg-{{ $avatarColor }} img-circle"
                                        style="width: 45px; height: 45px; line-height: 45px; text-align: center; color: white; font-weight: bold;">
                                        {{ $otherUser->initials() }}
                                    </div>
                                    @if ($isOnline)
                                        <span class="position-absolute"
                                            style="bottom: 0; right: 0; width: 12px; height: 12px; background-color: #28a745; border: 2px solid white; border-radius: 50%;">
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <!-- Conversation Info -->
                            <div class="flex-grow-1 ml-3 min-width-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="text-truncate">
                                        <strong class="text-dark">
                                            {{ $otherUser ? $otherUser->full_name : 'Unknown User' }}
                                        </strong>
                                        @if ($otherUser)
                                            <br>
                                            <small class="text-muted">
                                                @if ($otherUser->hasRole('student'))
                                                    <i class="fas fa-user-graduate mr-1"></i>
                                                    {{ $otherUser->studentProfile->institution_name ?? 'Student' }}
                                                @elseif($otherUser->hasRole('admin') || $otherUser->hasRole('super-admin'))
                                                    <i class="fas fa-user-shield mr-1"></i>Admin
                                                @elseif($otherUser->hasRole('employer'))
                                                    <i class="fas fa-building mr-1"></i>Employer
                                                @elseif($otherUser->hasRole('mentor'))
                                                    <i class="fas fa-chalkboard-teacher mr-1"></i>Mentor
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                    <div class="text-right ml-2">
                                        @if ($lastMessage)
                                            <small class="text-muted d-block">
                                                {{ $lastMessage->created_at->format('M d') }}
                                            </small>
                                            <small class="text-muted d-block">
                                                {{ $lastMessage->created_at->format('g:i A') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Last Message Preview -->
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <p class="text-muted mb-0 text-truncate" style="max-width: 200px;">
                                        @if ($lastMessage)
                                            @if ($lastMessage->sender_id == auth()->id())
                                                <span class="text-muted">You: </span>
                                            @endif
                                            {{ Str::limit($lastMessage->body, 50) }}
                                        @else
                                            <em class="text-muted">No messages yet</em>
                                        @endif
                                    </p>

                                    <!-- Unread Badge & Status -->
                                    <div class="d-flex align-items-center ml-2">
                                        @if ($unread > 0)
                                            <span class="badge badge-danger badge-pill">{{ $unread }}</span>
                                        @endif
                                        @if ($lastMessage && $lastMessage->sender_id == auth()->id())
                                            <small class="ml-1">
                                                @if ($lastMessage->read_at)
                                                    <i class="fas fa-check-double text-primary" title="Read"></i>
                                                @else
                                                    <i class="fas fa-check text-muted" title="Sent"></i>
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No conversations found</h6>
                        @if ($search || $filter !== 'all')
                            <p class="text-muted small">Try adjusting your search or filters</p>
                        @else
                            <p class="text-muted small">Students will appear here when they start a chat</p>
                            <button wire:click="showStudentList" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Start New Chat
                            </button>
                        @endif
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

            .active-conversation {
                background-color: #e3f2fd !important;
                border-left: 3px solid #007bff;
            }

            .conversations-list::-webkit-scrollbar {
                width: 6px;
            }

            .conversations-list::-webkit-scrollbar-thumb {
                background-color: #c1c1c1;
                border-radius: 3px;
            }

            .conversations-list::-webkit-scrollbar-thumb:hover {
                background-color: #a8a8a8;
            }

            .badge-pill {
                padding-right: 0.6em;
                padding-left: 0.6em;
            }

            /* Online status pulse animation */
            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
                }
            }

            .position-absolute[style*="background-color: #28a745"] {
                animation: pulse 2s infinite;
            }
        </style>
    @endpush
</div>
