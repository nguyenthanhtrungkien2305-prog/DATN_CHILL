<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trợ lý AI Quản lý - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Markdown style formatting */
        .ai-message-bubble ul {
            list-style-type: disc;
            margin-left: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .ai-message-bubble ol {
            list-style-type: decimal;
            margin-left: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .ai-message-bubble li {
            margin-bottom: 0.25rem;
        }
        .ai-message-bubble table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.85rem;
        }
        .ai-message-bubble th, .ai-message-bubble td {
            border: 1px solid rgba(229, 231, 235, 1);
            padding: 0.5rem 0.75rem;
            text-align: left;
        }
        .ai-message-bubble th {
            background-color: rgba(243, 244, 246, 1);
            font-weight: 600;
        }
        .ai-message-bubble p {
            margin-bottom: 0.5rem;
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        /* Custom scrollbar for chat */
        .chat-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 20px;
        }
        /* Pulsing dot animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .4; transform: scale(1.15); }
        }
        .animate-pulse-custom {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- MAIN CONTENT AREA --}}
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        {{-- Header --}}
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-gray-800">🤖 Trợ lý AI Quản trị</h2>
                <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-medium border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse-custom"></span>
                    Trực tuyến
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button id="clear-chat-btn" class="text-sm text-gray-500 hover:text-red-500 flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                    🗑️ Xóa hội thoại
                </button>
                <div class="h-6 w-px bg-gray-200"></div>
                <span class="text-sm text-gray-600">Quản trị viên: <strong>{{ Auth::user()->name }}</strong></span>
            </div>
        </header>

        {{-- Workspace --}}
        <div class="flex-1 flex p-6 gap-6 overflow-hidden">
            
            {{-- Chat Box --}}
            <div class="flex-1 bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden relative">
                
                {{-- Message History --}}
                <div id="chat-messages-container" class="flex-1 p-6 overflow-y-auto chat-scroll space-y-4">
                    {{-- Welcome Message --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0">
                            🤖
                        </div>
                        <div class="bg-gray-100 text-gray-800 p-4 rounded-3xl rounded-tl-none max-w-[75%] shadow-sm text-sm leading-relaxed">
                            Dạ, em kính chào Quản lý! Em là <strong>Trợ lý AI Quản lý của Chill Chill</strong>. ☕ <br><br>
                            Em có thể giúp Quản lý thực hiện nhanh các tác vụ sau:
                            <ul class="list-disc pl-5 mt-2 space-y-1 font-medium text-gray-700">
                                <li>Tạo mã giảm giá (voucher) mới cho cửa hàng.</li>
                                <li>Xem & tra cứu giá bán danh sách sản phẩm.</li>
                                <li>Điều chỉnh giá bán sản phẩm hoặc biến thể size.</li>
                                <li>Tạo chương trình giảm giá cho toàn bộ sản phẩm của một danh mục.</li>
                            </ul>
                            <br>
                            Quản lý có thể chọn các gợi ý nhanh bên phải hoặc nhập yêu cầu trực tiếp bên dưới ạ!
                        </div>
                    </div>

                    {{-- Session History --}}
                    @foreach($history as $msg)
                        @if($msg['sender_type'] === 'customer')
                            {{-- Admin message --}}
                            <div class="flex items-start gap-3 justify-end">
                                <div class="bg-[#2B2623] text-white p-4 rounded-3xl rounded-tr-none max-w-[75%] shadow-sm text-sm leading-relaxed">
                                    {{ $msg['message'] }}
                                </div>
                                <div class="w-10 h-10 rounded-2xl bg-[#e8634a] text-white flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                                    AD
                                </div>
                            </div>
                        @else
                            {{-- AI Response --}}
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0">
                                    🤖
                                </div>
                                <div class="bg-gray-50 border border-gray-100 text-gray-800 p-4 rounded-3xl rounded-tl-none max-w-[75%] shadow-sm text-sm leading-relaxed ai-message-bubble">
                                    {!! nl2br(e($msg['message'])) !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Loading Indicator --}}
                <div id="ai-loading" class="hidden absolute bottom-24 left-6 flex items-center gap-2 px-4 py-2 rounded-2xl bg-gray-50 border border-gray-100 text-gray-500 text-xs shadow-sm">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#e8634a] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#e8634a]"></span>
                    </span>
                    Trợ lý AI đang xử lý yêu cầu...
                </div>

                {{-- Input Bar --}}
                <div class="p-6 border-t border-gray-100 shrink-0">
                    <form id="chat-form" class="flex gap-3">
                        @csrf
                        <input type="text" id="chat-input" placeholder="Nhập yêu cầu quản lý tại đây (Ví dụ: 'Tạo mã voucher CHILL50k giảm 50.000đ từ ngày mai')..." class="flex-1 px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-[#e8634a]/30 focus:border-[#e8634a] transition focus:bg-white" required>
                        <button type="submit" id="send-btn" class="bg-[#e8634a] text-white px-6 rounded-2xl hover:bg-[#d5523b] transition flex items-center justify-center gap-2 font-medium text-sm">
                            Gửi đi 🚀
                        </button>
                    </form>
                </div>

            </div>

            {{-- Quick Prompts Sidebar --}}
            <div class="w-80 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col gap-6 overflow-y-auto shrink-0">
                <div>
                    <h3 class="text-base font-semibold text-gray-800 mb-1">💡 Gợi ý nhanh</h3>
                    <p class="text-xs text-gray-400">Nhấp vào gợi ý để yêu cầu AI thực hiện nhanh</p>
                </div>

                <div class="space-y-4">
                    {{-- Section 1 --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">🎟️ Quản lý Voucher</span>
                        <div class="space-y-2">
                            <button class="quick-prompt-btn w-full text-left p-3 rounded-2xl border border-gray-100 hover:border-[#e8634a] hover:bg-orange-50/20 text-xs font-medium text-gray-700 transition">
                                Tạo mã KM30 giảm 30% cho đơn tối thiểu 100k
                            </button>
                            <button class="quick-prompt-btn w-full text-left p-3 rounded-2xl border border-gray-100 hover:border-[#e8634a] hover:bg-orange-50/20 text-xs font-medium text-gray-700 transition">
                                Tạo mã CHILL50 giảm 50k cho đơn từ 200k
                            </button>
                        </div>
                    </div>

                    {{-- Section 2 --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">☕ Điều chỉnh sản phẩm</span>
                        <div class="space-y-2">
                            <button class="quick-prompt-btn w-full text-left p-3 rounded-2xl border border-gray-100 hover:border-[#e8634a] hover:bg-orange-50/20 text-xs font-medium text-gray-700 transition">
                                Tra cứu danh sách sản phẩm và giá bán
                            </button>
                            <button class="quick-prompt-btn w-full text-left p-3 rounded-2xl border border-gray-100 hover:border-[#e8634a] hover:bg-orange-50/20 text-xs font-medium text-gray-700 transition">
                                Đổi giá Món Ngon Chill Chill 1 thành 39,000đ
                            </button>
                            <button class="quick-prompt-btn w-full text-left p-3 rounded-2xl border border-gray-100 hover:border-[#e8634a] hover:bg-orange-50/20 text-xs font-medium text-gray-700 transition">
                                Giảm giá 10% cho tất cả sản phẩm nhóm Đá Xay
                            </button>
                        </div>
                    </div>

                    {{-- Safety constraint warning display --}}
                    <div class="mt-auto p-4 rounded-2xl bg-amber-50 border border-amber-100">
                        <span class="text-xs font-semibold text-amber-800 block mb-1">🛡️ Giới hạn Bảo mật:</span>
                        <span class="text-[11px] text-amber-700 leading-normal block">
                            Bot AI này chỉ hoạt động trong khuôn khổ Quản lý Voucher và Giá Sản phẩm. Nó sẽ từ chối trả lời mọi vấn đề nằm ngoài phạm vi này.
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </main>

    {{-- Script xử lý AJAX chat --}}
    <script>
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatContainer = document.getElementById('chat-messages-container');
        const loadingIndicator = document.getElementById('ai-loading');
        const clearBtn = document.getElementById('clear-chat-btn');
        const quickPromptBtns = document.querySelectorAll('.quick-prompt-btn');

        // Hàm cuộn chat xuống đáy
        function scrollToBottom() {
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: 'smooth'
            });
        }

        // Tự động cuộn khi load trang
        scrollToBottom();

        // Xử lý gửi tin nhắn
        async function handleSendMessage(messageText) {
            if (!messageText.trim()) return;

            // 1. Hiển thị tin nhắn người dùng lên giao diện
            const userMsgHtml = `
                <div class="flex items-start gap-3 justify-end">
                    <div class="bg-[#2B2623] text-white p-4 rounded-3xl rounded-tr-none max-w-[75%] shadow-sm text-sm leading-relaxed">
                        ${escapeHtml(messageText)}
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-[#e8634a] text-white flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                        AD
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', userMsgHtml);
            chatInput.value = '';
            scrollToBottom();

            // 2. Hiển thị loading & khóa input
            loadingIndicator.classList.remove('hidden');
            chatInput.disabled = true;
            document.getElementById('send-btn').disabled = true;

            try {
                const response = await fetch("{{ route('admin.ai.chat', [], false) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ message: messageText })
                });

                const data = await response.json();
                loadingIndicator.classList.add('hidden');

                if (data.success) {
                    // Định dạng và hiển thị phản hồi từ AI
                    const aiMsgHtml = `
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0">
                                🤖
                            </div>
                            <div class="bg-gray-50 border border-gray-100 text-gray-800 p-4 rounded-3xl rounded-tl-none max-w-[75%] shadow-sm text-sm leading-relaxed ai-message-bubble">
                                ${formatAiResponse(data.reply)}
                            </div>
                        </div>
                    `;
                    chatContainer.insertAdjacentHTML('beforeend', aiMsgHtml);
                } else {
                    alert("Đã xảy ra lỗi khi gửi yêu cầu đến trợ lý AI.");
                }
            } catch (error) {
                console.error(error);
                loadingIndicator.classList.add('hidden');
                alert("Không thể kết nối đến server. Chi tiết: " + error.message);
            } finally {
                chatInput.disabled = false;
                document.getElementById('send-btn').disabled = false;
                chatInput.focus();
                scrollToBottom();
            }
        }

        // Event listener cho Form submit
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleSendMessage(chatInput.value);
        });

        // Event listener cho nút Gợi ý nhanh
        quickPromptBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                handleSendMessage(this.innerText.trim());
            });
        });

        // Event listener xóa lịch sử
        clearBtn.addEventListener('click', async function() {
            if (confirm("Quản lý có chắc chắn muốn xóa toàn bộ lịch sử trò chuyện này không?")) {
                try {
                    const response = await fetch("{{ route('admin.ai.clear', [], false) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        // Reset giao diện
                        location.reload();
                    }
                } catch (error) {
                    alert("Không thể xóa lịch sử. Chi tiết: " + error.message);
                }
            }
        });

        // Helper: Escape HTML chống XSS
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Helper: Format markdown responses from AI using marked.js
        function formatAiResponse(text) {
            return marked.parse(text);
        }
    </script>
</body>
</html>
