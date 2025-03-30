<?php
/**
 * Admin Class for Aliyun DeepSeek ChatBot
 *
 * Handles all admin functionality, settings pages and admin UI
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Aliyun_Chatbot_Admin {

    /**
     * Initialize the admin class
     */
    public function __construct() {
        // Add settings page
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Handle direct form submission
        add_action('admin_init', array($this, 'handle_direct_form_submission'));
    }
    
    /**
     * Handle direct form submission
     */
    public function handle_direct_form_submission() {
        if (
            isset($_POST['direct_submit']) && 
            isset($_POST['direct_save_nonce']) && 
            wp_verify_nonce($_POST['direct_save_nonce'], 'direct_save_action')
        ) {
            if (isset($_POST['direct_app_id'])) {
                $direct_app_id = sanitize_text_field($_POST['direct_app_id']);
                $result = update_option('aliyun_chatbot_app_id', $direct_app_id);
                
                if (WP_DEBUG) {
                    error_log('Direct alternative form - Update App ID result: ' . ($result ? 'Success' : 'Failed'));
                }
                
                if (isset($_POST['direct_api_key'])) {
                    $direct_api_key = sanitize_text_field($_POST['direct_api_key']);
                    update_option('aliyun_chatbot_api_key', $direct_api_key);
                }
                
                if (isset($_POST['direct_api_endpoint'])) {
                    $direct_api_endpoint = sanitize_text_field($_POST['direct_api_endpoint']);
                    update_option('aliyun_chatbot_api_endpoint', $direct_api_endpoint);
                }
                
                // Add admin notice for success
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success is-dismissible"><p>' . 
                        __('Settings saved via direct form.', 'aliyun-deepseek-chatbot') . 
                        '</p></div>';
                });
                
                // Redirect to refresh page and see updated values
                wp_redirect(add_query_arg('settings-updated', 'true'));
                exit;
            }
        }
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_options_page(
            __('Aliyun DeepSeek ChatBot Settings', 'aliyun-deepseek-chatbot'),
            __('AI ChatBot', 'aliyun-deepseek-chatbot'),
            'manage_options',
            'aliyun-deepseek-chatbot',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook The current admin page
     */
    public function admin_enqueue_scripts($hook) {
        if ('settings_page_aliyun-deepseek-chatbot' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'aliyun-chatbot-admin',
            ALIYUN_CHATBOT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ALIYUN_CHATBOT_VERSION
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'aliyun_deepseek_settings', 
            'aliyun_chatbot_api_key', 
            array('sanitize_callback' => 'sanitize_text_field')
        );
        
        register_setting(
            'aliyun_deepseek_settings', 
            'aliyun_chatbot_api_endpoint', 
            array('sanitize_callback' => 'esc_url_raw')
        );
        
        register_setting(
            'aliyun_deepseek_settings', 
            'aliyun_chatbot_app_id', 
            array('sanitize_callback' => 'sanitize_text_field')
        );
        
        register_setting(
            'aliyun_deepseek_settings', 
            'aliyun_chatbot_enable_conversation', 
            array('sanitize_callback' => 'intval')
        );
        
        register_setting(
            'aliyun_deepseek_settings', 
            'aliyun_chatbot_show_thoughts', 
            array('sanitize_callback' => 'intval')
        );
        
        register_setting(
            'aliyun_deepseek_settings', 
            'aliyun_chatbot_history_length', 
            array(
                'sanitize_callback' => function($value) {
                    $value = intval($value);
                    return max(1, min(20, $value)); // Ensure value is between 1 and 20
                }
            )
        );
        
        // API Settings section
        add_settings_section(
            'aliyun_deepseek_api_section',
            __('API Settings', 'aliyun-deepseek-chatbot'),
            array($this, 'render_api_section'),
            'aliyun_deepseek_settings'
        );
        
        add_settings_field(
            'aliyun_chatbot_api_key',
            __('API Key', 'aliyun-deepseek-chatbot'),
            array($this, 'render_api_key_field'),
            'aliyun_deepseek_settings',
            'aliyun_deepseek_api_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_api_endpoint',
            __('API Endpoint', 'aliyun-deepseek-chatbot'),
            array($this, 'render_api_endpoint_field'),
            'aliyun_deepseek_settings',
            'aliyun_deepseek_api_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_app_id',
            __('App ID', 'aliyun-deepseek-chatbot'),
            array($this, 'render_app_id_field'),
            'aliyun_deepseek_settings',
            'aliyun_deepseek_api_section'
        );
        
        // Features Settings section
        add_settings_section(
            'aliyun_deepseek_features_section',
            __('Features Settings', 'aliyun-deepseek-chatbot'),
            array($this, 'render_features_section'),
            'aliyun_deepseek_settings'
        );
        
        add_settings_field(
            'aliyun_chatbot_enable_conversation',
            __('Multi-turn Conversation', 'aliyun-deepseek-chatbot'),
            array($this, 'render_enable_conversation_field'),
            'aliyun_deepseek_settings',
            'aliyun_deepseek_features_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_show_thoughts',
            __('Show Thinking Process', 'aliyun-deepseek-chatbot'),
            array($this, 'render_show_thoughts_field'),
            'aliyun_deepseek_settings',
            'aliyun_deepseek_features_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_history_length',
            __('Conversation History Length', 'aliyun-deepseek-chatbot'),
            array($this, 'render_history_length_field'),
            'aliyun_deepseek_settings',
            'aliyun_deepseek_features_section'
        );
        
        // API Settings section
        add_settings_section(
            'aliyun_deepseek_api_section',
            __('API Settings', 'aliyun-deepseek-chatbot'),
            array($this, 'render_api_section'),
            'aliyun-deepseek-chatbot'
        );
        
        add_settings_field(
            'aliyun_chatbot_api_key',
            __('API Key', 'aliyun-deepseek-chatbot'),
            array($this, 'render_api_key_field'),
            'aliyun-deepseek-chatbot',
            'aliyun_deepseek_api_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_api_endpoint',
            __('API Endpoint', 'aliyun-deepseek-chatbot'),
            array($this, 'render_api_endpoint_field'),
            'aliyun-deepseek-chatbot',
            'aliyun_deepseek_api_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_app_id',
            __('App ID', 'aliyun-deepseek-chatbot'),
            array($this, 'render_app_id_field'),
            'aliyun-deepseek-chatbot',
            'aliyun_deepseek_api_section'
        );
        
        // Features Settings section
        add_settings_section(
            'aliyun_deepseek_features_section',
            __('Features Settings', 'aliyun-deepseek-chatbot'),
            array($this, 'render_features_section'),
            'aliyun-deepseek-chatbot'
        );
        
        add_settings_field(
            'aliyun_chatbot_enable_conversation',
            __('Multi-turn Conversation', 'aliyun-deepseek-chatbot'),
            array($this, 'render_enable_conversation_field'),
            'aliyun-deepseek-chatbot',
            'aliyun_deepseek_features_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_show_thoughts',
            __('Show Thinking Process', 'aliyun-deepseek-chatbot'),
            array($this, 'render_show_thoughts_field'),
            'aliyun-deepseek-chatbot',
            'aliyun_deepseek_features_section'
        );
        
        add_settings_field(
            'aliyun_chatbot_history_length',
            __('Conversation History Length', 'aliyun-deepseek-chatbot'),
            array($this, 'render_history_length_field'),
            'aliyun-deepseek-chatbot',
            'aliyun_deepseek_features_section'
        );
    }

    /**
     * Render API section description
     */
    public function render_api_section() {
        echo '<p>' . __('Enter your Aliyun DeepSeek API credentials below.', 'aliyun-deepseek-chatbot') . '</p>';
        echo '<p>' . __('Note: This plugin uses the Aliyun DeepSeek API format.', 'aliyun-deepseek-chatbot') . '</p>';
    }

    /**
     * Render features section description
     */
    public function render_features_section() {
        echo '<p>' . __('Configure additional features for the chatbot.', 'aliyun-deepseek-chatbot') . '</p>';
    }

    /**
     * Render API key field
     */
    public function render_api_key_field() {
        $api_key = get_option('aliyun_chatbot_api_key', '');
        ?>
        <input type="password" id="aliyun_chatbot_api_key" name="aliyun_chatbot_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
        <p class="description"><?php _e('Your Aliyun DeepSeek API key', 'aliyun-deepseek-chatbot'); ?></p>
        <?php
    }

    /**
     * Render API endpoint field
     */
    public function render_api_endpoint_field() {
        $api_endpoint = get_option('aliyun_chatbot_api_endpoint', 'https://dashscope.aliyuncs.com/api/v1/apps');
        ?>
        <input type="text" id="aliyun_chatbot_api_endpoint" name="aliyun_chatbot_api_endpoint" value="<?php echo esc_attr($api_endpoint); ?>" class="regular-text">
        <p class="description"><?php _e('The Aliyun DeepSeek API endpoint (default: https://dashscope.aliyuncs.com/api/v1/apps)', 'aliyun-deepseek-chatbot'); ?></p>
        <?php
    }

    /**
     * Render App ID field
     */
    public function render_app_id_field() {
        $app_id = get_option('aliyun_chatbot_app_id', '');
        ?>
        <input type="text" id="aliyun_chatbot_app_id" name="aliyun_chatbot_app_id" value="<?php echo esc_attr($app_id); ?>" class="regular-text">
        <p class="description"><?php _e('Your Aliyun DeepSeek App ID', 'aliyun-deepseek-chatbot'); ?></p>
        <?php
        
        // Add direct output of current value for debugging
        if (WP_DEBUG) {
            echo '<p><strong>Debug</strong>: Current stored App ID value: "' . esc_html($app_id) . '"</p>';
        }
    }

    /**
     * Render multi-turn conversation enable field
     */
    public function render_enable_conversation_field() {
        $enable_conversation = get_option('aliyun_chatbot_enable_conversation', 1);
        ?>
        <label>
            <input type="checkbox" id="aliyun_chatbot_enable_conversation" name="aliyun_chatbot_enable_conversation" value="1" <?php checked(1, $enable_conversation); ?>>
            <?php _e('Enable multi-turn conversation', 'aliyun-deepseek-chatbot'); ?>
        </label>
        <p class="description"><?php _e('When enabled, the chatbot will remember previous messages in the conversation.', 'aliyun-deepseek-chatbot'); ?></p>
        <?php
    }

    /**
     * Render show thinking process field
     */
    public function render_show_thoughts_field() {
        $show_thoughts = get_option('aliyun_chatbot_show_thoughts', 0);
        ?>
        <label>
            <input type="checkbox" id="aliyun_chatbot_show_thoughts" name="aliyun_chatbot_show_thoughts" value="1" <?php checked(1, $show_thoughts); ?>>
            <?php _e('Show AI thinking process', 'aliyun-deepseek-chatbot'); ?>
        </label>
        <p class="description"><?php _e('When enabled, the chatbot will display the AI\'s thinking process before showing the answer.', 'aliyun-deepseek-chatbot'); ?></p>
        <?php
    }

    /**
     * Render history length field
     */
    public function render_history_length_field() {
        $history_length = get_option('aliyun_chatbot_history_length', 5);
        ?>
        <input type="number" id="aliyun_chatbot_history_length" name="aliyun_chatbot_history_length" value="<?php echo esc_attr($history_length); ?>" class="small-text" min="1" max="20">
        <p class="description"><?php _e('Number of conversation turns to remember (1-20). Higher values provide more context but may slow down responses.', 'aliyun-deepseek-chatbot'); ?></p>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Force-refresh values from database for display
        $app_id = get_option('aliyun_chatbot_app_id', '');
        $api_key = get_option('aliyun_chatbot_api_key', '');
        $api_endpoint = get_option('aliyun_chatbot_api_endpoint', 'https://dashscope.aliyuncs.com/api/v1/apps');
        $enable_conversation = get_option('aliyun_chatbot_enable_conversation', 1);
        $show_thoughts = get_option('aliyun_chatbot_show_thoughts', 0);
        $history_length = get_option('aliyun_chatbot_history_length', 5);
        
        // Check if settings were just updated
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            add_settings_error(
                'aliyun_chatbot_messages',
                'aliyun_chatbot_updated',
                __('Settings saved.', 'aliyun-deepseek-chatbot'),
                'success'
            );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php 
            // Show settings errors/notices
            settings_errors('aliyun_chatbot_messages');
            ?>
            
            <?php if (WP_DEBUG && current_user_can('manage_options')): ?>
            <!-- Alternative direct form for debugging -->
            <div class="card" style="max-width:800px; margin-bottom:20px; background:#f7f7f7; padding:10px 20px; border-left:4px solid #007cba;">
                <h2><?php _e('Direct Settings Form (Debug)', 'aliyun-deepseek-chatbot'); ?></h2>
                <p><strong><?php _e('Try this alternative form if the standard form below is not working.', 'aliyun-deepseek-chatbot'); ?></strong></p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('direct_save_action', 'direct_save_nonce'); ?>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label for="direct_api_key"><?php _e('API Key', 'aliyun-deepseek-chatbot'); ?></label></th>
                            <td>
                                <input type="password" id="direct_api_key" name="direct_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="direct_app_id"><?php _e('App ID', 'aliyun-deepseek-chatbot'); ?></label></th>
                            <td>
                                <input type="text" id="direct_app_id" name="direct_app_id" value="<?php echo esc_attr($app_id); ?>" class="regular-text">
                                <p><strong>Current stored value:</strong> "<?php echo esc_html($app_id); ?>"</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="direct_api_endpoint"><?php _e('API Endpoint', 'aliyun-deepseek-chatbot'); ?></label></th>
                            <td>
                                <input type="text" id="direct_api_endpoint" name="direct_api_endpoint" value="<?php echo esc_attr($api_endpoint); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="direct_submit" id="direct_submit" class="button button-primary" value="<?php _e('Save Settings (Direct)', 'aliyun-deepseek-chatbot'); ?>">
                    </p>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Standard WordPress Settings API form -->
            <form action="options.php" method="post">
                <?php
                settings_fields('aliyun_deepseek_settings');
                do_settings_sections('aliyun-deepseek-chatbot');
                submit_button(__('Save Settings', 'aliyun-deepseek-chatbot'));
                ?>
            </form>
            
            <div class="aliyun-chatbot-config-info">
                <h2><?php _e('API Configuration', 'aliyun-deepseek-chatbot'); ?></h2>
                <p><?php _e('Required settings:', 'aliyun-deepseek-chatbot'); ?></p>
                <ul>
                    <li><strong><?php _e('API Key:', 'aliyun-deepseek-chatbot'); ?></strong> <?php _e('Your Aliyun DashScope API key', 'aliyun-deepseek-chatbot'); ?></li>
                    <li><strong><?php _e('App ID:', 'aliyun-deepseek-chatbot'); ?></strong> <?php _e('Your Aliyun DeepSeek App ID', 'aliyun-deepseek-chatbot'); ?></li>
                </ul>
                <p><?php _e('The API request format follows the Aliyun DeepSeek format as shown below:', 'aliyun-deepseek-chatbot'); ?></p>
                <pre>
curl --location 'https://dashscope.aliyuncs.com/api/v1/apps/{YOUR_APP_ID}/completion' \
--header 'Authorization: Bearer {YOUR_API_KEY}' \
--header 'Content-Type: application/json' \
--data '{
    "input": {
        "prompt": "User message here"
    },
    "parameters": {
        "has_thoughts": true
    }
}'
                </pre>
            </div>
            
            <div class="aliyun-chatbot-shortcode-info">
                <h2><?php _e('Shortcode Usage', 'aliyun-deepseek-chatbot'); ?></h2>
                <p><?php _e('Basic usage:', 'aliyun-deepseek-chatbot'); ?></p>
                <code>[aliyun_chatbot]</code>
                
                <p><?php _e('Advanced usage with all parameters:', 'aliyun-deepseek-chatbot'); ?></p>
                <code>[aliyun_chatbot title="Your Custom Title" placeholder="Type your question..." welcome_message="Hi there! How can I assist you today?" show_clear="yes"]</code>
                
                <p><?php _e('Available parameters:', 'aliyun-deepseek-chatbot'); ?></p>
                <ul>
                    <li><strong>title</strong>: <?php _e('The title displayed at the top of the chatbot', 'aliyun-deepseek-chatbot'); ?></li>
                    <li><strong>placeholder</strong>: <?php _e('Placeholder text for the input field', 'aliyun-deepseek-chatbot'); ?></li>
                    <li><strong>welcome_message</strong>: <?php _e('Initial message from the chatbot', 'aliyun-deepseek-chatbot'); ?></li>
                    <li><strong>show_clear</strong>: <?php _e('Show clear conversation button (yes/no)', 'aliyun-deepseek-chatbot'); ?></li>
                </ul>
            </div>
            
            <?php if (WP_DEBUG && current_user_can('manage_options')): ?>
            <div class="aliyun-chatbot-debug-info" style="margin-top: 20px; padding: 10px; background: #f5f5f5; border-left: 4px solid #ccc;">
                <h3><?php _e('Debug Information', 'aliyun-deepseek-chatbot'); ?></h3>
                <p><strong><?php _e('Current API Key:', 'aliyun-deepseek-chatbot'); ?></strong> <?php echo $api_key ? '**********' . substr($api_key, -4) : __('Not set', 'aliyun-deepseek-chatbot'); ?></p>
                <p><strong><?php _e('Current App ID:', 'aliyun-deepseek-chatbot'); ?></strong> "<?php echo esc_html($app_id) ?: __('Not set', 'aliyun-deepseek-chatbot'); ?>"</p>
                <p><strong><?php _e('Current API Endpoint:', 'aliyun-deepseek-chatbot'); ?></strong> <?php echo esc_html($api_endpoint); ?></p>
                
                <h4>WordPress Database Check</h4>
                <p>Raw option value for 'aliyun_chatbot_app_id': <?php echo esc_html(print_r(get_option('aliyun_chatbot_app_id'), true)); ?></p>
                
                <h4>Settings API Registration</h4>
                <p>All registered settings:</p>
                <pre><?php print_r(get_registered_settings()); ?></pre>
            </div>
            <?php endif; ?>
        </div>
        <?php
        // Note: Direct form submission is now handled in the handle_direct_form_submission method
    }
}
