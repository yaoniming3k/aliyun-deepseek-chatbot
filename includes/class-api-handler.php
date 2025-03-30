<?php
/**
 * API Handler Class for Aliyun DeepSeek ChatBot
 *
 * Handles all API communication with Aliyun DeepSeek service
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Aliyun_Chatbot_API_Handler {

    /**
     * Initialize the API handler class
     */
    public function __construct() {
        // Register AJAX handler
        add_action('wp_ajax_aliyun_chatbot_request', array($this, 'handle_ajax_request'));
        add_action('wp_ajax_nopriv_aliyun_chatbot_request', array($this, 'handle_ajax_request'));
    }
    
    /**
     * Get API version from endpoint URL
     * This is a helper function to determine API compatibility
     * 
     * @param string $endpoint The API endpoint URL
     * @return string The detected API version or '1.0' as default
     */
    protected function get_api_version($endpoint) {
        // Extract version from URL if present (e.g., /api/v1/ would return 1.0)
        if (preg_match('/\/v(\d+(\.\d+)?)\//', $endpoint, $matches)) {
            return $matches[1];
        }
        
        // Default to version 1.0 if no version detected
        return '1.0';
    }

    /**
     * Handle AJAX request to the Aliyun DeepSeek API
     *
     * @return void
     */
    public function handle_ajax_request() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aliyun_chatbot_nonce')) {
            wp_send_json_error('Invalid security token');
        }
        
        $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
        
        if (empty($message)) {
            wp_send_json_error('Message is required');
        }
        
        $api_key = get_option('aliyun_chatbot_api_key', '');
        $api_endpoint = get_option('aliyun_chatbot_api_endpoint', 'https://dashscope.aliyuncs.com/api/v1/apps');
        $app_id = get_option('aliyun_chatbot_app_id', '');
        $show_thoughts = get_option('aliyun_chatbot_show_thoughts', 0);
        
        if (empty($api_key) || empty($api_endpoint) || empty($app_id)) {
            wp_send_json_error('API configuration is incomplete');
        }
        
        // Make sure the API endpoint doesn't have a trailing slash before adding the app_id
        $api_endpoint = rtrim($api_endpoint, '/');
        // Build the full endpoint URL with the App ID
        $full_endpoint = $api_endpoint . '/' . $app_id . '/completion';
        
        // Prepare the request data according to the DeepSeek API format
        $request_data = array(
            'input' => array(
                'prompt' => $message
            ),
            'parameters' => array(
                'has_thoughts' => (bool) $show_thoughts
            )
        );
        
        // Add conversation history if enabled
        $enable_conversation = get_option('aliyun_chatbot_enable_conversation', 1);
        if ($enable_conversation && isset($_POST['session_id'])) {
            $session_id = sanitize_text_field($_POST['session_id']);
            if (!empty($session_id)) {
                $request_data['parameters']['session_id'] = $session_id;
                
                // Only set history_length if using session (conversation mode)
                // Some API versions might not support this parameter, so we check API compatibility
                $api_version = $this->get_api_version($api_endpoint);
                if ($api_version >= '1.0') {  // Assuming version 1.0+ supports history_length
                    $history_length = get_option('aliyun_chatbot_history_length', 5);
                    $request_data['parameters']['history_length'] = intval($history_length);
                }
            }
        }
        
        // Debug log
        if (WP_DEBUG) {
            error_log('DeepSeek API request to: ' . $full_endpoint);
            error_log('Request data: ' . wp_json_encode($request_data));
        }
        
        $response = wp_remote_post(
            $full_endpoint,
            array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body' => json_encode($request_data),
                'timeout' => 60,
            )
        );
        
        // Check if the request was successful
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('DeepSeek API error: ' . $error_message);
            
            // Provide more specific error messages based on error type
            if (strpos($error_message, 'cURL error 28') !== false) {
                wp_send_json_error('API connection timed out. Please check your network connection or try again later.');
            } else if (strpos($error_message, 'cURL error 6') !== false) {
                wp_send_json_error('Cannot resolve API hostname. Please check your API endpoint setting.');
            } else {
                wp_send_json_error('API request failed: ' . $error_message);
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
                $error_message = 'App ID not found. Please check your App ID setting.';
            } else if ($http_code === 429) {
                $error_message = 'API rate limit exceeded. Please try again later.';
            } else if ($error_data && isset($error_data['error']['message'])) {
                // Try to get error message from API response
                $error_message = $error_data['error']['message'];
            }
            
            error_log('DeepSeek API HTTP error: ' . $http_code . ' - ' . $error_message);
            error_log('Error response: ' . $error_body);
            
            wp_send_json_error($error_message);
            return;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid JSON response: ' . json_last_error_msg();
            error_log('DeepSeek API JSON decode error: ' . json_last_error_msg());
            wp_send_json_error($error_message);
            return;
        }
        
        // Debug log
        if (WP_DEBUG) {
            error_log('DeepSeek API response: ' . wp_json_encode($data));
        }
        
        // Prepare response data
        $response_data = array(
            'message' => '',
            'has_thoughts' => false,
            'thoughts' => '',
            'session_id' => ''
        );
        
        // Process API response
        if (isset($data['output'])) {
            // Extract reply text
            if (isset($data['output']['text'])) {
                $response_data['message'] = $data['output']['text'];
            }
            
            // Extract session ID
            if (isset($data['output']['session_id'])) {
                $response_data['session_id'] = $data['output']['session_id'];
            }
            
            // Extract thinking process
            if ($show_thoughts && isset($data['output']['thoughts']) && !empty($data['output']['thoughts'])) {
                $response_data['has_thoughts'] = true;
                
                // Extract thoughts from the response
                $thoughts_content = '';
                foreach ($data['output']['thoughts'] as $thought) {
                    if (isset($thought['thought'])) {
                        $thoughts_content .= $thought['thought'] . "\n\n";
                    }
                }
                
                $response_data['thoughts'] = trim($thoughts_content);
            }
            
            wp_send_json_success($response_data);
            return;
        } else {
            // Log unexpected response format
            error_log('Unexpected DeepSeek API response format: ' . wp_json_encode($data));
            wp_send_json_error('The AI service returned an unexpected response format. Please try again later.');
            return;
        }
    }
}
