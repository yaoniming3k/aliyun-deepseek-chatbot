<?php
/**
 * Plugin Name: Aliyun DeepSeek AI ChatBot
 * Plugin URI: https://example.com/plugins/aliyun-deepseek-chatbot
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
    // Set default API endpoint
    if (!get_option('aliyun_chatbot_api_endpoint')) {
        update_option('aliyun_chatbot_api_endpoint', 'https://dashscope.aliyuncs.com/api/v1/apps');
    }
    
    // Set initial version
    update_option('aliyun_chatbot_version', ALIYUN_CHATBOT_VERSION);
    
    // Ensure directories exist
    wp_mkdir_p(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css');
    wp_mkdir_p(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/js');
    wp_mkdir_p(ALIYUN_CHATBOT_PLUGIN_DIR . 'includes');
    
    // Create initial CSS and JS files if they don't exist
    if (!file_exists(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css/chatbot.css')) {
        $css_content = file_get_contents(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css/chatbot-default.css');
        file_put_contents(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css/chatbot.css', $css_content);
    }
    
    if (!file_exists(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css/admin.css')) {
        $admin_css_content = file_get_contents(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css/admin-default.css');
        file_put_contents(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/css/admin.css', $admin_css_content);
    }
    
    if (!file_exists(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/js/chatbot.js')) {
        $js_content = file_get_contents(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/js/chatbot-default.js');
        file_put_contents(ALIYUN_CHATBOT_PLUGIN_DIR . 'assets/js/chatbot.js', $js_content);
    }
    
    // Set default options
    add_option('aliyun_chatbot_api_endpoint', 'https://dashscope.aliyuncs.com/api/v1/apps');
    add_option('aliyun_chatbot_enable_conversation', 1);
    add_option('aliyun_chatbot_show_thoughts', 0);
    add_option('aliyun_chatbot_history_length', 5);
}
register_activation_hook(__FILE__, 'aliyun_deepseek_chatbot_activate');

/**
 * Clean up plugin data when deactivated
 */
function aliyun_deepseek_chatbot_deactivate() {
    // Clean up temporary data if needed
}
register_deactivation_hook(__FILE__, 'aliyun_deepseek_chatbot_deactivate');

/**
 * Create uninstall.php file to handle proper uninstallation
 */
function aliyun_deepseek_chatbot_create_uninstall_file() {
    $uninstall_file = ALIYUN_CHATBOT_PLUGIN_DIR . 'uninstall.php';
    
    if (!file_exists($uninstall_file)) {
        $uninstall_content = <<<PHP
<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove all plugin options
delete_option('aliyun_chatbot_api_key');
delete_option('aliyun_chatbot_api_endpoint');
delete_option('aliyun_chatbot_app_id');
delete_option('aliyun_chatbot_version');
delete_option('aliyun_chatbot_enable_conversation');
delete_option('aliyun_chatbot_show_thoughts');
delete_option('aliyun_chatbot_history_length');
PHP;
        
        file_put_contents($uninstall_file, $uninstall_content);
    }
}
add_action('activated_plugin', 'aliyun_deepseek_chatbot_create_uninstall_file');
