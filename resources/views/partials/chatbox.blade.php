{{-- Nút bong bóng chat nổi --}}
<button id="chat-bubble" onclick="toggleChatWindow()" class="fixed bottom-6 right-6 z-[999] w-14 h-14 rounded-full flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300 group cursor-pointer" style="background: linear-gradient(135deg, #e8634a 0%, #c84932 100%) !important; color: #ffffff !important; box-shadow: 0 10px 35px rgba(232, 99, 74, 0.6) !important;">
    <span class="absolute -top-1 -right-1 text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden shadow-sm animate-bounce" id="chat-badge" style="background-color: #dc2626 !important; color: #ffffff !important;">0</span>
    <svg class="w-7 h-7 transform group-hover:rotate-12 transition-transform" fill="none" stroke="#ffffff" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
</button>

{{-- Khung cửa sổ chat --}}
<div id="chat-window" class="fixed bottom-24 right-6 z-[999] w-[360px] h-[520px] rounded-[28px] flex flex-col overflow-hidden translate-y-10 opacity-0 pointer-events-none transition-all duration-300 ease-out" style="background-color: #ffffff !important; border: 1px solid rgba(43, 38, 35, 0.15) !important; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25) !important;">
    
    {{-- Header --}}
    <div class="p-4 flex items-center justify-between shrink-0" style="background-color: #2B2623 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg" style="background-color: #FFF0D4 !important;">
                    ☕
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full animate-pulse" style="background-color: #34d399 !important; border: 2px solid #2B2623 !important;"></div>
            </div>
            <div>
                <h4 class="font-bold text-sm tracking-wide" style="color: #ffffff !important;">Chill Chill Support</h4>
                <p class="text-[10px] flex items-center gap-1" style="color: #fef08a !important;">
                    <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #34d399 !important;"></span> Đang hoạt động • Trợ lý AI
                </p>
            </div>
        </div>
        <button onclick="toggleChatWindow()" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors cursor-pointer" style="background-color: rgba(255,255,255,0.15) !important; color: #ffffff !important;">
            <svg class="w-4 h-4" fill="none" stroke="#ffffff" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Khung hiển thị tin nhắn --}}
    <div id="chat-messages-container" class="flex-1 p-4 overflow-y-auto space-y-3 flex flex-col custom-scrollbar" style="background-color: #FAF7F2 !important;">
        {{-- Tin nhắn mặc định chào mừng --}}
        <div class="flex items-start gap-2 max-w-[85%]">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm" style="background-color: #FFF0D4 !important; border: 1px solid #fde68a !important;">
                ☕
            </div>
            <div id="chat-welcome-msg" class="p-3.5 rounded-[20px] rounded-tl-xs shadow-sm text-xs leading-relaxed font-medium" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #f3f4f6 !important;">
                Chào bạn! Chill Chill có thể giúp gì cho bạn hôm nay? Hãy gửi lời nhắn nhé!
            </div>
        </div>
    </div>

    {{-- Thanh Gợi Ý Nhanh --}}
    <div class="px-3 py-1.5 flex items-center gap-1.5 overflow-x-auto whitespace-nowrap custom-scrollbar shrink-0" style="background-color: #FAF7F2 !important; border-top: 1px solid #f3f4f6 !important;">
        <button type="button" onclick="sendQuickPrompt('Cà phê')" class="px-2.5 py-1 text-[11px] font-medium rounded-full transition-all shadow-2xs cursor-pointer" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #e5e7eb !important;">☕ Cà phê</button>
        <button type="button" onclick="sendQuickPrompt('Trà trái cây')" class="px-2.5 py-1 text-[11px] font-medium rounded-full transition-all shadow-2xs cursor-pointer" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #e5e7eb !important;">🍹 Trà trái cây</button>
        <button type="button" onclick="sendQuickPrompt('Bánh ngọt')" class="px-2.5 py-1 text-[11px] font-medium rounded-full transition-all shadow-2xs cursor-pointer" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #e5e7eb !important;">🍰 Bánh ngọt</button>
        <button type="button" onclick="sendQuickPrompt('Xem menu')" class="px-2.5 py-1 text-[11px] font-medium rounded-full transition-all shadow-2xs cursor-pointer" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #e5e7eb !important;">📋 Menu</button>
        <button type="button" onclick="sendQuickPrompt('Giao hàng')" class="px-2.5 py-1 text-[11px] font-medium rounded-full transition-all shadow-2xs cursor-pointer" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #e5e7eb !important;">🛵 Ship hàng</button>
    </div>

    {{-- Khung nhập tin nhắn --}}
    <form id="chat-form" onsubmit="handleSend(event)" class="p-3 flex items-center gap-2 shrink-0" style="background-color: #ffffff !important; border-top: 1px solid #f3f4f6 !important;">
        <input type="text" id="chat-input" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" class="flex-1 px-4 py-2.5 rounded-full text-xs focus:outline-none transition-all" style="background-color: #FAF7F2 !important; color: #2B2623 !important; border: 1px solid #e5e7eb !important;">
        
        <button type="submit" class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 hover:scale-105 active:scale-95 transition-all cursor-pointer" style="background-color: #e8634a !important; color: #ffffff !important; box-shadow: 0 4px 15px rgba(232, 99, 74, 0.4) !important;">
            <svg class="w-4 h-4 transform rotate-45 -translate-x-0.5" fill="none" stroke="#ffffff" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </form>
