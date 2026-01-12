<?php
/**
 * API Handler Class for Aliyun DeepSeek ChatBot
 *
 * Handles all API communication with Aliyun DeepSeek service
 * Compatible with both OpenAI-compatible mode and DashScope native mode
 *
 * @since 1.0.0
 * @version 1.1.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Aliyun_Chatbot_API_Handler {

    /**
     * Conversation history storage
     *
     * @var array
     */
    private $conversation_history = array();

    /**
     * Initialize the API handler class
     */
    public function __construct() {
        // Register AJAX handler
        add_action('wp_ajax_aliyun_chatbot_request', array($this, 'handle_ajax_request'));
        add_action('wp_ajax_nopriv_aliyun_chatbot_request', array($this, 'handle_ajax_request'));
    }

    /**
     * Check if this is a streaming (SSE) request.
     *
     * @return bool
     */
    private function is_stream_request() {
        if (empty($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return stripos($_SERVER['HTTP_ACCEPT'], 'text/event-stream') !== false;
    }

    /**
     * Send an error response in JSON or SSE format.
     *
     * @param string $message Error message
     * @param bool $is_stream_request Whether request expects SSE
     * @return void
     */
    private function send_error_response($message, $is_stream_request) {
        if ($is_stream_request) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no');

            echo "data: " . wp_json_encode(array('error' => $message)) . "\n\n";
            flush();
            echo "data: [DONE]\n\n";
            flush();
            exit;
        }

        wp_send_json_error($message);
    }

    /**
     * Get a sanitized session ID from the request.
     *
     * @return string
     */
    private function get_request_session_id() {
        $session_id = isset($_REQUEST['session_id']) ? wp_unslash($_REQUEST['session_id']) : '';
        $session_id = sanitize_text_field($session_id);
        $session_id = preg_replace('/[^A-Za-z0-9_-]/', '', $session_id);

        if (strlen($session_id) > 64) {
            $session_id = substr($session_id, 0, 64);
        }

        return $session_id;
    }

    /**
     * Normalize a hostname value.
     *
     * @param string $host Host input
     * @return string
     */
    private function normalize_host($host) {
        $host = trim($host);
        if ($host === '') {
            return '';
        }

        $host = preg_replace('#^https?://#i', '', $host);
        $host = preg_replace('#/.*$#', '', $host);
        $host = strtolower($host);
        $host = preg_replace('/[^a-z0-9.-]/', '', $host);

        return $host;
    }

    /**
     * Get allowed API hosts.
     *
     * @return array
     */
    private function get_allowed_api_hosts() {
        $default_hosts = array('dashscope.aliyuncs.com');
        $raw_hosts = get_option('aliyun_chatbot_allowed_api_hosts', '');
        $hosts = array();

        if (!empty($raw_hosts)) {
            $parts = preg_split('/\s*,\s*/', $raw_hosts);
            foreach ($parts as $part) {
                $host = $this->normalize_host($part);
                if ($host !== '') {
                    $hosts[] = $host;
                }
            }
        }

        $hosts = array_merge($default_hosts, $hosts);
        $hosts = array_unique($hosts);

        $hosts = apply_filters('aliyun_chatbot_allowed_api_hosts', $hosts);
        if (!is_array($hosts)) {
            $hosts = $default_hosts;
        }

        $normalized = array();
        foreach ($hosts as $host) {
            $host = $this->normalize_host($host);
            if ($host !== '') {
                $normalized[] = $host;
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Check if the API endpoint is allowed.
     *
     * @param string $endpoint API endpoint
     * @return bool
     */
    private function is_allowed_api_endpoint($endpoint) {
        $endpoint = trim($endpoint);
        if (empty($endpoint)) {
            return false;
        }

        $parts = wp_parse_url($endpoint);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);

        if ($scheme !== 'https') {
            return false;
        }

        $allowed_hosts = $this->get_allowed_api_hosts();

        return in_array($host, $allowed_hosts, true);
    }

    /**
     * Get message length using a multibyte-safe function when available.
     *
     * @param string $message Message content
     * @return int
     */
    private function get_message_length($message) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($message);
        }

        return strlen($message);
    }
    
    /**
     * Get conversation messages array
     *
     * @param string $new_message New user message
     * @return array Messages array in OpenAI format
     */
    private function get_conversation_messages($new_message) {
        $messages = array();
        $session_id = $this->get_request_session_id();
        $enable_conversation = get_option('aliyun_chatbot_enable_conversation', 1);

        // Load conversation history from transient
        if ($enable_conversation && !empty($session_id)) {
            $history = get_transient('aliyun_chatbot_history_' . $session_id);
            if ($history && is_array($history)) {
                $messages = $history;
            }
        }

        // Add system message if not present
        if (empty($messages)) {
            $system_message = get_option('aliyun_chatbot_system_message', '');
            if (!empty($system_message)) {
                $messages[] = array(
                    'role' => 'system',
                    'content' => $system_message
                );
            }
        }

        // Add new user message
        $messages[] = array(
            'role' => 'user',
            'content' => $new_message
        );

        // Limit history length
        $max_history = get_option('aliyun_chatbot_history_length', 5);
        $max_messages = ($max_history * 2) + 1; // user + assistant pairs + system message

        if (count($messages) > $max_messages) {
            // Keep system message and trim old messages
            $system_msg = array();
            if ($messages[0]['role'] === 'system') {
                $system_msg[] = array_shift($messages);
            }
            $messages = array_merge($system_msg, array_slice($messages, -($max_messages - 1)));
        }

        return $messages;
    }

    /**
     * Save conversation messages to transient
     *
     * @param string $session_id Session ID
     * @param array $messages Messages array
     * @return void
     */
    private function save_conversation_messages($session_id, $messages) {
        if (empty($session_id)) {
            return;
        }

        // Save for 1 hour
        set_transient('aliyun_chatbot_history_' . $session_id, $messages, HOUR_IN_SECONDS);
    }

    /**
     * Handle AJAX request to the Aliyun DeepSeek API
     *
     * @return void
     */
    public function handle_ajax_request() {
        $is_stream_request = $this->is_stream_request();

        // Verify nonce
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'aliyun_chatbot_nonce')) {
            $this->send_error_response('Invalid security token', $is_stream_request);
        }

        $message = isset($_REQUEST['message']) ? sanitize_textarea_field(wp_unslash($_REQUEST['message'])) : '';

        if (empty($message)) {
            $this->send_error_response('Message is required', $is_stream_request);
        }

        $max_message_length = absint(get_option('aliyun_chatbot_max_message_length', 4000));
        $max_message_length = absint(apply_filters('aliyun_chatbot_max_message_length', $max_message_length));
        if ($max_message_length < 1) {
            $max_message_length = 4000;
        }
        if ($this->get_message_length($message) > $max_message_length) {
            $this->send_error_response('Message is too long', $is_stream_request);
        }

        // Get API configuration
        $api_key = get_option('aliyun_chatbot_api_key', '');
        $model = get_option('aliyun_chatbot_model', 'deepseek-chat');
        $show_thoughts = get_option('aliyun_chatbot_show_thoughts', 0);
        $enable_stream = get_option('aliyun_chatbot_enable_stream', 0);

        if (empty($api_key)) {
            $this->send_error_response('API key is required', $is_stream_request);
        }

        // Use OpenAI-compatible endpoint
        $api_endpoint = get_option('aliyun_chatbot_api_endpoint', '');
        if (empty($api_endpoint)) {
            $api_endpoint = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
        }
        if (!$this->is_allowed_api_endpoint($api_endpoint)) {
            $this->send_error_response('Invalid API endpoint host', $is_stream_request);
        }

        // Build messages array for conversation
        $messages = $this->get_conversation_messages($message);

        // Prepare the request data using OpenAI-compatible format
        $request_data = array(
            'model' => $model,
            'messages' => $messages,
            'stream' => (bool) $enable_stream
        );

        // Add temperature and other parameters
        $temperature = get_option('aliyun_chatbot_temperature', 1.0);
        $max_tokens = get_option('aliyun_chatbot_max_tokens', 4000);

        $request_data['temperature'] = floatval($temperature);
        $request_data['max_tokens'] = intval($max_tokens);

        // Enable reasoning mode if showing thoughts
        if ($show_thoughts) {
            $request_data['reasoner_config'] = array(
                'enable_thinking' => true
            );
        }
        
        // Debug log
        if (WP_DEBUG) {
            error_log('DeepSeek API request to: ' . $api_endpoint);
            error_log('Request meta: ' . wp_json_encode(array(
                'model' => $model,
                'message_count' => count($messages),
                'stream' => (bool) $enable_stream,
                'show_thoughts' => (bool) $show_thoughts,
            )));
        }

        // If streaming is enabled, use SSE streaming
        if ($enable_stream && $is_stream_request) {
            $this->handle_streaming_request($api_endpoint, $api_key, $request_data, $messages);
            return;
        }

        // Non-streaming request
        $response = wp_remote_post(
            $api_endpoint,
            array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body' => wp_json_encode($request_data),
                'timeout' => 60,
            )
        );
        
        // Check if the request was successful
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('DeepSeek API error: ' . $error_message);
            
            // Provide more specific error messages based on error type
            if (strpos($error_message, 'cURL error 28') !== false) {
                $this->send_error_response('API connection timed out. Please check your network connection or try again later.', $is_stream_request);
            } else if (strpos($error_message, 'cURL error 6') !== false) {
                $this->send_error_response('Cannot resolve API hostname. Please check your API endpoint setting.', $is_stream_request);
            } else {
                $this->send_error_response('API request failed: ' . $error_message, $is_stream_request);
            }
            return;
        }
        
        // Check the HTTP response code
        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code !== 200) {
            $error_body = wp_remote_retrieve_body($response);
            $error_data = json_decode($error_body, true);
            
            $error_message = 'Request failed';
            
            // Handle common error status codes
            if ($http_code === 401) {
                $error_message = 'Invalid API key. Please check your settings.';
            } else if ($http_code === 404) {
                $error_message = 'Endpoint not found. Please check your API endpoint setting.';
            } else if ($http_code === 429) {
                $error_message = 'API rate limit exceeded. Please try again later.';
            } else if ($error_data && isset($error_data['error']['message'])) {
                // Try to get error message from API response
                $error_message = $error_data['error']['message'];
            }
            
            error_log('DeepSeek API HTTP error: ' . $http_code . ' - ' . $error_message);
            error_log('Error response: ' . $error_body);
            
            $this->send_error_response($error_message, $is_stream_request);
            return;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid JSON response: ' . json_last_error_msg();
            error_log('DeepSeek API JSON decode error: ' . json_last_error_msg());
            $this->send_error_response($error_message, $is_stream_request);
            return;
        }

        // Debug log
        if (WP_DEBUG) {
            error_log('DeepSeek API response meta: ' . wp_json_encode(array(
                'id' => isset($data['id']) ? $data['id'] : '',
                'choices' => isset($data['choices']) ? count($data['choices']) : 0,
                'usage' => isset($data['usage']) ? $data['usage'] : array(),
            )));
        }

        // Process OpenAI-compatible response
        if (!isset($data['choices']) || !is_array($data['choices']) || empty($data['choices'])) {
            error_log('Unexpected DeepSeek API response format: ' . wp_json_encode($data));
            $this->send_error_response('The AI service returned an unexpected response format.', $is_stream_request);
            return;
        }

        $choice = $data['choices'][0];
        $assistant_message = isset($choice['message']) ? $choice['message'] : array();

        // Prepare response data
        $response_data = array(
            'message' => '',
            'has_thoughts' => false,
            'thoughts' => '',
            'session_id' => ''
        );

        // Extract content
        if (isset($assistant_message['content'])) {
            $response_data['message'] = $assistant_message['content'];
        }

        // Extract reasoning content if available
        if ($show_thoughts && isset($assistant_message['reasoning_content'])) {
            $response_data['has_thoughts'] = true;
            $response_data['thoughts'] = $assistant_message['reasoning_content'];
        }

        // Generate session ID if conversation is enabled
        $session_id = $this->get_request_session_id();
        if (empty($session_id)) {
            $session_id = 'session_' . uniqid();
        }
        $response_data['session_id'] = $session_id;

        // Save conversation history
        $messages[] = array(
            'role' => 'assistant',
            'content' => $response_data['message']
        );
        $this->save_conversation_messages($session_id, $messages);

        wp_send_json_success($response_data);
    }

    /**
     * Handle streaming request with SSE
     *
     * @param string $endpoint API endpoint URL
     * @param string $api_key API key
     * @param array $request_data Request data
     * @param array $messages Conversation messages
     * @return void
     */
    private function handle_streaming_request($endpoint, $api_key, $request_data, $messages) {
        // Set headers for SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        // Disable default PHP buffering
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Use cURL for streaming response
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($request_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
            'Accept: text/event-stream'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        // Variables to accumulate response
        $buffer = '';
        $full_content = '';
        $full_reasoning = '';
        $session_id = $this->get_request_session_id();

        if (empty($session_id)) {
            $session_id = 'session_' . uniqid();
        }

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) use (&$buffer, &$full_content, &$full_reasoning, $session_id) {
            $buffer .= $data;
            $lines = explode("\n", $buffer);

            // Keep the last incomplete line in buffer
            $buffer = array_pop($lines);

            foreach ($lines as $line) {
                $line = trim($line);

                // Skip empty lines and comments
                if (empty($line) || strpos($line, ':') === 0) {
                    continue;
                }

                // Parse SSE data
                if (strpos($line, 'data:') === 0) {
                    $json_str = trim(substr($line, 5));

                    // Check for [DONE] marker
                    if ($json_str === '[DONE]') {
                        continue;
                    }

                    $chunk_data = json_decode($json_str, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($chunk_data['choices'])) {
                        $choice = $chunk_data['choices'][0];
                        $delta = isset($choice['delta']) ? $choice['delta'] : array();

                        $event_data = array();

                        // Extract incremental content
                        if (isset($delta['content'])) {
                            $full_content .= $delta['content'];
                            $event_data['text'] = $full_content;
                        }

                        // Extract incremental reasoning content
                        if (isset($delta['reasoning_content'])) {
                            $full_reasoning .= $delta['reasoning_content'];
                            $event_data['thoughts'] = $full_reasoning;
                        }

                        // Add session ID
                        $event_data['session_id'] = $session_id;

                        // Send SSE event
                        if (!empty($event_data)) {
                            echo "data: " . wp_json_encode($event_data) . "\n\n";
                            flush();
                        }
                    }
                }
            }

            return strlen($data);
        });

        curl_exec($ch);

        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Save conversation history
        if (!empty($full_content)) {
            $messages[] = array(
                'role' => 'assistant',
                'content' => $full_content
            );
            $this->save_conversation_messages($session_id, $messages);
        }

        // Handle errors
        if (!empty($curl_error)) {
            $error_data = array('error' => $curl_error);
            echo "data: " . wp_json_encode($error_data) . "\n\n";
            flush();
        } elseif ($http_code !== 200) {
            $error_data = array('error' => 'HTTP ' . $http_code);
            echo "data: " . wp_json_encode($error_data) . "\n\n";
            flush();
        }

        // Send final done message
        echo "data: [DONE]\n\n";
        flush();

        exit;
    }
}
