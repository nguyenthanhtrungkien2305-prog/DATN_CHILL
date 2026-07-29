@extends('admin.layouts.app')

@section('title', 'Hỗ trợ Trực Tuyến - Chill Chill Admin')
<<<<<<< HEAD
@section('page_title', 'Hỗ trợ khách hàng trực tuyến')

@section('main_class', 'overflow-hidden')
@section('content_class', 'flex-1 flex overflow-hidden')

@push('styles')
    <style> 
        .active-session { background-color: rgba(232, 99, 74, 0.08); border-left: 4px solid #e8634a; }
    </style>
@endpush

@section('content')
{{-- Cột Trái: Danh sách phiên chat active --}}
<div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full shrink-0">
    <div class="p-4 border-b border-gray-100 shrink-0">
        <input type="text" id="session-search" oninput="renderSessionSidebar()" placeholder="Tìm khách hàng..." class="w-full px-4 py-2 bg-gray-50 border border-transparent rounded-xl text-sm focus:outline-none focus:border-[#e8634a] focus:bg-white transition-all text-espresso placeholder-gray-400">
    </div>
    {{-- Container danh sách phiên --}}
    <div id="sessions-list" class="flex-1 overflow-y-auto divide-y divide-gray-50">
        {{-- Load sessions ở đây --}}
        <div class="p-8 text-center text-gray-400 italic text-sm">Đang tải cuộc trò chuyện...</div>
    </div>
</div>

{{-- Cột Phải: Khung chat chi tiết --}}
<div class="flex-1 flex flex-col bg-gray-50 h-full relative" id="chat-conversation-pane">
    {{-- Trạng thái trống khi chưa chọn session --}}
    <div id="chat-empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 p-8">
        <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center shadow-sm text-4xl mb-4">
            💬
        </div>
        <h3 class="font-bold text-gray-700 text-lg mb-1">Bắt đầu tư vấn</h3>
        <p class="text-sm text-center max-w-xs">Chọn một khách hàng từ danh sách bên trái để xem nội dung chat và hỗ trợ trực tuyến.</p>
    </div>

    {{-- Khung trò chuyện khi đã chọn session (Ẩn mặc định) --}}
    <div id="chat-active-state" class="flex flex-col h-full w-full hidden">
        {{-- Header phòng chat --}}
        <div class="bg-white px-6 py-3 shadow-sm border-b flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#FAF7F2] border border-[#e8634a]/10 flex items-center justify-center text-lg">
                    👤
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm" id="active-session-name">Khách hàng</h4>
                    <span class="flex items-center gap-1.5 text-[10px] text-green-500 font-medium mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Đang trực tuyến
                    </span>
=======

@section('content')
    <style> 
        .active-session { background-color: rgba(232, 99, 74, 0.08); border-left: 4px solid #e8634a; }
    </style>

    {{-- Header của trang Chat --}}
    <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <span>💬</span> Hỗ trợ khách hàng trực tuyến
        </h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
            <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Đăng xuất</a>
        </div>
    </header>

    {{-- Khung chia đôi chat room --}}
    <div class="flex-1 flex overflow-hidden">
        
        {{-- Cột Trái: Danh sách phiên chat active --}}
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full shrink-0">
            <div class="p-4 border-b border-gray-100 shrink-0">
                <input type="text" id="session-search" oninput="renderSessionSidebar()" placeholder="Tìm khách hàng..." class="w-full px-4 py-2 bg-gray-50 border border-transparent rounded-xl text-sm focus:outline-none focus:border-[#e8634a] focus:bg-white transition-all text-[#3e2723] placeholder-gray-400">
            </div>
            {{-- Container danh sách phiên --}}
            <div id="sessions-list" class="flex-1 overflow-y-auto divide-y divide-gray-50 custom-scrollbar">
                {{-- Load sessions ở đây --}}
                <div class="p-8 text-center text-gray-400 italic text-sm">Đang tải cuộc trò chuyện...</div>
            </div>
        </div>

        {{-- Cột Phải: Khung chat chi tiết --}}
        <div class="flex-1 flex flex-col bg-gray-50 h-full relative" id="chat-conversation-pane">
            
            {{-- Trạng thái trống khi chưa chọn session --}}
            <div id="chat-empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 p-8">
                <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center shadow-sm text-4xl mb-4">
                    💬
>>>>>>> main
                </div>
                <h3 class="font-bold text-gray-700 text-lg mb-1">Bắt đầu tư vấn</h3>
                <p class="text-sm text-center max-w-xs">Chọn một khách hàng từ danh sách bên trái để xem nội dung chat và hỗ trợ trực tuyến.</p>
            </div>
<<<<<<< HEAD
            {{-- Bot AI Toggle --}}
            <div class="flex items-center gap-2 bg-gray-50 border px-3 py-1.5 rounded-full shadow-sm">
                <span class="text-xs font-semibold text-gray-600 flex items-center gap-1">
                    🤖 Trợ lý AI:
                </span>
                <button id="btn-toggle-bot" onclick="toggleBotStatus()" class="px-3 py-1 text-[11px] font-bold rounded-full transition-all duration-200 shadow-sm bg-green-100 text-green-700 hover:bg-green-200">
                    Đang Bật
                </button>
            </div>
        </div>
=======

            {{-- Khung trò chuyện khi đã chọn session (Ẩn mặc định) --}}
            <div id="chat-active-state" class="flex flex-col h-full w-full hidden">
                {{-- Header phòng chat --}}
                <div class="bg-white px-6 py-3 shadow-sm border-b flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#FAF7F2] border border-[#e8634a]/10 flex items-center justify-center text-lg">
                            👤
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm" id="active-session-name">Khách hàng</h4>
                            <span class="flex items-center gap-1.5 text-[10px] text-green-500 font-medium mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Đang trực tuyến
                            </span>
                        </div>
                    </div>
                    {{-- Bot AI Toggle --}}
                    <div class="flex items-center gap-2 bg-gray-50 border px-3 py-1.5 rounded-full shadow-sm">
                        <span class="text-xs font-semibold text-gray-600 flex items-center gap-1">
                            🤖 Trợ lý AI:
                        </span>
                        <button id="btn-toggle-bot" onclick="toggleBotStatus()" class="px-3 py-1 text-[11px] font-bold rounded-full transition-all duration-200 shadow-sm bg-green-100 text-green-700 hover:bg-green-200">
                            Đang Bật
                        </button>
                    </div>
                </div>

                {{-- Khung hiển thị các tin nhắn --}}
                <div id="admin-chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4 flex flex-col bg-[#FAF7F2] custom-scrollbar">
                    {{-- Tin nhắn load ở đây --}}
                </div>

                {{-- Form nhập tin nhắn gửi đi --}}
                <form id="admin-chat-form" onsubmit="handleAdminSend(event)" class="p-4 bg-white border-t flex items-center gap-3 shrink-0">
                    <input type="text" id="admin-chat-input" placeholder="Nhập câu trả lời của bạn..." autocomplete="off" class="flex-1 px-4 py-3 bg-gray-50 border border-transparent rounded-xl text-sm focus:outline-none focus:border-[#e8634a] focus:bg-white transition-all text-[#3e2723] placeholder-gray-400">
                    <button type="submit" class="bg-[#e8634a] hover:bg-[#d5523b] text-white px-6 py-3 rounded-xl font-bold shadow-md shadow-[#e8634a]/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 text-sm shrink-0">
                        🚀 Gửi đi
                    </button>
                </form>
            </div>
        </div>
    </div>
>>>>>>> main

        {{-- Khung hiển thị các tin nhắn --}}
        <div id="admin-chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4 flex flex-col bg-[#FAF7F2]">
            {{-- Tin nhắn load ở đây --}}
        </div>

        {{-- Form nhập tin nhắn gửi đi --}}
        <form id="admin-chat-form" onsubmit="handleAdminSend(event)" class="p-4 bg-white border-t flex items-center gap-3 shrink-0">
            <input type="text" id="admin-chat-input" placeholder="Nhập câu trả lời của bạn..." autocomplete="off" class="flex-1 px-4 py-3 bg-gray-50 border border-transparent rounded-xl text-sm focus:outline-none focus:border-[#e8634a] focus:bg-white transition-all text-espresso placeholder-gray-400">
            <button type="submit" class="bg-[#e8634a] hover:bg-[#d5523b] text-white px-6 py-3 rounded-xl font-bold shadow-md shadow-[#e8634a]/20 hover:scale-102 active:scale-98 transition-all flex items-center gap-2 text-sm shrink-0">
                🚀 Gửi đi
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let allSessions = [];
    let activeSessionId = null;
    let activeSessionToken = null;
    let sessionPoll = null;
    let messagePoll = null;

    // Tải danh sách phòng chat từ server
    async function fetchSessions() {
        try {
            const response = await fetch('{{ route('admin.chats.sessions') }}');
            const data = await response.json();
            if (data.success) {
                allSessions = data.sessions;
                renderSessionSidebar();
            }
        } catch (error) {
            console.error('Lỗi tải danh sách session:', error);
        }
    }

    // Render danh sách session ở sidebar
    function renderSessionSidebar() {
        const container = document.getElementById('sessions-list');
        const searchVal = document.getElementById('session-search').value.toLowerCase();
        
        const filtered = allSessions.filter(s => s.name.toLowerCase().includes(searchVal) || s.last_message.toLowerCase().includes(searchVal));

        if (filtered.length === 0) {
            container.innerHTML = `<div class="p-8 text-center text-gray-400 italic text-xs">Không tìm thấy cuộc trò chuyện nào.</div>`;
            return;
        }

        container.innerHTML = '';
        filtered.forEach(session => {
            const isSelected = activeSessionId === session.id;
            const unreadBadge = session.unread_count > 0 
                ? `<span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 animate-bounce">${session.unread_count}</span>` 
                : '';

            const item = document.createElement('div');
            item.className = `p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between gap-3 ${isSelected ? 'active-session' : ''}`;
            item.onclick = () => selectSession(session.id, session.session_token);

            item.innerHTML = `
                <div class="flex items-center gap-3 overflow-hidden flex-1">
                    <div class="w-10 h-10 rounded-full bg-[#FFF0D4] flex items-center justify-center text-sm shrink-0">
                        ☕
                    </div>
                    <div class="overflow-hidden flex-1">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <h4 class="font-bold text-gray-800 text-xs truncate">${session.name}</h4>
                            <span class="text-[9px] text-gray-400 shrink-0">${session.last_message_time}</span>
                        </div>
                        <p class="text-xs text-gray-500 truncate">${session.last_message}</p>
                    </div>
                </div>
                ${unreadBadge}
            `;

            container.appendChild(item);
        });
    }

    // Chọn một phòng chat để trò chuyện
    async function selectSession(sessionId, sessionToken) {
        activeSessionId = sessionId;
        activeSessionToken = sessionToken;

        // Xóa empty state, hiện active state
        document.getElementById('chat-empty-state').classList.add('hidden');
        document.getElementById('chat-active-state').classList.remove('hidden');

        // Render active class ở sidebar
        renderSessionSidebar();

        // Load tin nhắn đầu tiên
        await loadMessages();

        // Thiết lập polling cho tin nhắn phòng chat này mỗi 2.5 giây
        if (messagePoll) clearInterval(messagePoll);
        messagePoll = setInterval(loadMessages, 2500);

        document.getElementById('admin-chat-input').focus();
    }

    // Tải các tin nhắn của session đang chọn
    async function loadMessages() {
        if (!activeSessionId) return;
        try {
            const response = await fetch(`/admin/chats/sessions/${activeSessionId}/messages`);
            const data = await response.json();
            if (data.success) {
                document.getElementById('active-session-name').innerText = data.session_name;
                
                const container = document.getElementById('admin-chat-messages');
                const currentMsgCount = container.querySelectorAll('.admin-msg-item').length;

                // Cập nhật trạng thái nút Bot AI
                updateBotToggleButton(data.is_bot_enabled);

                // Nếu có tin nhắn mới, render lại danh sách tin nhắn
                if (data.messages.length !== currentMsgCount) {
                    container.innerHTML = '';
                    data.messages.forEach(msg => {
                        appendAdminMessageToDOM(msg.sender_type, msg.message, msg.created_at);
                    });
                    scrollAdminChatToBottom();
                    
                    // Cập nhật lại sidebar nhanh chóng
                    fetchSessions();
                }
            }
        } catch (error) {
            console.error('Lỗi tải tin nhắn:', error);
        }
    }

    // Gửi tin nhắn từ Admin
    async function handleAdminSend(e) {
        e.preventDefault();
        const input = document.getElementById('admin-chat-input');
        const text = input.value.trim();
        if (!text || !activeSessionId) return;

<<<<<<< HEAD
        input.value = '';
=======
            if (sender === 'admin') {
                wrapper.innerHTML = `
                    <div class="flex flex-col items-end max-w-[75%]">
                        <div class="bg-gray-800 text-white p-3.5 rounded-[18px] rounded-tr-none shadow-sm text-sm leading-relaxed">
                            ${formatMessageText(message)}
                        </div>
                        <span class="text-[9px] text-gray-400 mt-1">${time}</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-xs shrink-0 font-bold">
                        AD
                    </div>
                `;
            } else {
                wrapper.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-[#e8634a] text-white flex items-center justify-center text-xs shrink-0 font-bold">
                        KH
                    </div>
                    <div class="flex flex-col items-start max-w-[70%]">
                        <div class="bg-white text-[#3e2723] p-3.5 rounded-[18px] rounded-tl-none shadow-sm border border-gray-100 text-sm leading-relaxed">
                            ${formatMessageText(message)}
                        </div>
                        <span class="text-[9px] text-gray-400 mt-1">${time}</span>
                    </div>
                `;
            }
>>>>>>> main

        // Render ngay lập tức lên DOM
        const tempTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        appendAdminMessageToDOM('admin', text, tempTime);
        scrollAdminChatToBottom();

        try {
            const response = await fetch(`/admin/chats/sessions/${activeSessionId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text })
            });
            const data = await response.json();
            if (data.success) {
                // Update danh sách session sidebar để cập nhật preview
                fetchSessions();
            }
        } catch (error) {
            console.error('Lỗi gửi phản hồi của admin:', error);
        }
    }

    // Append tin nhắn vào giao diện admin chat
    function appendAdminMessageToDOM(sender, message, time) {
        const container = document.getElementById('admin-chat-messages');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-2.5 admin-msg-item ' + (sender === 'admin' ? 'justify-end' : '');

        if (sender === 'admin') {
            wrapper.innerHTML = `
                <div class="flex flex-col items-end max-w-[75%]">
                    <div class="bg-gray-800 text-white p-3.5 rounded-[18px] rounded-tr-none shadow-sm text-sm leading-relaxed">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] text-gray-400 mt-1">${time}</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-xs shrink-0 font-bold">
                    AD
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-[#e8634a] text-white flex items-center justify-center text-xs shrink-0 font-bold">
                    KH
                </div>
                <div class="flex flex-col items-start max-w-[70%]">
                    <div class="bg-white text-espresso p-3.5 rounded-[18px] rounded-tl-none shadow-sm border border-gray-100 text-sm leading-relaxed">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] text-gray-400 mt-1">${time}</span>
                </div>
            `;
        }

        container.appendChild(wrapper);
    }