</div>

<script>
    let chatOpen = false;
    let chatToken = localStorage.getItem('chill_chat_token');
    let chatPollInterval = null;

    function sendQuickPrompt(promptText) {
        const input = document.getElementById('chat-input');
        input.value = promptText;
        document.getElementById('chat-form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
    }

    async function initChatSession() {
        try {
            const response = await fetch('/chat/start', {
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
                
                if (data.messages && data.messages.length > 0) {
                    const container = document.getElementById('chat-messages-container');
                    container.innerHTML = `
                        <div class="flex items-start gap-2 max-w-[85%]">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm" style="background-color: #FFF0D4 !important; border: 1px solid #fde68a !important;">
                                ☕
                            </div>
                            <div id="chat-welcome-msg" class="p-3.5 rounded-[20px] rounded-tl-xs shadow-sm text-xs leading-relaxed font-medium" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #f3f4f6 !important;">
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

    function toggleChatWindow() {
        const windowEl = document.getElementById('chat-window');
        chatOpen = !chatOpen;

        if (chatOpen) {
            chatToken = null;
            localStorage.removeItem('chill_chat_token');

            const container = document.getElementById('chat-messages-container');
            container.innerHTML = `
                <div class="flex items-start gap-2 max-w-[85%]">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm" style="background-color: #FFF0D4 !important; border: 1px solid #fde68a !important;">
                        ☕
                    </div>
                    <div id="chat-welcome-msg" class="p-3.5 rounded-[20px] rounded-tl-xs shadow-sm text-xs leading-relaxed font-medium" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #f3f4f6 !important;">
                        ${getRealTimeGreeting()}
                    </div>
                </div>
            `;

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

    async function fetchMessages() {
        if (!chatToken) return;
        try {
            const response = await fetch(`/chat/messages?session_token=${chatToken}`);
            const data = await response.json();
            if (data.success) {
                const container = document.getElementById('chat-messages-container');
                const prevCount = container.querySelectorAll('.chat-msg-item').length;
                
                if (data.messages.length > prevCount) {
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

                if (data.cart_updated) {
                    updateHeaderCartBadge(data.cart_count);
                    if (window.location.pathname.startsWith('/cart') || window.location.pathname.startsWith('/checkout')) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }

                if (data.order_created) {
                    const noticeText = data.payment_method === 'qr'
                        ? 'Đặt đơn hàng thành công! Đang tự động chuyển hướng bạn tới trang quét mã QR thanh toán...'
                        : 'Đặt đơn hàng thành công! Đang tự động chuyển hướng bạn tới trang danh sách đơn hàng...';

                    appendMessageToDOM('admin', `✨ ${noticeText}`, new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }));
                    scrollToBottom();

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

    function showTypingIndicator() {
        if (document.getElementById('chat-typing-indicator')) return;

        const container = document.getElementById('chat-messages-container');
        const wrapper = document.createElement('div');
        wrapper.id = 'chat-typing-indicator';
        wrapper.className = 'flex items-start gap-2';
        wrapper.innerHTML = `
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 animate-pulse" style="background-color: #FFF0D4 !important; border: 1px solid #fde68a !important;">
                ☕
            </div>
            <div class="flex flex-col items-start max-w-[85%]">
                <div class="px-4 py-2.5 rounded-[20px] rounded-tl-xs shadow-sm flex items-center gap-1.5 h-8" style="background-color: #ffffff !important; border: 1px solid #f3f4f6 !important;">
                    <span class="w-1.5 h-1.5 rounded-full animate-bounce" style="background-color: #e8634a !important; animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full animate-bounce" style="background-color: #e8634a !important; animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full animate-bounce" style="background-color: #e8634a !important; animation-delay: 300ms"></span>
                </div>
                <span class="text-[9px] mt-1 ml-1 font-medium" style="color: rgba(43, 38, 35, 0.4) !important;">Chill Chill Support đang tìm câu trả lời...</span>
            </div>
        `;

        container.appendChild(wrapper);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('chat-typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    async function handleSend(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        const tempTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        appendMessageToDOM('customer', text, tempTime);
        scrollToBottom();

        showTypingIndicator();

        try {
            const response = await fetch('/chat/send', {
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
                if (!chatToken) {
                    chatToken = data.message.chat_session_id;
                }
                await fetchMessages();
            }
        } catch (error) {
            console.error('Lỗi gửi tin nhắn:', error);
        } finally {
            hideTypingIndicator();
        }
    }

    function appendMessageToDOM(sender, message, time) {
        const container = document.getElementById('chat-messages-container');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-2 chat-msg-item ' + (sender === 'customer' ? 'justify-end' : '');

        if (sender === 'customer') {
            wrapper.innerHTML = `
                <div class="flex flex-col items-end max-w-[85%]">
                    <div class="p-3.5 rounded-[20px] rounded-tr-xs shadow-sm text-xs leading-relaxed font-medium" style="background-color: #e8634a !important; color: #ffffff !important;">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] mt-1 mr-1" style="color: rgba(43, 38, 35, 0.4) !important;">${time}</span>
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm" style="background-color: #FFF0D4 !important; border: 1px solid #fde68a !important;">
                    ☕
                </div>
                <div class="flex flex-col items-start max-w-[85%]">
                    <div class="p-3.5 rounded-[20px] rounded-tl-xs shadow-sm text-xs leading-relaxed font-medium" style="background-color: #ffffff !important; color: #2B2623 !important; border: 1px solid #f3f4f6 !important;">
                        ${formatMessageText(message)}
                    </div>
                    <span class="text-[9px] mt-1 ml-1" style="color: rgba(43, 38, 35, 0.4) !important;">${time}</span>
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

    async function chatQuickAddToCart(productId, variantId, qty, btnElement) {
        if (!productId) return;
        
        const originalHTML = btnElement ? btnElement.innerHTML : '';
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = `⏳ Đang thêm...`;
            btnElement.style.opacity = '0.7';
        }

        try {
            const response = await fetch('/chat/add-to-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: qty
                })
            });

            const data = await response.json();
            if (data.success) {
                if (btnElement) {
                    btnElement.innerHTML = `✅ Đã thêm vào giỏ!`;
                    btnElement.style.background = '#10b981 !important';
                    btnElement.style.opacity = '1';
                }

                updateHeaderCartBadge(data.cart_count);
                
                const timeStr = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                appendMessageToDOM('admin', `✅ ${data.message} [🛍️ Xem giỏ hàng](/cart) | [💳 Thanh toán ngay](/checkout)`, timeStr);
                scrollToBottom();
            } else {
                alert(data.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalHTML;
                    btnElement.style.opacity = '1';
                }
            }
        } catch (error) {
            console.error('Lỗi thêm giỏ hàng từ chat:', error);
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = originalHTML;
                btnElement.style.opacity = '1';
            }
        }
    }

    async function chatQuickAddCombo(itemsParam, btnElement) {
        if (!itemsParam) return;

        const originalHTML = btnElement ? btnElement.innerHTML : '';
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = `⏳ Đang thêm Combo...`;
            btnElement.style.opacity = '0.7';
        }

        try {
            const response = await fetch('/chat/add-combo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: itemsParam })
            });

            const data = await response.json();
            if (data.success) {
                if (btnElement) {
                    btnElement.innerHTML = `✅ Đã thêm cả Combo!`;
                    btnElement.style.background = '#10b981 !important';
                    btnElement.style.opacity = '1';
                }
                updateHeaderCartBadge(data.cart_count);
                const timeStr = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                appendMessageToDOM('admin', `✅ ${data.message} [🛍️ Xem giỏ hàng](/cart) | [💳 Thanh toán ngay](/checkout)`, timeStr);
                scrollToBottom();
            } else {
                alert(data.message || 'Không thể thêm Combo vào giỏ hàng.');
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalHTML;
                    btnElement.style.opacity = '1';
                }
            }
        } catch (error) {
            console.error('Lỗi thêm Combo vào giỏ hàng:', error);
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = originalHTML;
                btnElement.style.opacity = '1';
            }
        }
    }

    function formatMessageText(text) {
        if (!text) return '';
        let escaped = escapeHTML(text);
        
        // Match Markdown Bold **text**
        escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\n/g, '<br>');

        // Match Action Button: [Label](action:add_to_cart?product_id=X&variant_id=Y&qty=Z)
        escaped = escaped.replace(/\[([^\]]+)\]\((action:add_to_cart[^\s)]*)\)/gi, (match, label, actionUrl) => {
            const urlClean = actionUrl.replace(/&amp;/g, '&');
            const params = new URLSearchParams(urlClean.replace('action:add_to_cart?', ''));
            const pId = params.get('product_id') || '';
            const vId = params.get('variant_id') || '';
            const qty = params.get('qty') || '1';
            
            return `<button type="button" onclick="chatQuickAddToCart('${pId}', '${vId}', '${qty}', this)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[11px] font-bold transition-all shadow-xs cursor-pointer hover:scale-105 active:scale-95 my-1" style="background: linear-gradient(135deg, #e8634a 0%, #c84932 100%) !important; color: #ffffff !important;">${label}</button>`;
        });

        // Match Combo Button: [Label](action:add_combo?items=pid:vid,pid:vid,...)
        escaped = escaped.replace(/\[([^\]]+)\]\((action:add_combo[^\s)]*)\)/gi, (match, label, actionUrl) => {
            const urlClean = actionUrl.replace(/&amp;/g, '&');
            const params = new URLSearchParams(urlClean.replace('action:add_combo?', ''));
            const items = params.get('items') || '';
            return `<button type="button" onclick="chatQuickAddCombo('${items}', this)" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[12px] font-bold transition-all shadow cursor-pointer hover:scale-105 active:scale-95 my-2" style="background: linear-gradient(135deg, #2B2623 0%, #e8634a 100%) !important; color: #ffffff !important; box-shadow: 0 4px 15px rgba(232,99,74,0.4) !important;">${label}</button>`;
        });

        // Match normal Markdown links: [Label](URL)
        return escaped.replace(/\[([^\]]+)\]\(((?:https?:\/\/|\/)[^\s)]+)\)/g, (match, label, url) => {
            return `<a href="${url}" target="_blank" class="underline font-bold transition-colors" style="color: #e8634a !important;">${label}</a>`;
        });
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

    document.addEventListener('DOMContentLoaded', () => {
        const welcomeEl = document.getElementById('chat-welcome-msg');
        if (welcomeEl) {
            welcomeEl.innerText = getRealTimeGreeting();
        }
        if (chatToken) {
            fetchMessages();
        }
    });
</script>
