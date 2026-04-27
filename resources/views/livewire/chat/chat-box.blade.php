<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <div class="card direct-chat direct-chat-primary h-100">
        <div class="card-header">
            <h3 class="card-title">
                @if ($conversation)
                    @php
                        $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                    @endphp
                    <div class="d-flex align-items-center">
                        @if ($otherUser)
                            <div class="avatar-initials bg-success img-circle mr-2"
                                style="width: 35px; height: 35px; line-height: 35px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                {{ $otherUser->initials() }}
                            </div>
                            <span>{{ $otherUser->full_name }}</span>
                            <span class="ml-2">
                                <span
                                    class="badge badge-{{ $otherUser->hasRole('admin') || $otherUser->hasRole('super-admin') ? 'danger' : 'success' }}">
                                    {{ $otherUser->hasRole('admin') || $otherUser->hasRole('super-admin') ? 'Admin' : 'Student' }}
                                </span>
                            </span>
                        @endif
                    </div>
                @else
                    Select a Conversation
                @endif
            </h3>
            <div class="card-tools">
                @if ($conversation)
                    <button type="button" class="btn btn-tool" title="Search Messages">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-tool" title="Conversation Details">
                        <i class="fas fa-info-circle"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if (!$conversation)
                <!-- No Conversation Selected -->
                <div class="text-center p-5" style="height: 60vh;">
                    <i class="fas fa-comment-dots fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">Your Messages</h5>
                    <p class="text-muted">Select a conversation from the list to start chatting</p>
                    <p class="text-muted">or</p>
                    {{-- Use $dispatch for Livewire 3 --}}
                    <button wire:click="$dispatch('start-new-chat')" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Start a New Conversation
                    </button>
                </div>
            @else
                <!-- Messages Container -->
                <div class="direct-chat-messages" id="messages-container" style="height: 50vh; overflow-y: auto;">

                    @if ($messages && $messages->count() > 0)
                        @foreach ($messages as $message)
                            @php
                                $isMine = $message->sender_id == auth()->id();
                                $messageColor = $isMine ? 'bg-primary' : 'bg-success';
                            @endphp

                            <div class="direct-chat-msg {{ $isMine ? 'right' : '' }} mb-3">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name {{ $isMine ? 'float-right' : 'float-left' }}">
                                        {{ $message->sender->full_name ?? 'Unknown' }}
                                    </span>
                                    <span class="direct-chat-timestamp {{ $isMine ? 'float-left' : 'float-right' }}">
                                        {{ $message->created_at->format('M d, g:i A') }}
                                    </span>
                                </div>

                                <!-- Avatar -->
                                <div class="direct-chat-img">
                                    <div class="avatar-initials {{ $messageColor }} img-circle"
                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                        {{ $message->sender->initials() ?? '?' }}
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="direct-chat-text {{ $messageColor }}">
                                    @if ($message->type == 'text')
                                        {{ $message->body }}
                                    @elseif($message->type == 'image' && !empty($message->attachments))
                                        <div class="message-image">
                                            @if (isset($message->attachments[0]['path']))
                                                <img src="{{ Storage::url($message->attachments[0]['path']) }}"
                                                    class="img-fluid rounded" style="max-width: 250px;"
                                                    alt="Attachment">
                                            @endif
                                            @if ($message->body)
                                                <p class="mt-2">{{ $message->body }}</p>
                                            @endif
                                        </div>
                                    @elseif(!empty($message->attachments))
                                        <div class="message-file">
                                            <i class="fas fa-paperclip"></i>
                                            @foreach ($message->attachments as $attachment)
                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                                    class="text-white d-block">
                                                    <i class="fas fa-file mr-1"></i>
                                                    {{ $attachment['name'] }}
                                                </a>
                                            @endforeach
                                            @if ($message->body)
                                                <p class="mt-1">{{ $message->body }}</p>
                                            @endif
                                        </div>
                                    @else
                                        {{ $message->body }}
                                    @endif

                                    <!-- Message Status -->
                                    @if ($isMine)
                                        <div class="message-status text-right mt-1">
                                            @if ($message->read_at)
                                                <small class="text-white-50">
                                                    <i class="fas fa-check-double"></i> Read
                                                    {{ $message->read_at->format('g:i A') }}
                                                </small>
                                            @else
                                                <small class="text-white-50">
                                                    <i class="fas fa-check"></i> Sent
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No messages yet. Start the conversation!</p>
                        </div>
                    @endif
                </div>

                <!-- Message Input -->
                <div class="card-footer">
                    <form wire:submit.prevent="sendMessage" class="input-group">
                        <div class="input-group-prepend">
                            <label for="file-upload" class="btn btn-default mb-0" title="Attach File"
                                style="cursor: pointer;">
                                <i class="fas fa-paperclip"></i>
                            </label>
                            <input type="file" wire:model="attachments" class="d-none" multiple id="file-upload">
                        </div>

                        <input type="text" wire:model.defer="messageBody" placeholder="Type your message..."
                            class="form-control" wire:keydown.enter="sendMessage">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-paper-plane"></i>
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Attachment Preview -->
                    @if (isset($attachments) && count($attachments) > 0)
                        <div class="mt-2">
                            <small class="text-muted">Attachments:</small>
                            @foreach ($attachments as $index => $attachment)
                                <span class="badge badge-info mr-1">
                                    {{ $attachment->getClientOriginalName() }}
                                    <button type="button" class="close ml-1"
                                        wire:click="removeAttachment({{ $index }})">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:load', function() {
                // Function to scroll to bottom of messages
                function scrollToBottom() {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 100);
                    }
                }

                // Scroll on load
                scrollToBottom();

                // Listen for message updates
                Livewire.hook('message.processed', (message, component) => {
                    scrollToBottom();
                });

                // Listen for startNewChat event from chat-box
                Livewire.on('startNewChat', () => {
                    // This will be handled by the ChatList component
                });

                // Listen for new messages from Echo
                @if ($conversation)
                    window.Echo.private('conversation.{{ $conversation->id }}')
                        .listen('.message.sent', (e) => {
                            // Refresh the Livewire component
                            @this.call('refreshMessages');

                            // Show browser notification if page not focused
                            if (!document.hasFocus() && e.message.sender_id !== {{ auth()->id() }}) {
                                if (Notification.permission === 'granted') {
                                    new Notification(`New message from ${e.message.sender?.full_name || 'User'}`, {
                                        body: e.message.body?.substring(0, 100) || 'New message received',
                                        icon: '{{ asset('img/logo-icon.png') }}',
                                        badge: '{{ asset('img/logo-icon.png') }}'
                                    });
                                }
                            }
                        })

                        .listen('.message.read', (e) => {
                            // Update read receipts
                            console.log('Messages read by user:', e.userId);
                        });
                @endif
            });

            // Request notification permission on page load
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
        </script>
    @endpush


</div>
