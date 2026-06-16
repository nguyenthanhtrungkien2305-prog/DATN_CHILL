{{-- Nút bong bóng chat nổi --}}
<button id="chat-bubble" onclick="toggleChatWindow()" class="fixed bottom-6 right-6 z-[999] w-14 h-14 bg-coral text-white rounded-full flex items-center justify-center shadow-[0_8px_30px_rgb(232,99,74,0.4)] hover:scale-110 active:scale-95 transition-all duration-300 group">
    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden" id="chat-badge">0</span>
    <svg class="w-7 h-7 transform group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
</button>

{{-- Khung cửa sổ chat --}}
<div id="chat-window" class="fixed bottom-24 right-6 z-[999] w-[350px] h-[480px] bg-white rounded-[24px] shadow-[0_15px_50px_rgba(43,38,35,0.15)] border border-espresso/5 flex flex-col overflow-hidden translate-y-10 opacity-0 pointer-events-none transition-all duration-300 ease-out">
    {{-- Header --}}
    <div class="bg-espresso text-cream p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-[#FFF0D4] flex items-center justify-center text-lg">
                    ☕
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-espresso rounded-full animate-pulse"></div>
            </div>
            <div>
                <h4 class="font-bold text-sm text-white">Chill Chill Support</h4>
                <p class="text-[10px] text-cream/70">Thường phản hồi trong vài phút</p>
            </div>
        </div>
        <button onclick="toggleChatWindow()" class="text-cream/60 hover:text-white transition-colors p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Khung hiển thị tin nhắn --}}
    <div id="chat-messages-container" class="flex-1 p-4 overflow-y-auto bg-[#FAF7F2] space-y-3 flex flex-col">
        {{-- Tin nhắn mặc định chào mừng --}}
        <div class="flex items-start gap-2 max-w-[80%]">
            <div class="w-7 h-7 rounded-full bg-[#FFF0D4] flex items-center justify-center text-xs shrink-0">
                ☕
            </div>
            <div id="chat-welcome-msg" class="bg-white text-espresso p-3 rounded-[18px] rounded-tl-none shadow-sm text-xs leading-relaxed">
                Chào bạn! Chill Chill có thể giúp gì cho bạn hôm nay? Hãy gửi lời nhắn nhé!
            </div>
        </div>
    </div>

    {{-- Khung nhập tin nhắn --}}
    <form id="chat-form" onsubmit="handleSend(event)" class="p-3 border-t border-espresso/5 bg-white flex items-center gap-2">
        <input type="text" id="chat-input" placeholder="Nhập tin nhắn..." autocomplete="off" class="flex-1 px-4 py-2 bg-[#FAF7F2] border border-transparent rounded-full text-xs focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso placeholder-espresso/40">
        <button type="submit" class="w-8 h-8 rounded-full bg-coral hover:bg-[#d5523b] text-white flex items-center justify-center shrink-0 shadow-md shadow-coral/20 hover:scale-105 active:scale-95 transition-all">
            <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
            </svg>
        </button>
    </form>
</div>

