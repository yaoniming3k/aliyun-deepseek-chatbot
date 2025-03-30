# aliyun-deepseek-chatbot
# Aliyun DeepSeek AI ChatBot for WordPress

This plugin integrates the Aliyun DeepSeek AI service into your WordPress website, allowing visitors to interact with an AI assistant through a customizable chat interface.

## Features

- Easy to set up and configure
- Responsive chat interface that works on all devices
- Customizable appearance and behavior
- Multi-turn conversations with context memory
- Optional display of AI's thinking process
- Easy integration with shortcodes

## Installation

1. Upload the `aliyun-deepseek-chatbot` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings with your Aliyun DeepSeek API credentials

## Configuration

1. Navigate to **Settings > AI ChatBot** in your WordPress admin dashboard
2. Enter your Aliyun DashScope API Key and App ID
3. Configure optional settings as needed:
   - Enable/disable multi-turn conversations
   - Show/hide AI thinking process
   - Adjust conversation history length

## Usage

Add the chatbot to any page or post using the shortcode:
[aliyun_chatbot]

### Customization Options

You can customize the chatbot appearance and behavior using these attributes:
[aliyun_chatbot
title="Your Custom Title"
placeholder="Type your question..."
welcome_message="Hi there! How can I assist you today?"
show_clear="yes"
]

| Attribute | Description | Default |
|-----------|-------------|---------|
| `title` | The title displayed at the top of the chatbot | "AI Assistant" |
| `placeholder` | Placeholder text for the input field | "Ask me anything..." |
| `welcome_message` | Initial message from the chatbot | "Hello! How can I help you today?" |
| `show_clear` | Show clear conversation button (yes/no) | "no" |

## API Configuration

This plugin requires the Aliyun DashScope API. You'll need:

1. An Aliyun account with access to the DashScope service
2. An API key from your Aliyun account
3. A DeepSeek App ID from your Aliyun dashboard

The API request format follows the Aliyun DeepSeek format:
curl --location 'https://dashscope.aliyuncs.com/api/v1/apps/{YOUR_APP_ID}/completion' 
--header 'Authorization: Bearer {YOUR_API_KEY}' 
--header 'Content-Type: application/json' 
--data '{
   "input": {
      "prompt": "User message here"
      },
      "parameters": {
         "has_thoughts": true
         }
         }'

## Directory Structure
aliyun-deepseek-chatbot/

├── aliyun-deepseek-chatbot.php       # Main plugin file
├── includes/
│   ├── class-admin.php               # Admin settings functionality
│   ├── class-frontend.php            # Frontend display functionality
│   └── class-api-handler.php         # API communication handling
├── assets/
│   ├── css/
│   │   ├── chatbot.css               # Frontend styles
│   │   └── admin.css                 # Admin styles
│   └── js/
│       └── chatbot.js                # Frontend JavaScript
└── languages/                        # Translation files

## Troubleshooting

If you encounter issues with the plugin:

1. Check that your API Key and App ID are correctly entered
2. Verify that your Aliyun account has access to the DeepSeek service
3. Enable WP_DEBUG in your wp-config.php file to see detailed error messages
4. Check your browser console for JavaScript errors
5. Try the direct settings form in debug mode if the standard settings form isn't working

## License

This plugin is licensed under the MIT License.

## Credits

Developed by Chi Leung (https://www.rockbrain.net)