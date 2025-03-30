<?php
/**
 * Frontend Class for Aliyun DeepSeek ChatBot
 *
 * Handles all frontend functionality, shortcode rendering and client-side scripts
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Aliyun_Chatbot_Frontend {

    /**
     * Initialize the frontend class
     */
    public function __construct() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue frontend scripts and styles
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'aliyun-chatbot-style',
            ALIYUN_CHATBOT_PLUGIN_URL . 'assets/css/chatbot.css',
            array(),
            ALIYUN_CHATBOT_VERSION
        );
        
        wp_enqueue_script(
            'aliyun-chatbot-script',
            ALIYUN_CHATBOT_PLUGIN_URL . 'assets/js/chatbot.js',
            array('jquery'),
            ALIYUN_CHATBOT_VERSION,
            true
        );
        
        wp_localize_script(
            'aliyun-chatbot-script',
            'aliyunChatbotData',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aliyun_chatbot_nonce'),
                'loading_text' => __('Thinking...', 'aliyun-deepseek-chatbot'),
                'error_text' => __('Sorry, something went wrong. Please try again.', 'aliyun-deepseek-chatbot'),
            )
        );
    }
    
    /**
     * Render the chatbot via shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_chatbot($atts) {
        $atts = shortcode_atts(
            array(
                'title' => __('AI Assistant', 'aliyun-deepseek-chatbot'),
                'placeholder' => __('Ask me anything...', 'aliyun-deepseek-chatbot'),
                'welcome_message' => __('Hello! How can I help you today?', 'aliyun-deepseek-chatbot'),
                'show_clear' => 'no'
            ),
            $atts,
            'aliyun_chatbot'
        );
        
        // Get API settings
        $api_key = get_option('aliyun_chatbot_api_key', '');
        $app_id = get_option('aliyun_chatbot_app_id', '');
        
        if (empty($api_key) || empty($app_id)) {
            if (current_user_can('manage_options')) {
                return '<div class="aliyun-chatbot-error">' . 
                    __('Please configure the Aliyun DeepSeek API key and App ID in the plugin settings.', 'aliyun-deepseek-chatbot') . 
                    '</div>';
            } else {
                return '';
            }
        }
        
        // Generate a unique ID for the chatbot
        $chatbot_id = 'aliyun-chatbot-' . uniqid();
        
        ob_start();
        ?>
        <div id="<?php echo esc_attr($chatbot_id); ?>" class="aliyun-chatbot-container" aria-live="polite" role="region" aria-label="<?php echo esc_attr($atts['title']); ?>">
            <div class="aliyun-chatbot-header">
                <h3><?php echo esc_html($atts['title']); ?></h3>
                <?php if ($atts['show_clear'] === 'yes'): ?>
                <button class="aliyun-chatbot-clear-btn" aria-label="<?php _e('Clear conversation', 'aliyun-deepseek-chatbot'); ?>"><?php _e('Clear', 'aliyun-deepseek-chatbot'); ?></button>
                <?php endif; ?>
            </div>
            <div class="aliyun-chatbot-messages" aria-label="<?php _e('Conversation messages', 'aliyun-deepseek-chatbot'); ?>">
                <div class="aliyun-chatbot-message bot">
                    <div class="aliyun-chatbot-message-content">
                        <?php echo esc_html($atts['welcome_message']); ?>
                    </div>
                </div>
            </div>
            <div class="aliyun-chatbot-input-container">
                <label for="<?php echo esc_attr($chatbot_id); ?>-input" class="screen-reader-text"><?php echo esc_html($atts['placeholder']); ?></label>
                <input type="text" id="<?php echo esc_attr($chatbot_id); ?>-input" class="aliyun-chatbot-input" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" aria-label="<?php echo esc_attr($atts['placeholder']); ?>">
                <button class="aliyun-chatbot-send" aria-label="<?php _e('Send message', 'aliyun-deepseek-chatbot'); ?>"><?php _e('Send', 'aliyun-deepseek-chatbot'); ?></button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}