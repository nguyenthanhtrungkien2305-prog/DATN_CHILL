{{-- Nút bong bóng chat nổi --}}
<button id="chat-bubble" onclick="toggleChatWindow()" class="fixed bottom-6 right-6 z-[999] w-14 h-14 bg-coral text-white rounded-full flex items-center justify-center shadow-[0_8px_30px_rgb(232,99,74,0.4)] hover:scale-110 active:scale-95 transition-all duration-300 group">
    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden" id="chat-badge">0</span>
    <svg class="w-7 h-7 transform group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
</button>

{{-- Khung cửa sổ chat --}}
<div id="chat-window" class="fixed bottom-24 right-6 z-[999] w-[360px] h-[520px] bg-white rounded-[28px] shadow-[0_20px_60px_rgba(43,38,35,0.2)] border border-espresso/10 flex flex-col overflow-hidden translate-y-10 opacity-0 pointer-events-none transition-all duration-300 ease-out">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-espresso via-[#4a322c] to-espresso text-cream p-4 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-[#FFF0D4] flex items-center justify-center text-xl shadow-inner">
                    ☕
                </div>
                <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-espresso rounded-full animate-pulse"></div>
            </div>
            <div>
                <h4 class="font-bold text-sm text-white flex items-center gap-1.5">
                    Chill Chill AI Assistant
                    <span class="text-[9px] bg-coral/20 text-coral font-bold px-1.5 py-0.5 rounded-full border border-coral/30">AI Bot 24/7</span>
                </h4>
                <p class="text-[10px] text-cream/80 font-medium">Trợ lý tư vấn đồ uống & bánh ngọt</p>
            </div>
        </div>
        <button onclick="toggleChatWindow()" class="text-cream/70 hover:text-white hover:bg-white/10 rounded-full p-1.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Khung hiển thị tin nhắn --}}
    <div id="chat-messages-container" class="flex-1 p-4 overflow-y-auto bg-[#FAF7F2] space-y-3 flex flex-col">
        {{-- Tin nhắn mặc định chào mừng --}}
        <div class="flex items-start gap-2 max-w-[85%]">
            <div class="w-8 h-8 rounded-full bg-[#FFF0D4] flex items-center justify-center text-sm shrink-0 shadow-xs border border-espresso/5">
                ☕
            </div>
            <div class="bg-white text-espresso p-3.5 rounded-[20px] rounded-tl-none shadow-sm text-xs leading-relaxed border border-espresso/5">
                <p class="font-bold text-coral mb-1">Dạ, Chill Chill xin chào bạn ạ! ☕✨</p>
                <p>Bạn cần mình tư vấn chọn món nước giải nhiệt, cà phê tỉnh táo hay bánh ngọt nào hôm nay ạ?</p>
            </div>
        </div>

        {{-- Gợi ý câu hỏi nhanh (Quick Chips) --}}
        <div id="quick-chips-container" class="flex flex-wrap gap-1.5 my-2 pl-9">
            <button onclick="sendQuickMessage('Gợi ý cho mình món Cà phê đậm vị tỉnh táo nhé ☕')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                ☕ Cà phê tỉnh táo
            </button>
            <button onclick="sendQuickMessage('Gợi ý cho mình các món Trà trái cây thanh mát giải nhiệt nhé 🍹')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                🍹 Trà trái cây giải nhiệt
            </button>
            <button onclick="sendQuickMessage('Quán có những món Bánh ngọt tươi nào thơm ngon ạ? 🍰')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                🍰 Bánh ngọt tươi
            </button>
            <button onclick="sendQuickMessage('Có những Gói Combo Tiết Kiệm nào ưu đãi không ạ? 🎁')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                🎁 Combo tiết kiệm
            </button>
        </div>
    </div>

    {{-- Khung nhập tin nhắn --}}
    <form id="chat-form" onsubmit="handleSend(event)" class="p-3 border-t border-espresso/5 bg-white flex items-center gap-2">
        <input type="text" id="chat-input" placeholder="Nhập tin nhắn tư vấn..." autocomplete="off" class="flex-1 px-4 py-2.5 bg-[#FAF7F2] border border-transparent rounded-full text-xs focus:outline-none focus:border-coral focus:bg-white transition-all text-espresso placeholder-espresso/40">
        <button type="submit" class="w-9 h-9 rounded-full bg-coral hover:bg-[#d5523b] text-white flex items-center justify-center shrink-0 shadow-md shadow-coral/20 hover:scale-105 active:scale-95 transition-all">
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
                    container.innerHTML = `
                        <div class="flex items-start gap-2 max-w-[85%]">
                            <div class="w-8 h-8 rounded-full bg-[#FFF0D4] flex items-center justify-center text-sm shrink-0 shadow-xs border border-espresso/5">
                                ☕
                            </div>
                            <div class="bg-white text-espresso p-3.5 rounded-[20px] rounded-tl-none shadow-sm text-xs leading-relaxed border border-espresso/5">
                                <p class="font-bold text-coral mb-1">Dạ, Chill Chill xin chào bạn ạ! ☕✨</p>
                                <p>Bạn cần mình tư vấn chọn món nước giải nhiệt, cà phê tỉnh táo hay bánh ngọt nào hôm nay ạ?</p>
                            </div>
                        </div>
                        <div id="quick-chips-container" class="flex flex-wrap gap-1.5 my-2 pl-9">
                            <button onclick="sendQuickMessage('Gợi ý cho mình món Cà phê đậm vị tỉnh táo nhé ☕')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                                ☕ Cà phê tỉnh táo
                            </button>
                            <button onclick="sendQuickMessage('Gợi ý cho mình các món Trà trái cây thanh mát giải nhiệt nhé 🍹')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                                🍹 Trà trái cây giải nhiệt
                            </button>
                            <button onclick="sendQuickMessage('Quán có những món Bánh ngọt tươi nào thơm ngon ạ? 🍰')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                                🍰 Bánh ngọt tươi
                            </button>
                            <button onclick="sendQuickMessage('Có những Gói Combo Tiết Kiệm nào ưu đãi không ạ? 🎁')" class="bg-white hover:bg-coral hover:text-white text-espresso/80 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-espresso/10 shadow-2xs transition-all active:scale-95">
                                🎁 Combo tiết kiệm
                            </button>
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
            windowEl.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
            windowEl.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
            
            document.getElementById('chat-badge').innerText = '0';
            document.getElementById('chat-badge').classList.add('hidden');

            initChatSession().then(() => {
                fetchMessages();
                chatPollInterval = setInterval(fetchMessages, 3000);
            });
            
            setTimeout(() => {
                document.getElementById('chat-input').focus();
            }, 300);
        } else {
            windowEl.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            windowEl.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
            
            if (chatPollInterval) {
                clearInterval(chatPollInterval);
                chatPollInterval = null;
            }
        }
    }

    // Gửi tin nhắn từ Quick Chips
    function sendQuickMessage(text) {
        document.getElementById('chat-input').value = text;
        const form = document.getElementById('chat-form');
        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
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
                
                if (data.messages.length > prevCount) {
                    removeTypingIndicator();
                    const newMessages = data.messages.slice(prevCount);
                    newMessages.forEach(msg => {
                        appendMessageToDOM(msg.sender_type, msg.message, msg.created_at);
                        
                        if (msg.sender_type === 'admin' && !chatOpen) {
                            const badge = document.getElementById('chat-badge');
                            let currentBadgeCount = parseInt(badge.innerText) || 0;
                            badge.innerText = currentBadgeCount + 1;
                            badge.classList.remove('hidden');
                        }
                    });
                    scrollToBottom();
                }
            }
        } catch (error) {
            console.error('Lỗi lấy tin nhắn:', error);
        }
    }

    // Gửi tin nhắn
    async function handleSend(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        const tempTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        appendMessageToDOM('customer', text, tempTime);
        showTypingIndicator();
        scrollToBottom();

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
                if (data.session_token) {
                    chatToken = data.session_token;
                    localStorage.setItem('chill_chat_token', chatToken);
                }
                removeTypingIndicator();
                if (data.ai_reply) {
                    appendMessageToDOM('admin', data.ai_reply.message, data.ai_reply.created_at);
                    scrollToBottom();
                }
            } else {
                removeTypingIndicator();
            }
        } catch (error) {
            console.error('Lỗi gửi tin nhắn:', error);
            removeTypingIndicator();
        }
    }

    // Hiển thị hiệu ứng gõ phím Typing Indicator
    function showTypingIndicator() {
        removeTypingIndicator();
        const container = document.getElementById('chat-messages-container');
        const typingDiv = document.createElement('div');
        typingDiv.id = 'ai-typing-indicator';
        typingDiv.className = 'flex items-start gap-2 chat-msg-item';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-[#FFF0D4] flex items-center justify-center text-sm shrink-0 border border-espresso/5 shadow-xs">
                ☕
            </div>
            <div class="bg-white text-espresso px-4 py-3 rounded-[20px] rounded-tl-none shadow-sm text-xs leading-relaxed flex items-center gap-1 border border-espresso/5">
                <span class="w-1.5 h-1.5 bg-coral rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-coral rounded-full animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 bg-coral rounded-full animate-bounce [animation-delay:0.4s]"></span>
            </div>
        `;
        container.appendChild(typingDiv);
    }

    function removeTypingIndicator() {
        const el = document.getElementById('ai-typing-indicator');
        if (el) el.remove();
    }

    // Thêm tin nhắn vào khung chat
    function appendMessageToDOM(sender, message, time) {
        const container = document.getElementById('chat-messages-container');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-2 chat-msg-item ' + (sender === 'customer' ? 'justify-end' : '');

        if (sender === 'customer') {
            wrapper.innerHTML = `
                <div class="flex flex-col items-end max-w-[85%]">
                    <div class="bg-coral text-white p-3.5 rounded-[20px] rounded-tr-none shadow-sm text-xs leading-relaxed">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] text-espresso/40 mt-1">${time}</span>
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-[#FFF0D4] flex items-center justify-center text-sm shrink-0 border border-espresso/5 shadow-xs">
                    ☕
                </div>
                <div class="flex flex-col items-start max-w-[85%]">
                    <div class="bg-white text-espresso p-3.5 rounded-[20px] rounded-tl-none shadow-sm text-xs leading-relaxed border border-espresso/5">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] text-espresso/40 mt-1">${time}</span>
                </div>
            `;
        }

        container.appendChild(wrapper);
    }

    function scrollToBottom() {
        const container = document.getElementById('chat-messages-container');
        container.scrollTop = container.scrollHeight;
    }

    function formatTime(isoString) {
        if (!isoString) return '';
        try {
            const date = new Date(isoString);
            return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return '';
        }
    }

    function addFromChatToCart(identifier, e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (!identifier) return;

        let payload = {
            _token: '{{ csrf_token() }}',
            product_id: identifier,
            slug: identifier,
            quantity: 1
        };

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('🎉 ' + data.message, 'success');
                } else {
                    alert(data.message);
                }
                if (typeof updateCartBadge === 'function') {
                    updateCartBadge(data.cart_count);
                }
            } else {
                if (typeof showToast === 'function') {
                    showToast('Lỗi: ' + data.message, 'error');
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => console.error('Lỗi thêm giỏ hàng từ chat:', err));
    }

    function formatMessageText(text) {
        if (!text) return '';
        let escaped = escapeHTML(text);
        
        // 1. Chuyển đổi Markdown **text** thành <strong>text</strong>
        escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong class="font-bold text-espresso">$1</strong>');
        
        // 2. Chuyển đổi Markdown link [Label](URL) thành Nút Thêm Vào Giỏ Hàng Trực Tiếp
        escaped = escaped.replace(/\[([^\]]+)\]\(([^)]+)\)/g, (match, label, url) => {
            let identifier = null;

            // 1. Tìm hash #add-to-cart-123
            let matchHash = url.match(/#add-to-cart-(\d+)/);
            if (matchHash) {
                identifier = matchHash[1];
            }

            // 2. Tìm slug từ URL /san-pham/slug-xxx
            if (!identifier) {
                let matchSlugPath = url.match(/\/san-pham\/([^#?]+)/);
                if (matchSlugPath) {
                    identifier = matchSlugPath[1];
                }
            }

            // 3. Tìm số ID dạng -24-177417390134 hoặc cuối slug
            if (!identifier) {
                let matchId = url.match(/-(\d+)-\d+$/);
                if (matchId) {
                    identifier = matchId[1];
                }
            }

            if (identifier) {
                return `<button type="button" onclick="addFromChatToCart('${identifier}', event)" class="inline-flex items-center gap-1.5 font-bold text-white bg-coral hover:bg-[#d5523b] active:scale-95 transition-all mt-2 px-3.5 py-1.5 rounded-full shadow-md text-xs cursor-pointer border border-coral/30 hover:shadow-lg"><svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg> ${label}</button>`;
            }

            return `<a href="${url}" target="_blank" class="inline-flex items-center gap-1 font-bold text-coral hover:underline hover:text-[#d5523b] transition-colors mt-1 bg-coral/10 px-2.5 py-1 rounded-full border border-coral/20 shadow-2xs"><svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg> ${label}</a>`;
        });
        
        // 3. Thay thế xuống dòng bằng <br>
        return escaped.replace(/\n/g, '<br>');
    }

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

    document.addEventListener('DOMContentLoaded', () => {
        if (chatToken) {
            fetchMessages();
        }
    });
</script>
