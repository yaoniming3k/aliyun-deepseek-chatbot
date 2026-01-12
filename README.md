# 阿里云百炼 AI 聊天机器人 | Aliyun Bailian ChatBot for WordPress

一个强大的 WordPress 插件，为您的网站添加由阿里云百炼（Model Studio）驱动的 AI 聊天机器人。**支持双模式**：模型直接调用和应用构建调用。

A powerful WordPress plugin that adds an AI chatbot powered by Aliyun Bailian (Model Studio) to your website. **Dual-mode support**: Direct model calling and Application API calling.

![Version](https://img.shields.io/badge/version-1.2.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-green.svg)
![License](https://img.shields.io/badge/license-MIT-orange.svg)
![Aliyun](https://img.shields.io/badge/Aliyun-Bailian-orange.svg)

## 🌟 Features | 功能特色

### Dual API Modes | 双 API 模式 ⭐

#### 模式 1: 模型直接调用 | Model API (Direct Calling)
- **直接调用大模型** - 直接访问 Qwen、DeepSeek 等模型 | Direct access to Qwen, DeepSeek models
- **完全参数控制** - 自定义温度、最大 Token、系统提示词 | Full control over temperature, max_tokens, system message
- **适用场景** - 简单问答、客服对话 | Perfect for simple Q&A and customer service
- **本地对话管理** - 插件管理对话历史 | Plugin manages conversation history locally

#### 模式 2: 应用构建调用 | Application API (Agent/Workflow)
- **调用百炼应用** - 调用在百炼控制台创建的智能体或工作流 | Call applications built in Bailian Console
- **内置高级功能** - RAG 知识库、工具调用、MCP 集成 | Built-in RAG, tool calling, MCP integration
- **云端对话管理** - 使用 session_id 由云端托管对话历史 | Cloud-managed conversation via session_id
- **适用场景** - 复杂业务、知识库问答、多步骤任务 | Perfect for complex business, knowledge base, multi-step tasks

### Core Features | 核心功能
- **Easy Integration | 简单集成** - Use shortcode `[aliyun_chatbot]` to add chatbot to any page | 使用短代码 `[aliyun_chatbot]` 将聊天机器人添加到任意页面
- **Real-time Streaming | 实时流式输出** - ChatGPT-like real-time response experience | 类 ChatGPT 的实时响应体验
- **Multi-turn Conversations | 多轮对话** - Intelligent context memory for coherent dialogues | 智能上下文记忆，支持连贯对话
- **Reasoning Display | 推理过程显示** - Optional display of AI's thinking process (DeepSeek R1) | 可选显示 AI 的思考过程（DeepSeek R1）
- **Multiple Models | 多模型支持** - Qwen-Plus, Qwen-Max, DeepSeek-V3, DeepSeek-R1, Qwen-VL | 支持 Qwen-Plus、Qwen-Max、DeepSeek-V3、DeepSeek-R1、Qwen-VL
- **Appearance Customization | 外观定制** - Customize chatbot width and height | 自定义聊天窗口宽高

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

### Quick Start | 快速开始

1. Navigate to **Settings > AI ChatBot** in your WordPress admin dashboard
2. Enter your **Aliyun DashScope API Key** ([Get API Key](https://bailian.console.aliyun.com/#/api-key))
3. **Choose API Mode**:
   - **Model API**: For simple Q&A (recommended for beginners)
   - **Application API**: For advanced features (requires creating an app in [Bailian Console](https://bailian.console.aliyun.com/#/app-center))
4. Configure model/application settings based on your mode
5. Add `[aliyun_chatbot]` shortcode to any page

### 快速开始

1. 在 WordPress 后台进入 **设置 > AI ChatBot**
2. 输入您的**阿里云 DashScope API Key** ([获取 API Key](https://bailian.console.aliyun.com/#/api-key))
3. **选择 API 模式**：
   - **模型 API**：用于简单问答（推荐新手）
   - **应用 API**：用于高级功能（需要在[百炼控制台](https://bailian.console.aliyun.com/#/app-center)创建应用）
4. 根据模式配置模型/应用设置
5. 在任意页面添加 `[aliyun_chatbot]` 短代码

### Mode-Specific Configuration | 模式专属配置

#### Model API Mode | 模型 API 模式
- **Model Selection** - Choose Qwen-Plus, Qwen-Max, or DeepSeek-V3 | 选择 Qwen-Plus、Qwen-Max 或 DeepSeek-V3
- **System Message** - Define AI behavior and role | 定义 AI 行为和角色
- **Temperature** - Control response randomness (0.0-2.0) | 控制响应随机性 (0.0-2.0)
- **Max Tokens** - Limit response length | 限制响应长度

#### Application API Mode | 应用 API 模式
- **App ID** - Your application ID from Bailian Console | 百炼控制台的应用 ID ([How to get](https://help.aliyun.com/zh/model-studio/obtain-api-key-app-id-and-workspace-id))
- **Workspace ID** - Optional, for sub-business spaces | 可选，用于子业务空间
- Configuration is done in Bailian Console (system message, tools, RAG, etc.) | 在百炼控制台配置（系统提示词、工具、RAG 等）

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

## 🆚 Mode Comparison | 模式对比

| Feature / 功能 | Model API<br>模型 API | Application API<br>应用 API |
|---------------|----------------------|----------------------------|
| **Setup Complexity<br>配置复杂度** | ⭐ Simple | ⭐⭐ Moderate (requires Bailian app) |
| **Conversation Management<br>对话管理** | Local (WordPress)<br>本地（WordPress） | Cloud-managed (session_id)<br>云端托管（session_id） |
| **RAG (Knowledge Base)<br>RAG（知识库）** | ❌ Not supported | ✅ Built-in support |
| **Tool Calling<br>工具调用** | ❌ Not supported | ✅ Built-in support |
| **MCP Integration<br>MCP 集成** | ❌ Not supported | ✅ Built-in support |
| **System Message<br>系统提示词** | Configured in plugin<br>在插件中配置 | Configured in Bailian Console<br>在百炼控制台配置 |
| **Model Selection<br>模型选择** | In plugin settings<br>插件设置 | In Bailian app config<br>百炼应用配置 |
| **Best For<br>最适合** | Simple Q&A, customer service<br>简单问答、客服 | Complex business logic, knowledge base<br>复杂业务逻辑、知识库 |
| **API Endpoint<br>API 端点** | `/compatible-mode/v1/chat/completions` | `/api/v1/apps/{app_id}/completion` |

### Which Mode Should I Choose? | 应该选择哪种模式？

✅ **Choose Model API if:** | **选择模型 API 如果：**
- You want quick setup without creating apps | 希望快速设置，无需创建应用
- Simple Q&A or customer service scenarios | 简单的问答或客服场景
- Full control over model parameters | 完全控制模型参数
- No need for RAG or tool calling | 不需要 RAG 或工具调用

✅ **Choose Application API if:** | **选择应用 API 如果：**
- You need RAG (knowledge base) functionality | 需要 RAG（知识库）功能
- Complex workflows or multi-step tasks | 复杂工作流或多步骤任务
- Integration with external tools/services | 需要与外部工具/服务集成
- Want cloud-managed conversation history | 希望云端管理对话历史

## Configuration Options | 配置选项

### API Settings | API 设置

| Option | Description | Default |
|--------|-------------|---------|
| API Mode | Model (OpenAI-compatible) or Agent (App) / API 模式 | model |
| API Key | Aliyun DashScope API Key / 阿里云 DashScope API Key | - |
| API Endpoint | Model API endpoint / 模型接口端点 | https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions |
| App ID | Agent App ID / 智能体应用 ID | - |
| Workspace ID | Agent Workspace ID / 业务空间 ID | - |
| Allowed API Hosts | Allowed API hostnames (comma-separated) / 允许的 API 域名（逗号分隔） | dashscope.aliyuncs.com, dashscope-intl.aliyuncs.com |
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
| Max Message Length | Maximum message length in characters (200-20000) / 消息最大字符长度（200-20000） | 4000 |
| Enable Streaming Output | Enable real-time streaming / 启用实时流式输出 | Off |

### Appearance Settings | 外观设置

| Option | Description | Default |
|--------|-------------|---------|
| Chatbot Width | Chatbot width in pixels (300-1200) / 聊天框宽度（300-1200） | 600 |
| Chatbot Height | Chatbot height in pixels (300-800) / 聊天框高度（300-800） | 500 |

## Technical Details | 技术细节

### API Endpoint | API 端点

This plugin supports both model and agent endpoints:

```
https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions
https://dashscope.aliyuncs.com/api/v1/apps/APP_ID/completion
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
