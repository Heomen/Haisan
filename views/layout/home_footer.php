<?php
echo <<<HTML
    <!-- Menu nổi -->
    <div class="floating-actions">
        <a href="https://zalo.me/0921427637" target="_blank" class="floating-btn btn-zalo">
            <img src="images/zalo.jpg" alt="Zalo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
        </a>
    </div>

    <!-- AI Chatbot UI -->
    <div id="ai-chat-window" class="ai-chat-window">
        <div class="ai-chat-header">
            <div class="d-flex align-items-center">
                <div class="ai-avatar"><i class="fa-solid fa-robot"></i></div>
                <div class="ms-2">
                    <h6 class="mb-0 fw-bold">SeaFood AI</h6>
                    <small style="font-size: 11px; opacity: 0.8;">Luôn sẵn sàng hỗ trợ</small>
                </div>
            </div>
            <button class="btn-close-chat" onclick="toggleChat()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="ai-chat-body" class="ai-chat-body">
            <div class="chat-msg ai-msg">
                Xin chào! Tôi là Trợ lý ảo của SeaFood. Bạn cần tôi tư vấn về Thực đơn, Giá cả, Khuyến mãi hay cách Đặt bàn?
            </div>
        </div>
        <div class="ai-chat-input-area">
            <input type="text" id="ai-chat-input" placeholder="Nhập câu hỏi của bạn..." onkeypress="if(event.key === 'Enter') sendChatMessage()">
            <button class="btn-send-chat" onclick="sendChatMessage()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <style>
        .ai-chat-window {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 360px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            z-index: 9999;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            animation: slideUpChat 0.3s ease-out;
        }
        @keyframes slideUpChat {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .ai-chat-header {
            background: linear-gradient(135deg, #0073C2, #00A3FF);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ai-avatar {
            width: 35px; height: 35px; background: white; color: #0073C2;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .btn-close-chat {
            background: transparent; border: none; color: white; font-size: 18px; cursor: pointer;
        }
        .ai-chat-body {
            height: 380px;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .chat-msg {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 14.5px;
            line-height: 1.5;
            word-wrap: break-word;
        }
        .ai-msg {
            background: white;
            color: #334155;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .user-msg {
            background: #0073C2;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,115,194,0.2);
        }
        .ai-chat-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
        }
        #ai-chat-input {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 30px;
            padding: 10px 15px;
            outline: none;
            font-size: 14px;
        }
        #ai-chat-input:focus { border-color: #0073C2; }
        .btn-send-chat {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #0073C2;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-send-chat:hover { background: #005fa3; }
        .typing-indicator { font-size: 13px; color: #94a3b8; font-style: italic; }
    </style>

    <script>
        function toggleChat() {
            const chatWindow = document.getElementById('ai-chat-window');
            if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                chatWindow.style.display = 'flex';
                document.getElementById('ai-chat-input').focus();
            } else {
                chatWindow.style.display = 'none';
            }
        }

        async function sendChatMessage() {
            const input = document.getElementById('ai-chat-input');
            const message = input.value.trim();
            if (!message) return;

            const chatBody = document.getElementById('ai-chat-body');
            
            // Add user message
            chatBody.innerHTML += `<div class="chat-msg user-msg">\${message}</div>`;
            input.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Add typing indicator
            const typingId = 'typing-' + Date.now();
            chatBody.innerHTML += `<div id="\${typingId}" class="chat-msg ai-msg typing-indicator"><i class="fa-solid fa-spinner fa-spin"></i> SeaFood AI đang trả lời...</div>`;
            chatBody.scrollTop = chatBody.scrollHeight;

            try {
                // Send AJAX request
                const response = await fetch('index.php?controller=home&action=ai_chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'message=' + encodeURIComponent(message)
                });
                const data = await response.json();
                
                // Remove typing indicator and show response
                document.getElementById(typingId).remove();
                chatBody.innerHTML += `<div class="chat-msg ai-msg">\${data.reply}</div>`;
                chatBody.scrollTop = chatBody.scrollHeight;
            } catch (error) {
                document.getElementById(typingId).remove();
                chatBody.innerHTML += `<div class="chat-msg ai-msg text-danger">Xin lỗi, kết nối bị lỗi. Vui lòng thử lại.</div>`;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
?>
