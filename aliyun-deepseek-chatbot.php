<?php
/**
 * Plugin Name: Aliyun DeepSeek AI ChatBot
 * Plugin URI: https://github.com/yaoniming3k/aliyun-deepseek-chatbot
 * Description: Add an AI ChatBot powered by Aliyun DeepSeek service to your WordPress website using a shortcode.
 * Version: 1.0.0
 * Author: Chi Leung
 * Author URI: https://www.rockbrain.net
 * Text Domain: aliyun-deepseek-chatbot
 * Domain Path: /languages
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('ALIYUN_CHATBOT_VERSION', '1.0.0');
define('ALIYUN_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ALIYUN_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Load plugin textdomain.
 */
function aliyun_deepseek_chatbot_load_textdomain() {
    load_plugin_textdomain('aliyun-deepseek-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'aliyun_deepseek_chatbot_load_textdomain');

/**
 * Function to check if a new version of the plugin has been installed
 * and perform any necessary updates
 * 
 * @since 1.0.0
 */
function aliyun_deepseek_chatbot_check_version() {
    $installed_version = get_option('aliyun_chatbot_version', '0.0.0');
    
    if (version_compare($installed_version, ALIYUN_CHATBOT_VERSION, '<')) {
        // Perform upgrade tasks if needed
        if (version_compare($installed_version, '1.0.0', '<')) {
            // First installation or upgrading from a pre-1.0.0 version
            // Add any migration code here if needed in the future
        }
        
        // Update stored version number
        update_option('aliyun_chatbot_version', ALIYUN_CHATBOT_VERSION);
    }
}
add_action('plugins_loaded', 'aliyun_deepseek_chatbot_check_version', 5);

// Load required files
require_once ALIYUN_CHATBOT_PLUGIN_DIR . 'includes/class-admin.php';
require_once ALIYUN_CHATBOT_PLUGIN_DIR . 'includes/class-frontend.php';
require_once ALIYUN_CHATBOT_PLUGIN_DIR . 'includes/class-api-handler.php';

/**
 * Initialize the plugin 
 * 
 * Creates instances of the plugin classes and sets up hooks
 *
 * @return void
 */
function aliyun_deepseek_chatbot_init() {
    global $aliyun_chatbot_admin;
    global $aliyun_chatbot_frontend;
    global $aliyun_chatbot_api_handler;
    
    // Initialize the plugin components
    $aliyun_chatbot_admin = new Aliyun_Chatbot_Admin();
    $aliyun_chatbot_frontend = new Aliyun_Chatbot_Frontend();
    $aliyun_chatbot_api_handler = new Aliyun_Chatbot_API_Handler();
}
add_action('plugins_loaded', 'aliyun_deepseek_chatbot_init');

/**
 * Renders the chatbot interface via shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output of the chatbot interface
 */
function aliyun_deepseek_chatbot_shortcode($atts) {
    global $aliyun_chatbot_frontend;
    
    if (!isset($aliyun_chatbot_frontend)) {
        // Plugin not initialized properly
        return '';
    }
    
    // Render through the frontend class
    return $aliyun_chatbot_frontend->render_chatbot($atts);
}
add_shortcode('aliyun_chatbot', 'aliyun_deepseek_chatbot_shortcode');

/**
 * Activation hook - set up initial files and options
 */
function aliyun_deepseek_chatbot_activate() {
    // Set initial version
    update_option('aliyun_chatbot_version', ALIYUN_CHATBOT_VERSION);

    // Set default options (using add_option so existing values won't be overwritten)
    add_option('aliyun_chatbot_allowed_api_hosts', 'dashscope.aliyuncs.com');
    add_option('aliyun_chatbot_api_endpoint', 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions');
    add_option('aliyun_chatbot_enable_conversation', 1);
    add_option('aliyun_chatbot_show_thoughts', 0);
    add_option('aliyun_chatbot_history_length', 5);
    add_option('aliyun_chatbot_enable_stream', 0);
    add_option('aliyun_chatbot_max_message_length', 4000);
    add_option('aliyun_chatbot_width', 600);
    add_option('aliyun_chatbot_height', 500);
    add_option('aliyun_chatbot_model', 'deepseek-chat');
    add_option('aliyun_chatbot_temperature', 1.0);
    add_option('aliyun_chatbot_max_tokens', 4000);
    add_option('aliyun_chatbot_system_message', '');
}
register_activation_hook(__FILE__, 'aliyun_deepseek_chatbot_activate');

/**
 * Clean up plugin data when deactivated
 */
function aliyun_deepseek_chatbot_deactivate() {
    // Clean up temporary data if needed
}
register_deactivation_hook(__FILE__, 'aliyun_deepseek_chatbot_deactivate');
