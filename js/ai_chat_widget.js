// AI Chat Widget JavaScript
class AIChat {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.currentCourseId = null;
        this.init();
    }

    init() {
        this.createWidget();
        this.attachEventListeners();
    }

    createWidget() {
        const widget = document.createElement('div');
        widget.className = 'ai-chat-widget';
        widget.innerHTML = `
            <!-- Chat Button -->
            <button class="ai-chat-button pulse" id="aiChatButton">
                <i class="fas fa-robot"></i>
            </button>

            <!-- Chat Panel -->
            <div class="ai-chat-panel" id="aiChatPanel">
                <div class="ai-chat-header">
                    <h3><i class="fas fa-robot"></i> AI Study Assistant</h3>
                    <button class="ai-chat-close" id="aiChatClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="ai-chat-messages" id="aiChatMessages">
                    <div class="ai-message assistant">
                        <div class="ai-avatar">🤖</div>
                        <div>
                            <div class="ai-bubble">
                                Hi! I'm your AI study assistant. I can help you with:
                                <br>• Explaining complex topics
                                <br>• Quiz hints
                                <br>• Practice questions
                                <br>• Study tips
                                <br><br>What would you like to learn today?
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ai-chat-input">
                    <div class="ai-quick-buttons">
                        <button class="ai-quick-btn" onclick="aiChat.sendQuickMessage('Explain this topic')">
                            💡 Explain
                        </button>
                        <button class="ai-quick-btn" onclick="aiChat.sendQuickMessage('Give me a hint')">
                            🔍 Hint
                        </button>
                        <button class="ai-quick-btn" onclick="aiChat.sendQuickMessage('Create practice questions')">
                            📝 Practice
                        </button>
                        <button class="ai-quick-btn" onclick="aiChat.sendQuickMessage('Study tips')">
                            🎯 Tips
                        </button>
                    </div>
                    <div class="ai-input-box">
                        <input 
                            type="text" 
                            id="aiChatInput" 
                            placeholder="Ask me anything..."
                            autocomplete="off"
                        >
                        <button class="ai-send-btn" id="aiSendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(widget);
    }

    attachEventListeners() {
        document.getElementById('aiChatButton').addEventListener('click', () => this.toggle());
        document.getElementById('aiChatClose').addEventListener('click', () => this.close());
        document.getElementById('aiSendBtn').addEventListener('click', () => this.sendMessage());
        
        const input = document.getElementById('aiChatInput');
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }

    toggle() {
        this.isOpen = !this.isOpen;
        const panel = document.getElementById('aiChatPanel');
        const button = document.getElementById('aiChatButton');
        
        if (this.isOpen) {
            panel.classList.add('active');
            button.classList.remove('pulse');
            document.getElementById('aiChatInput').focus();
        } else {
            panel.classList.remove('active');
        }
    }

    close() {
        this.isOpen = false;
        document.getElementById('aiChatPanel').classList.remove('active');
    }

    async sendMessage() {
        const input = document.getElementById('aiChatInput');
        const message = input.value.trim();
        
        if (!message) return;

        // Disable input while processing
        input.disabled = true;
        document.getElementById('aiSendBtn').disabled = true;

        // Add user message to UI
        this.addMessage(message, 'user');
        input.value = '';

        // Show typing indicator
        this.showTyping();

        try {
            const response = await fetch('backend/ai/ai_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({
                    message: message,
                    course_id: this.currentCourseId,
                    type: this.detectMessageType(message)
                })
            });

            const data = await response.json();

            // Remove typing indicator
            this.removeTyping();

            if (data.status === 'success') {
                this.addMessage(data.response, 'assistant');
            } else {
                this.addMessage('Sorry, I encountered an error: ' + data.message, 'assistant');
            }

        } catch (error) {
            console.error('Chat error:', error);
            this.removeTyping();
            this.addMessage('Sorry, I\'m having trouble connecting. Please try again.', 'assistant');
        } finally {
            input.disabled = false;
            document.getElementById('aiSendBtn').disabled = false;
            input.focus();
        }
    }

    sendQuickMessage(message) {
        const input = document.getElementById('aiChatInput');
        input.value = message;
        this.sendMessage();
    }

    detectMessageType(message) {
        const lower = message.toLowerCase();
        if (lower.includes('hint') || lower.includes('clue')) return 'hint';
        if (lower.includes('explain') || lower.includes('what is') || lower.includes('how does')) return 'explanation';
        if (lower.includes('practice') || lower.includes('question') || lower.includes('quiz')) return 'practice';
        return 'question';
    }

    addMessage(text, sender) {
        const messagesDiv = document.getElementById('aiChatMessages');
        const messageEl = document.createElement('div');
        messageEl.className = `ai-message ${sender}`;
        
        const avatar = sender === 'assistant' ? '🤖' : '👤';
        const now = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        messageEl.innerHTML = `
            <div class="ai-avatar">${avatar}</div>
            <div>
                <div class="ai-bubble">${this.formatMessage(text)}</div>
                <div class="ai-timestamp">${now}</div>
            </div>
        `;
        
        messagesDiv.appendChild(messageEl);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    formatMessage(text) {
        // Convert markdown-style formatting to HTML
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    showTyping() {
        const messagesDiv = document.getElementById('aiChatMessages');
        const typingEl = document.createElement('div');
        typingEl.className = 'ai-message assistant';
        typingEl.id = 'typingIndicator';
        typingEl.innerHTML = `
            <div class="ai-avatar">🤖</div>
            <div class="ai-typing">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        messagesDiv.appendChild(typingEl);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    removeTyping() {
        const typingEl = document.getElementById('typingIndicator');
        if (typingEl) typingEl.remove();
    }

    setCourseContext(courseId) {
        this.currentCourseId = courseId;
    }
}

// Initialize AI Chat when DOM is ready
let aiChat;
document.addEventListener('DOMContentLoaded', () => {
    aiChat = new AIChat();
    
    // Auto-detect course ID if on course page
    const urlParams = new URLSearchParams(window.location.search);
    const courseId = urlParams.get('course_id');
    if (courseId) {
        aiChat.setCourseContext(courseId);
    }
});