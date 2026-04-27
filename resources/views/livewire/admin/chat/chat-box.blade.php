<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="card direct-chat direct-chat-primary h-100">
        <!-- Header -->
        <div class="card-header bg-white">
            @if (!$conversation && !$showStudentList)
                <h3 class="card-title">
                    <i class="fas fa-comment-dots mr-2 text-primary"></i>Select a conversation
                </h3>
            @elseif($showStudentList)
                <h3 class="card-title">
                    <button wire:click="toggleStudentList" class="btn btn-default btn-sm mr-2">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    New Conversation
                </h3>
            @else
                <h3 class="card-title">
                    <div class="d-flex align-items-center">
                        @php
                            $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                            $isOnline =
                                $otherUser &&
                                $otherUser->last_login_at &&
                                $otherUser->last_login_at->diffInMinutes(now()) < 5;
                        @endphp

                        @if ($otherUser)
                            <div class="avatar-initials bg-success img-circle mr-2"
                                style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold;">
                                {{ $otherUser->initials() }}
                            </div>
                            <div>
                                <strong>{{ $otherUser->full_name }}</strong>
                                <br>
                                <small class="text-muted">
                                    @if ($otherUser->hasRole('student'))
                                        <i class="fas fa-user-graduate mr-1"></i>
                                        {{ $otherUser->studentProfile->institution_name ?? 'Student' }}
                                    @elseif($otherUser->hasRole('admin') || $otherUser->hasRole('super-admin'))
                                        <i class="fas fa-user-shield mr-1"></i>Admin
                                    @else
                                        {{ $otherUser->getRoleNames()->first() }}
                                    @endif

                                    @if ($isOnline)
                                        <span class="badge badge-success ml-1">Online</span>
                                    @else
                                        <span class="text-muted ml-1">
                                            <small>
                                                Last seen:
                                                {{ $otherUser->last_login_at ? $otherUser->last_login_at->diffForHumans() : 'Never' }}
                                            </small>
                                        </span>
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                </h3>
            @endif

            <div class="card-tools">
                @if ($conversation && !$showStudentList)
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool" title="Search messages">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-tool" title="View student profile">
                            <i class="fas fa-user"></i>
                        </button>
                        <button type="button" class="btn btn-tool" title="More options">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-0">
            @if ($showStudentList)
                <!-- Student Selection View -->
                <div style="height: 70vh; display: flex; flex-direction: column;">
                    <div class="p-3 border-bottom">
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model.debounce.300ms="studentSearch" class="form-control"
                                placeholder="Search students by name or email...">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow-1" style="overflow-y: auto;">
                        @forelse($students as $student)
                            <div class="p-3 border-bottom hover-bg-light" style="cursor: pointer;"
                                wire:click="startChatWithStudent({{ $student->id }})">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials bg-info img-circle mr-3"
                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold;">
                                        {{ $student->initials() }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $student->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $student->email }}
                                            @if ($student->studentProfile)
                                                • {{ $student->studentProfile->institution_name }}
                                            @endif
                                        </small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5">
                                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No students found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @elseif(!$conversation)
                <!-- No Conversation Selected -->
                <div class="text-center p-5"
                    style="height: 70vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">Manage Student Communications</h5>
                    <p class="text-muted">Select a conversation from the list or start a new one</p>
                    <button wire:click="toggleStudentList" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Start New Conversation
                    </button>
                </div>
            @else
                <!-- Chat Messages View -->
                <div style="height: 70vh; display: flex; flex-direction: column;">
                    <!-- Messages Container -->
                    <div class="direct-chat-messages flex-grow-1 p-3" id="admin-messages-container"
                        style="overflow-y: auto; background-color: #f4f6f9;">

                        @if ($messages && $messages->count() > 0)
                            @foreach ($messages->reverse() as $message)
                                @php
                                    $isMine = $message->sender_id == auth()->id();
                                    $messageColor = $isMine ? 'bg-primary' : 'bg-white';
                                    $textColor = $isMine ? 'text-white' : 'text-dark';
                                @endphp

                                <div class="direct-chat-msg {{ $isMine ? 'right' : '' }} mb-3">
                                    <!-- Message Header -->
                                    <div class="direct-chat-infos clearfix mb-1">
                                        <span class="direct-chat-name {{ $isMine ? 'float-right' : 'float-left' }}">
                                            <small
                                                class="font-weight-bold">{{ $message->sender->full_name ?? 'Unknown' }}</small>
                                        </span>
                                        <span
                                            class="direct-chat-timestamp {{ $isMine ? 'float-left' : 'float-right' }}">
                                            <small
                                                class="text-muted">{{ $message->created_at->format('M d, g:i A') }}</small>
                                        </span>
                                    </div>

                                    <!-- Avatar -->
                                    <div class="direct-chat-img">
                                        <div class="avatar-initials {{ $isMine ? 'bg-primary' : 'bg-secondary' }} img-circle"
                                            style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                            {{ $message->sender->initials() ?? '?' }}
                                        </div>
                                    </div>

                                    <!-- Message Content -->
                                    <div class="direct-chat-text {{ $messageColor }} {{ $textColor }}"
                                        style="max-width: 70%; word-wrap: break-word;">

                                        @if ($message->type == 'text')
                                            <p class="mb-0">{{ $message->body }}</p>
                                        @elseif($message->type == 'image' && !empty($message->attachments))
                                            <div class="message-image mb-2">
                                                @if (isset($message->attachments[0]['path']))
                                                    <img src="{{ Storage::url($message->attachments[0]['path']) }}"
                                                        class="img-fluid rounded"
                                                        style="max-width: 250px; cursor: pointer;" alt="Attachment"
                                                        onclick="window.open(this.src)">
                                                @endif
                                            </div>
                                            @if ($message->body)
                                                <p class="mb-0">{{ $message->body }}</p>
                                            @endif
                                        @elseif(!empty($message->attachments))
                                            <div class="message-file mb-2">
                                                @foreach ($message->attachments as $attachment)
                                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                                        class="d-block mb-1 {{ $isMine ? 'text-white' : 'text-primary' }}">
                                                        <i class="fas fa-file mr-1"></i>
                                                        {{ $attachment['name'] }}
                                                        <small class="text-muted ml-1">
                                                            ({{ number_format($attachment['size'] / 1024, 1) }} KB)
                                                        </small>
                                                    </a>
                                                @endforeach
                                            </div>
                                            @if ($message->body)
                                                <p class="mb-0">{{ $message->body }}</p>
                                            @endif
                                        @else
                                            <p class="mb-0">{{ $message->body }}</p>
                                        @endif

                                        <!-- Message Status (for admin's own messages) -->
                                        @if ($isMine)
                                            <div class="text-right mt-1">
                                                @if ($message->read_at)
                                                    <small
                                                        class="{{ $textColor === 'text-white' ? 'text-white-50' : 'text-muted' }}">
                                                        <i class="fas fa-check-double"></i> Read
                                                        {{ $message->read_at->format('g:i A') }}
                                                    </small>
                                                @elseif($message->delivered_at)
                                                    <small
                                                        class="{{ $textColor === 'text-white' ? 'text-white-50' : 'text-muted' }}">
                                                        <i class="fas fa-check"></i> Delivered
                                                    </small>
                                                @else
                                                    <small
                                                        class="{{ $textColor === 'text-white' ? 'text-white-50' : 'text-muted' }}">
                                                        <i class="fas fa-clock"></i> Sending...
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
                    <div class="p-3 border-top bg-white">
                        <form wire:submit.prevent="sendMessage">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label for="admin-file-upload" class="btn btn-outline-secondary mb-0"
                                        title="Attach files" style="cursor: pointer;">
                                        <i class="fas fa-paperclip"></i>
                                    </label>
                                    <input type="file" wire:model="attachments" class="d-none" multiple
                                        id="admin-file-upload" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                </div>

                                <textarea wire:model.defer="messageBody" placeholder="Type your message..." class="form-control" rows="1"
                                    style="resize: none; min-height: 38px; max-height: 120px;" wire:keydown.enter.prevent="sendMessage"
                                    oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';">
                            </textarea>

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
                            </div>
                        </form>

                        <!-- Attachment Preview -->
                        @if (isset($attachments) && count($attachments) > 0)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-paperclip mr-1"></i>Attachments:
                                </small>
                                @foreach ($attachments as $index => $attachment)
                                    <span class="badge badge-info mr-1 mb-1">
                                        <i class="fas fa-file mr-1"></i>
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
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:load', function() {
                function scrollToBottom() {
                    const container = document.getElementById('admin-messages-container');
                    if (container) {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 100);
                    }
                }

                scrollToBottom();

                Livewire.hook('message.processed', (message, component) => {
                    if (component.id.includes('chat-box')) {
                        scrollToBottom();
                    }
                });

                @if ($conversation && !$showStudentList)
                    window.Echo.private('conversation.{{ $conversation->id }}')
                        .listen('.message.sent', (e) => {
                            @this.call('refreshMessages');

                            if (!document.hasFocus() && e.message.sender_id !== {{ auth()->id() }}) {
                                if (Notification.permission === 'granted') {
                                    new Notification(`New message from ${e.message.sender?.full_name || 'User'}`, {
                                        body: e.message.body?.substring(0, 100) || 'New message received',
                                        icon: '{{ asset('img/logo-icon.png') }}',
                                        badge: '{{ asset('img/logo-icon.png') }}',
                                        tag: 'chat-message'
                                    });
                                }
                            }
                        });
                @endif
            });

            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
        </script>
    @endpush
</div>