<<<<<<< HEAD
    // Cuộn khung chat xuống đáy
    function scrollAdminChatToBottom() {
        const container = document.getElementById('admin-chat-messages');
        container.scrollTop = container.scrollHeight;
    }

    // Cập nhật giao diện nút Bật/Tắt Bot AI
    function updateBotToggleButton(isEnabled) {
        const btn = document.getElementById('btn-toggle-bot');
        if (isEnabled) {
            btn.innerText = 'Đang Bật';
            btn.className = 'px-3 py-1 text-[11px] font-bold rounded-full transition-all duration-200 shadow-sm bg-green-100 text-green-700 hover:bg-green-200';
        } else {
            btn.innerText = 'Đang Tắt';
            btn.className = 'px-3 py-1 text-[11px] font-bold rounded-full transition-all duration-200 shadow-sm bg-red-100 text-red-700 hover:bg-red-200';
        }
    }

    // Gửi yêu cầu đổi trạng thái Bot AI lên server
    async function toggleBotStatus() {
        if (!activeSessionId) return;
        try {
            const response = await fetch(`/admin/chats/sessions/${activeSessionId}/toggle-bot`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await response.json();
            if (data.success) {
                updateBotToggleButton(data.is_bot_enabled);
                fetchSessions();
            }
        } catch (error) {
            console.error('Lỗi thay đổi trạng thái Bot:', error);
        }
    }

    // Format tin nhắn hỗ trợ markdown link và xuống dòng
    function formatMessageText(text) {
        if (!text) return '';
        let escaped = escapeHTML(text);
        escaped = escaped.replace(/\n/g, '<br>');
        return escaped.replace(/\[([^\]]+)\]\(((?:https?:\/\/|\/)[^\s)]+)\)/g, (match, label, url) => {
            return `<a href="${url}" target="_blank" class="text-[#e8634a] hover:underline font-bold">${label}</a>`;
        });
    }

    // Tránh lỗi XSS
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    // Chạy lần đầu và khởi tạo polling
    fetchSessions();
    sessionPoll = setInterval(fetchSessions, 4000);
</script>
@endpush
=======
        // Chạy lần đầu và khởi tạo polling
        fetchSessions();
        sessionPoll = setInterval(fetchSessions, 4000);
    </script>
@endsection
>>>>>>> main