<script>
    let chatOpen = false;
    let chatToken = localStorage.getItem('chill_chat_token');
    let chatPollInterval = null;

    // Khởi tạo session chat
    async function initChatSession() {
        try {
            const response = await fetch('{{ route('chat.start') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ session_token: chatToken })
            });
            const data = await response.json();
            if (data.success) {
                chatToken = data.session_token;
                localStorage.setItem('chill_chat_token', chatToken);
                
                // Hiển thị lại các tin nhắn cũ
                if (data.messages && data.messages.length > 0) {
                    const container = document.getElementById('chat-messages-container');
                    // Xóa các tin nhắn trước đó (giữ lại tin nhắn chào mừng)
                    container.innerHTML = `
                        <div class="flex items-start gap-2 max-w-[80%]">
                            <div class="w-7 h-7 rounded-full bg-[#FFF0D4] flex items-center justify-center text-xs shrink-0">
                                ☕
                            </div>
                            <div id="chat-welcome-msg" class="bg-white text-espresso p-3 rounded-[18px] rounded-tl-none shadow-sm text-xs leading-relaxed">
                                ${getRealTimeGreeting()}
                            </div>
                        </div>
                    `;
                    data.messages.forEach(msg => {
                        appendMessageToDOM(msg.sender_type, msg.message, formatTime(msg.created_at));
                    });
                    scrollToBottom();
                }
            }
        } catch (error) {
            console.error('Lỗi khởi tạo chat:', error);
        }
    }

    // Toggle đóng mở cửa sổ
    function toggleChatWindow() {
        const windowEl = document.getElementById('chat-window');
        chatOpen = !chatOpen;

        if (chatOpen) {
            // Mỗi lần mở lên sẽ bắt đầu một đoạn chat mới
            chatToken = null;
            localStorage.removeItem('chill_chat_token');

            // Reset khung hiển thị tin nhắn về trạng thái ban đầu (chỉ giữ tin nhắn chào mừng)
            const container = document.getElementById('chat-messages-container');
            container.innerHTML = `
                <div class="flex items-start gap-2 max-w-[80%]">
                    <div class="w-7 h-7 rounded-full bg-[#FFF0D4] flex items-center justify-center text-xs shrink-0">
                        ☕
                    </div>
                    <div id="chat-welcome-msg" class="bg-white text-espresso p-3 rounded-[18px] rounded-tl-none shadow-sm text-xs leading-relaxed">
                        ${getRealTimeGreeting()}
                    </div>
                </div>
            `;

            windowEl.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
            windowEl.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
            
            // Xóa badge thông báo
            document.getElementById('chat-badge').innerText = '0';
            document.getElementById('chat-badge').classList.add('hidden');

            // Khởi tạo/Cập nhật session
            initChatSession().then(() => {
                fetchMessages();
                // Bắt đầu lập trình tự động kéo tin nhắn mới mỗi 3 giây
                chatPollInterval = setInterval(fetchMessages, 3000);
            });
            
            setTimeout(() => {
                document.getElementById('chat-input').focus();
            }, 300);
        } else {
            windowEl.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            windowEl.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
            
            // Dừng polling để tiết kiệm tài nguyên
            if (chatPollInterval) {
                clearInterval(chatPollInterval);
                chatPollInterval = null;
            }
        }
    }

    // Cập nhật số lượng giỏ hàng trên Header của Website
    function updateHeaderCartBadge(count) {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.innerText = count;
            if (count > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    // Lấy tin nhắn mới nhất
    async function fetchMessages() {
        if (!chatToken) return;
        try {
            const response = await fetch(`{{ route('chat.messages') }}?session_token=${chatToken}`);
            const data = await response.json();
            if (data.success) {
                const container = document.getElementById('chat-messages-container');
                const prevCount = container.querySelectorAll('.chat-msg-item').length;
                
                // Nếu số lượng tin nhắn mới khác biệt, render lại
                if (data.messages.length > prevCount) {
                    // Lấy các tin nhắn mới
                    const newMessages = data.messages.slice(prevCount);
                    newMessages.forEach(msg => {
                        appendMessageToDOM(msg.sender_type, msg.message, msg.created_at);
                        
                        // Nếu tin nhắn mới từ admin và cửa sổ đang đóng, hiển thị badge
                        if (msg.sender_type === 'admin' && !chatOpen) {
                            const badge = document.getElementById('chat-badge');
                            let currentBadgeCount = parseInt(badge.innerText) || 0;
                            badge.innerText = currentBadgeCount + 1;
                            badge.classList.remove('hidden');
                        }
                    });
                    scrollToBottom();
                }

                // Xử lý cờ cập nhật giỏ hàng từ Bot AI
                if (data.cart_updated) {
                    updateHeaderCartBadge(data.cart_count);
                    
                    // Nếu đang xem trang giỏ hàng hoặc checkout, reload để thấy thay đổi
                    if (window.location.pathname.startsWith('/cart') || window.location.pathname.startsWith('/checkout')) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }

                // Xử lý cờ tạo đơn hàng từ Bot AI
                if (data.order_created) {
                    const noticeText = data.payment_method === 'qr'
                        ? 'Đặt đơn hàng thành công! Đang tự động chuyển hướng bạn tới trang quét mã QR thanh toán...'
                        : 'Đặt đơn hàng thành công! Đang tự động chuyển hướng bạn tới trang danh sách đơn hàng...';

                    // Thêm thông báo hệ thống vào khung chat
                    appendMessageToDOM('admin', `✨ ${noticeText}`, new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }));
                    scrollToBottom();

                    // Chuyển hướng sau 2 giây
                    setTimeout(() => {
                        if (data.payment_method === 'qr') {
                            window.location.href = `/checkout/payment-qr/${data.order_created}`;
                        } else {
                            window.location.href = `/tai-khoan/don-hang`;
                        }
                    }, 2000);
                }
            }
        } catch (error) {
            console.error('Lỗi lấy tin nhắn:', error);
        }
    }

    // Hiển thị trạng thái đang soạn tin (typing indicator)
    function showTypingIndicator() {
        if (document.getElementById('chat-typing-indicator')) return;

        const container = document.getElementById('chat-messages-container');
        const wrapper = document.createElement('div');
        wrapper.id = 'chat-typing-indicator';
        wrapper.className = 'flex items-start gap-2'; // Không sử dụng chat-msg-item để tránh xung đột đếm tin nhắn
        wrapper.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-[#FFF0D4] flex items-center justify-center text-xs shrink-0 animate-pulse">
                ☕
            </div>
            <div class="flex flex-col items-start max-w-[80%]">
                <div class="bg-white text-espresso px-4 py-2.5 rounded-[18px] rounded-tl-none shadow-sm flex items-center gap-1.5 h-8">
                    <span class="w-1.5 h-1.5 bg-espresso/50 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 bg-espresso/50 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 bg-espresso/50 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
                <span class="text-[9px] text-espresso/40 mt-1 ml-1">Chill Chill Support đang nhập...</span>
            </div>
        `;

        container.appendChild(wrapper);
        scrollToBottom();
    }

    // Ẩn trạng thái đang soạn tin
    function hideTypingIndicator() {
        const indicator = document.getElementById('chat-typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    // Gửi tin nhắn
    async function handleSend(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        // Hiển thị tin nhắn ngay lập tức trên giao diện (màu cam, bên phải)
        const tempTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        appendMessageToDOM('customer', text, tempTime);
        scrollToBottom();

        // Hiển thị hiệu ứng đang soạn tin của trợ lý AI
        showTypingIndicator();

        try {
            const response = await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_token: chatToken,
                    message: text
                })
            });
            const data = await response.json();
            if (data.success) {
                // Đã gửi thành công lên server, session token đã được đồng bộ
                if (!chatToken) {
                    chatToken = data.message.chat_session_id; // phòng hờ
                }
                // Tải tin nhắn mới lập tức để cập nhật phản hồi của Bot và ẩn indicator
                await fetchMessages();
            }
        } catch (error) {
            console.error('Lỗi gửi tin nhắn:', error);
        } finally {
            // Luôn ẩn trạng thái đang soạn tin khi hoàn thành request
            hideTypingIndicator();
        }
    }

    // Thêm tin nhắn vào khung chat
    function appendMessageToDOM(sender, message, time) {
        const container = document.getElementById('chat-messages-container');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-2 chat-msg-item ' + (sender === 'customer' ? 'justify-end' : '');

        if (sender === 'customer') {
            wrapper.innerHTML = `
                <div class="flex flex-col items-end max-w-[85%]">
                    <div class="bg-coral text-white p-3 rounded-[18px] rounded-tr-none shadow-sm text-xs leading-relaxed">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] text-espresso/40 mt-1">${time}</span>
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-[#FFF0D4] flex items-center justify-center text-xs shrink-0">
                    ☕
                </div>
                <div class="flex flex-col items-start max-w-[80%]">
                    <div class="bg-white text-espresso p-3 rounded-[18px] rounded-tl-none shadow-sm text-xs leading-relaxed">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] text-espresso/40 mt-1">${time}</span>
                </div>
            `;
        }

        container.appendChild(wrapper);
    }

    // Cuộn xuống đáy khung chat
    function scrollToBottom() {
        const container = document.getElementById('chat-messages-container');
        container.scrollTop = container.scrollHeight;
    }

    // Tiện ích format thời gian từ chuỗi ISO
    function formatTime(isoString) {
        if (!isoString) return '';
        try {
            const date = new Date(isoString);
            return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return '';
        }
    }

    // Format tin nhắn (chuyển đổi Markdown và xuống dòng)
    function formatMessageText(text) {
        if (!text) return '';
        let escaped = escapeHTML(text);
        // Thay thế xuống dòng bằng thẻ <br>
        escaped = escaped.replace(/\n/g, '<br>');
        // Thay thế markdown link [Text](URL) bằng thẻ <a>
        return escaped.replace(/\[([^\]]+)\]\(((?:https?:\/\/|\/)[^\s)]+)\)/g, (match, label, url) => {
            return `<a href="${url}" target="_blank" class="text-coral underline font-bold hover:text-[#d5523b] transition-colors">${label}</a>`;
        });
    }

    // Tránh lỗ hổng XSS
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

    function getRealTimeGreeting() {
        const now = new Date();
        const hour = now.getHours();
        const minute = String(now.getMinutes()).padStart(2, '0');
        const daysOfWeek = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
        const dayName = daysOfWeek[now.getDay()];
        const timeStr = `${hour}:${minute} (${dayName})`;
        
        let period = "";
        let blessing = "";
        if (hour >= 5 && hour < 11) {
            period = "sáng";
            blessing = "Chúc bạn một buổi sáng tràn đầy năng lượng! ☕";
        } else if (hour >= 11 && hour < 14) {
            period = "trưa";
            blessing = "Chúc bạn một buổi trưa vui vẻ và ngon miệng! 🍹";
        } else if (hour >= 14 && hour < 18) {
            period = "chiều";
            blessing = "Chúc bạn một buổi chiều xế ngọt ngào và thư giãn! 🍰";
        } else if (hour >= 18 && hour < 23) {
            period = "tối";
            blessing = "Chúc bạn một buổi tối ấm áp và thư giãn! ✨";
        } else {
            period = "đêm";
            blessing = "Chúc bạn một đêm ngon giấc! 🌙";
        }
        
        if (hour >= 23 || hour < 5) {
            return `Dạ, bây giờ là ${timeStr} khuya, Chill Chill chào bạn! ${blessing} Các đơn hàng đặt giờ này sẽ được quán chuẩn bị vào sáng mai khi mở cửa. Bạn cần tư vấn món gì cứ nhắn Chill Chill nhé!`;
        } else {
            return `Dạ, bây giờ là ${timeStr} ${period}, Chill Chill chào bạn! ${blessing} Chill Chill có thể giúp gì cho bạn hôm nay?`;
        }
    }

    // Lắng nghe tải trang để kiểm tra badge chưa đọc ban đầu
    document.addEventListener('DOMContentLoaded', () => {
        const welcomeEl = document.getElementById('chat-welcome-msg');
        if (welcomeEl) {
            welcomeEl.innerText = getRealTimeGreeting();
        }
        if (chatToken) {
            // Chạy ngầm lấy tin nhắn lần đầu để cập nhật badge nếu có tin nhắn chưa đọc từ admin
            fetchMessages();
        }
    });
</script>
