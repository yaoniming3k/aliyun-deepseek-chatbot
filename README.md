# Aliyun DeepSeek AI ChatBot for WordPress

一个强大的 WordPress 插件，为您的网站添加由阿里云 DeepSeek 驱动的 AI 聊天机器人。支持实时流式输出、推理过程显示、多轮对话等高级功能。

A powerful WordPress plugin that adds an AI chatbot powered by Aliyun DeepSeek to your website. Features real-time streaming output, reasoning process display, multi-turn conversations, and more.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-green.svg)
![License](https://img.shields.io/badge/license-MIT-orange.svg)

## Features | 功能特色

### Core Features | 核心功能
- **Easy Integration | 简单集成** - Use shortcode `[aliyun_chatbot]` to add chatbot to any page | 使用短代码 `[aliyun_chatbot]` 将聊天机器人添加到任意页面
- **OpenAI Compatible | OpenAI 兼容** - Uses Aliyun DeepSeek's OpenAI-compatible API | 使用阿里云 DeepSeek 的 OpenAI 兼容 API
- **Real-time Streaming | 实时流式输出** - ChatGPT-like real-time response experience | 类 ChatGPT 的实时响应体验
- **Multi-turn Conversations | 多轮对话** - Intelligent context memory for coherent dialogues | 智能上下文记忆，支持连贯对话
- **Reasoning Display | 推理过程显示** - Optional display of AI's thinking process (for DeepSeek Reasoner) | 可选显示 AI 的思考过程（适用于 DeepSeek Reasoner）

### Advanced Configuration | 高级配置
- **Dual Model Support | 双模型支持** - Supports DeepSeek Chat and DeepSeek Reasoner (R1) | 支持 DeepSeek Chat 与 DeepSeek Reasoner (R1)
- **Custom System Message | 自定义系统消息** - Define AI's behavior and role | 定义 AI 的行为与角色
- **Temperature Control | 温度控制** - Adjust creativity and randomness of responses | 调整响应的随机性与创造性
- **Token Limit | 令牌限制** - Control maximum response length | 控制最大响应长度
- **Appearance Customization | 外观定制** - Customize chatbot width and height | 自定义聊天窗口宽高
- **Conversation History | 对话历史** - Configurable conversation turn memory | 可配置的对话轮数记忆

## Requirements | 系统要求

- WordPress 5.0 or higher | WordPress 5.0 或更高版本
- PHP 7.4 or higher | PHP 7.4 或更高版本
- cURL extension (for streaming) | cURL 扩展（用于流式输出）
- Aliyun DashScope API Key | 阿里云 DashScope API Key

## Installation | 安装

1. Upload the `aliyun-deepseek-chatbot` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Configure the plugin settings with your Aliyun DashScope API Key

OR

1. Download the zip file from GitHub
2. Install it via the WordPress dashboard (Plugins > Add New > Upload Plugin)

## 安装

1. 将 `aliyun-deepseek-chatbot` 文件夹上传到 `/wp-content/plugins/` 目录
2. 在 WordPress 后台“插件”菜单中启用该插件
3. 使用您的阿里云 DashScope API Key 配置插件

或者

1. 从 GitHub 下载 zip 包
2. 通过 WordPress 仪表盘上传并安装

## Configuration | 配置

1. Navigate to **Settings > AI ChatBot** in your WordPress admin dashboard
2. Enter your Aliyun DashScope API Key
3. Configure optional settings as needed:
   - Enable/disable multi-turn conversations
   - Show/hide AI thinking process
   - Adjust conversation history length

## 配置

1. 在 WordPress 后台进入 **设置 > AI ChatBot**
2. 输入您的阿里云 DashScope API Key
3. 按需配置可选项：
   - 启用/禁用多轮对话
   - 显示/隐藏 AI 思考过程
   - 调整对话历史长度

## Usage | 使用方法

Add the chatbot to any page or post using the shortcode:

```
[aliyun_chatbot]
```

## Customization Options | 自定义选项

You can customize the chatbot appearance and behavior using these attributes:

```
[aliyun_chatbot
title="Your Custom Title"
placeholder="Type your question..."
welcome_message="Hi there! How can I assist you today?"
show_clear="yes"
]
```

您可以使用以下属性自定义聊天机器人外观和行为：

```
[aliyun_chatbot
title="自定义标题"
placeholder="请输入您的问题..."
welcome_message="你好！我可以为您提供哪些帮助？"
show_clear="yes"
]
```

| Attribute | Description | Default |
|-----------|-------------|---------|
| `title` | The title displayed at the top of the chatbot / 顶部标题 | "AI Assistant" |
| `placeholder` | Placeholder text for the input field / 输入框提示文本 | "Ask me anything..." |
| `welcome_message` | Initial message from the chatbot / 欢迎语 | "Hello! How can I help you today?" |
| `show_clear` | Show clear conversation button (yes/no) / 是否显示清空按钮 | "no" |

## Configuration Options | 配置选项

### API Settings | API 设置

| Option | Description | Default |
|--------|-------------|---------|
| API Key | Aliyun DashScope API Key / 阿里云 DashScope API Key | - |
| Model | AI model selection / AI 模型选择 | deepseek-chat |
| Temperature | Response randomness (0.0-2.0) / 响应随机性（0.0-2.0） | 1.0 |
| Max Tokens | Maximum response tokens (100-8000) / 最大响应令牌数（100-8000） | 4000 |
| System Message | System prompt to define AI behavior / 系统提示语 | - |

### Feature Settings | 功能设置

| Option | Description | Default |
|--------|-------------|---------|
| Multi-turn Conversation | Enable multi-turn conversations / 启用多轮对话 | On |
| Show Thinking Process | Display reasoning process / 显示推理过程 | Off |
| Conversation History Length | Conversation history turns (1-20) / 对话历史轮数（1-20） | 5 |
| Enable Streaming Output | Enable real-time streaming / 启用实时流式输出 | Off |

### Appearance Settings | 外观设置

| Option | Description | Default |
|--------|-------------|---------|
| Chatbot Width | Chatbot width in pixels (300-1200) / 聊天框宽度（300-1200） | 600 |
| Chatbot Height | Chatbot height in pixels (300-800) / 聊天框高度（300-800） | 500 |

## Technical Details | 技术细节

### API Endpoint | API 端点

This plugin uses the OpenAI-compatible endpoint:

```
https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions
```

### Example API Request | API 请求示例

```bash
curl -X POST 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions' \
-H 'Content-Type: application/json' \
-H 'Authorization: Bearer YOUR_API_KEY' \
-d '{
  "model": "deepseek-chat",
  "messages": [
    {"role": "system", "content": "You are a helpful assistant."},
    {"role": "user", "content": "Hello!"}
  ],
  "temperature": 1.0,
  "max_tokens": 4000,
  "stream": false
}'
```

### Conversation History Storage | 对话历史存储

- Uses WordPress Transients API | 使用 WordPress Transients API 存储
- Auto-expiry time: 1 hour | 自动过期时间：1 小时
- Session management based on session_id | 基于 session_id 的会话管理

### Streaming Output | 流式输出

- Implemented using Server-Sent Events (SSE) | 使用 SSE 实现
- Supports real-time display of reasoning and content | 支持实时显示推理过程与内容
- cURL streaming processing with low latency | 使用 cURL 进行低延迟流式处理

### Security | 安全

- WordPress Nonce verification | WordPress Nonce 验证
- All inputs sanitized | 所有输入均经过清理
- API key stored in WordPress options | API Key 存储在 WordPress 选项中
- XSS protection | XSS 防护

## Directory Structure | 目录结构

```
aliyun-deepseek-chatbot/
|-- aliyun-deepseek-chatbot.php       # Main plugin file
|-- includes/
|   |-- class-admin.php               # Admin settings functionality
|   |-- class-frontend.php            # Frontend display functionality
|   |-- class-api-handler.php         # API communication handling
|-- assets/
|   |-- css/
|   |   |-- chatbot.css               # Frontend styles
|   |   |-- admin.css                 # Admin styles
|   |-- js/
|       |-- chatbot.js                # Frontend JavaScript
|-- languages/                        # Translation files
```

## Implementation Notes | 实现说明

- Main plugin file registers hooks, loads classes, and sets defaults | 主插件文件负责注册钩子、加载类并设置默认选项
- Admin class provides settings UI and validation | 后台类提供设置页面与数据验证
- Frontend class renders the shortcode and enqueues assets | 前端类渲染短代码并加载资源
- API handler manages AJAX/SSE and response parsing | API 类负责 AJAX/SSE 请求与响应解析

## Customization | 自定义

- Edit `assets/css/chatbot.css` and `assets/css/admin.css` for styling | 修改 `assets/css/chatbot.css` 和 `assets/css/admin.css` 进行样式定制
- Edit `assets/js/chatbot.js` for behavior changes | 修改 `assets/js/chatbot.js` 调整行为
- `*-default` files are reference copies; the plugin loads the non-default files | `*-default` 为参考样板，插件优先加载非 default 文件

## Debugging | 调试

- When `WP_DEBUG` is enabled, the settings page shows extra debug info and a direct form | 启用 `WP_DEBUG` 后，设置页面会显示调试信息并提供直连表单
- API requests log metadata to the WordPress debug log | API 请求信息会记录到 WordPress 调试日志

## Troubleshooting | 故障排查

1. Check that your API Key is correctly entered | 确认已正确输入 API Key
2. Verify that your Aliyun account has access to the DeepSeek service | 确认阿里云账户已开通 DeepSeek 服务权限
3. Enable `WP_DEBUG` in `wp-config.php` to see detailed error messages | 在 `wp-config.php` 中启用 `WP_DEBUG` 查看详细错误
4. Check your browser console for JavaScript errors | 查看浏览器控制台是否有 JavaScript 错误
5. Try the direct settings form in debug mode if the standard form isn't working | 标准表单无法使用时，可在调试模式下使用直连表单

## License

This plugin is licensed under the MIT License.

## Credits

Developed by Chi Leung (https://www.rockbrain.net)
