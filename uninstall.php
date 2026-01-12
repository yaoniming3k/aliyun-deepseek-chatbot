<?php
// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options = array(
    'aliyun_chatbot_api_key',
    'aliyun_chatbot_api_mode',
    'aliyun_chatbot_api_endpoint',
    'aliyun_chatbot_allowed_api_hosts',
    'aliyun_chatbot_app_id',
    'aliyun_chatbot_version',
    'aliyun_chatbot_enable_conversation',
    'aliyun_chatbot_show_thoughts',
    'aliyun_chatbot_history_length',
    'aliyun_chatbot_enable_stream',
    'aliyun_chatbot_max_message_length',
    'aliyun_chatbot_workspace_id',
    'aliyun_chatbot_width',
    'aliyun_chatbot_height',
    'aliyun_chatbot_model',
    'aliyun_chatbot_temperature',
    'aliyun_chatbot_max_tokens',
    'aliyun_chatbot_system_message'
);

foreach ($options as $option) {
    delete_option($option);
}

global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_aliyun_chatbot_history_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_aliyun_chatbot_history_%'");
