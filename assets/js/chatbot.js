jQuery(document).ready(function($) {
    // Initialize chatbot functionality for all instances on the page
    $('.aliyun-chatbot-container').each(function() {
        var container = $(this);
        var messagesContainer = container.find('.aliyun-chatbot-messages');
        var inputField = container.find('.aliyun-chatbot-input');
        var sendButton = container.find('.aliyun-chatbot-send');
        var clearButton = container.find('.aliyun-chatbot-clear-btn');
        
        // Session ID to maintain conversation context
        var sessionId = '';
        
        // Function to add a new message to the chat
        function addMessage(content, isUser) {
            var messageClass = isUser ? 'user' : 'bot';
            var messageHtml = '<div class="aliyun-chatbot-message ' + messageClass + '" role="listitem">' +
                '<div class="aliyun-chatbot-message-content">' + content + '</div>' +
                '</div>';
            
            messagesContainer.append(messageHtml);
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }
        
        // Function to add thinking process to the chat
        function addThoughts(content) {
            var thoughtsHtml = '<div class="aliyun-chatbot-thoughts" role="note" aria-label="AI thinking process">' + 
                content + 
                '</div>';
                
            messagesContainer.append(thoughtsHtml);
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }
        
        // Function to add a loading indicator
        function addThinkingIndicator() {
            var thinkingHtml = '<div class="aliyun-chatbot-message bot" id="thinking-indicator" role="status" aria-live="polite">' +
                '<div class="aliyun-chatbot-message-content aliyun-chatbot-thinking" aria-label="' + aliyunChatbotData.loading_text + '">' +
                '<span class="dot"></span>' +
                '<span class="dot"></span>' +
                '<span class="dot"></span>' +
                '</div>' +
                '</div>';
            
            messagesContainer.append(thinkingHtml);
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }
        
        // Function to remove the loading indicator
        function removeThinkingIndicator() {
            container.find('#thinking-indicator').remove();
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }
        
        // Function to format message text (adds line breaks, links, etc)
        function formatMessage(text) {
            if (!text) return '';
            
            // Convert line breaks to <br>
            var formatted = escapeHtml(text).replace(/\n/g, '<br>');
            
            // Make URLs clickable
            formatted = formatted.replace(
                /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig,
                '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
            );
            
            return formatted;
        }
        
        // Function to send a message to the API
        function sendMessage() {
            var message = inputField.val().trim();
            
            if (message === '') {
                return;
            }
            
            // Add the user's message to the chat
            addMessage(formatMessage(message), true);
            
            // Clear the input field
            inputField.val('');
            
            // Disable input during processing
            inputField.prop('disabled', true);
            sendButton.prop('disabled', true);
            
            // Show thinking indicator
            addThinkingIndicator();
            
            // Send the message to the server
            $.ajax({
                url: aliyunChatbotData.ajax_url,
                type: 'POST',
                data: {
                    action: 'aliyun_chatbot_request',
                    nonce: aliyunChatbotData.nonce,
                    message: message,
                    session_id: sessionId
                },
                success: function(response) {
                    // Remove thinking indicator
                    removeThinkingIndicator();
                    
                    // Re-enable input
                    inputField.prop('disabled', false);
                    sendButton.prop('disabled', false);
                    inputField.focus();
                    
                    if (response.success) {
                        // Save session ID if provided
                        if (response.data.session_id) {
                            sessionId = response.data.session_id;
                        }
                        
                        // Show thinking process if available
                        if (response.data.has_thoughts && response.data.thoughts) {
                            addThoughts(formatMessage(response.data.thoughts));
                        }
                        
                        // Add the bot's response to the chat
                        addMessage(formatMessage(response.data.message), false);
                    } else {
                        // Show an error message
                        addMessage(aliyunChatbotData.error_text, false);
                        console.error('API error:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    // Remove thinking indicator
                    removeThinkingIndicator();
                    
                    // Re-enable input
                    inputField.prop('disabled', false);
                    sendButton.prop('disabled', false);
                    
                    // Show an error message
                    addMessage(aliyunChatbotData.error_text, false);
                    console.error('AJAX error:', status, error);
                }
            });
        }
        
        // Function to clear the conversation
        function clearConversation() {
            // Clear all messages except the first (welcome message)
            messagesContainer.find('.aliyun-chatbot-message:not(:first-child)').remove();
            messagesContainer.find('.aliyun-chatbot-thoughts').remove();
            messagesContainer.find('#thinking-indicator').remove();
            
            // Reset session ID
            sessionId = '';
            
            // Enable input if it was disabled
            inputField.prop('disabled', false);
            sendButton.prop('disabled', false);
            
            // Focus on input
            inputField.focus();
        }
        
        // Handle send button click
        sendButton.on('click', sendMessage);
        
        // Handle clear button click if present
        if (clearButton.length) {
            clearButton.on('click', clearConversation);
        }
        
        // Handle Enter key press in the input field
        inputField.on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                sendMessage();
            }
        });
    });
});