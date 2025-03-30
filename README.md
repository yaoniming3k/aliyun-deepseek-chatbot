# aliyun-deepseek-chatbot

# Aliyun DeepSeek AI ChatBot for WordPress

This plugin integrates the Aliyun DeepSeek AI service into your WordPress website, allowing visitors to interact with an AI assistant through a customizable chat interface.

该插件将阿里云 DeepSeek AI 服务集成到您的 WordPress 网站中，使访客能够通过可自定义的聊天界面与 AI 助手进行互动。

## Features

- Easy to set up and configure
- Responsive chat interface that works on all devices
- Customizable appearance and behavior
- Multi-turn conversations with context memory
- Optional display of AI's thinking process
- Easy integration with shortcodes

- 简单设置与配置
- 响应式聊天界面，兼容所有设备
- 外观与行为可自定义
- 支持多轮对话并具备上下文记忆
- 可选显示 AI 的思考过程
- 通过短代码轻松集成

## Installation

1. Upload the `aliyun-deepseek-chatbot` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings with your Aliyun DeepSeek API credentials

1. 将 aliyun-deepseek-chatbot 文件夹上传至 /wp-content/plugins/ 目录
2. 在 WordPress 的“插件”菜单中启用该插件
3. 使用您的阿里云 DeepSeek API 凭证配置插件设置(智能体应用)

## Configuration

1. Navigate to **Settings > AI ChatBot** in your WordPress admin dashboard
2. Enter your Aliyun DashScope API Key and App ID
3. Configure optional settings as needed:
   - Enable/disable multi-turn conversations
   - Show/hide AI thinking process
   - Adjust conversation history length

1. 在 WordPress 管理后台导航至 设置 > AI ChatBot
2. 输入您的阿里云 DashScope API Key 和 App ID
3. 根据需要配置可选设置：
   - 启用/禁用多轮对话
   - 显示/隐藏 AI 思考过程
   - 调整对话历史长度

## Usage

Add the chatbot to any page or post using the shortcode:
[aliyun_chatbot]

使用以下短代码即可将聊天机器人添加到任意页面或文章中：
[aliyun_chatbot]

### Customization Options

You can customize the chatbot appearance and behavior using these attributes:
[aliyun_chatbot
title="Your Custom Title"
placeholder="Type your question..."
welcome_message="Hi there! How can I assist you today?"
show_clear="yes"
]

您可以使用以下属性自定义聊天机器人的外观和行为：
[aliyun_chatbot  
title="自定义标题"  
placeholder="请输入您的问题..."  
welcome_message="你好！我可以为您提供哪些帮助？"  
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

此插件需要使用阿里云 DashScope API。您需要具备以下条件：

1. 一个已开通 DashScope 服务的阿里云账户  
2. 从您的阿里云账户获取的 API Key  
3. 来自阿里云控制台的 DeepSeek 应用 ID（App ID）  

The API request format follows the Aliyun DeepSeek format:

API 请求格式遵循阿里云 DeepSeek R1 智能体的规范：

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

如果您在使用插件时遇到问题，请按照以下步骤进行排查：

1. 确认您已正确输入 API Key 和 App ID  
2. 验证您的阿里云账户是否已开通 DeepSeek 服务权限  
3. 在 `wp-config.php` 文件中启用 `WP_DEBUG` 以查看详细的错误信息  
4. 检查浏览器控制台是否有 JavaScript 错误  
5. 如果标准设置表单无法使用，请在调试模式下尝试使用直接设置表单

## License

This plugin is licensed under the MIT License.

## Credits

Developed by Chi Leung (https://www.rockbrain.net)