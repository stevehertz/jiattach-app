<div>
    {{-- Chat Manager Wrapper - manages communication between ChatList and ChatBox --}}
    <div class="row">
        <!-- Chat List Sidebar -->
        <div class="col-md-4">
            <livewire:chat.chat-list :key="'chat-list'" />
        </div>

        <!-- Chat Box -->
        <div class="col-md-8">
            <livewire:chat.chat-box :key="'chat-box'" />
        </div>
    </div>
</div>