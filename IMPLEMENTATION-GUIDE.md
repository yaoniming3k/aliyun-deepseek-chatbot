# Implementation Guide for Aliyun DeepSeek ChatBot

# 阿里云 DeepSeek 聊天机器人插件实现指南

This document explains how to implement the restructured plugin from the separate files.

本文档说明了如何从各个独立文件结构实现的插件。

## File Structure

## 文件结构

Create a directory structure as follows:

请创建如下目录结构：

aliyun-deepseek-chatbot/
├── aliyun-deepseek-chatbot.php       # Main plugin file
├── includes/
│   ├── class-admin.php               # Admin settings functionality
│   ├── class-frontend.php            # Frontend display functionality
│   └── class-api-handler.php         # API communication handling
├── assets/
│   ├── css/
│   │   ├── chatbot-default.css       # Default frontend styles
│   │   ├── chatbot.css               # Custom frontend styles (copy of default initially)
│   │   ├── admin-default.css         # Default admin styles
│   │   └── admin.css                 # Custom admin styles (copy of default initially)
│   └── js/
│       ├── chatbot-default.js        # Default frontend JavaScript
│       └── chatbot.js                # Custom frontend JavaScript (copy of default initially)
└── languages/                        # Translation files (empty initially)

## Installation Steps

1. Create the directory structure shown above
2. Copy each file from this repository into its corresponding location
3. For first-time installation, copy the -default CSS and JS files to create the non-default versions:
   - Copy `chatbot-default.css` to `chatbot.css`
   - Copy `admin-default.css` to `admin.css`
   - Copy `chatbot-default.js` to `chatbot.js`
4. Install the plugin via WordPress admin or by placing the entire folder in your `/wp-content/plugins/` directory
5. Activate the plugin through the WordPress admin interface

OR

Follow ReadMe.md

OR

1. Download zip file from Github.
2. Install it as what you regular do to Wordpress plugin installation by wordpress dashboard.

## 安装步骤

1. 创建上述所示的目录结构  
2. 将本仓库中的每个文件复制到对应的位置  
3. 若为首次安装，请将默认的 CSS 和 JS 文件复制为可编辑版本：  
   - 将 `chatbot-default.css` 复制为 `chatbot.css`  
   - 将 `admin-default.css` 复制为 `admin.css`  
   - 将 `chatbot-default.js` 复制为 `chatbot.js`  
4. 通过 WordPress 管理后台安装插件，或将整个插件文件夹放入 `/wp-content/plugins/` 目录中  
5. 在 WordPress 后台启用插件  

**或**

请参考 `ReadMe.md` 文件中的说明进行安装  

**或**

1. 从 Github 下载插件的 zip 压缩包  
2. 通过 WordPress 仪表盘按照常规方式上传并安装该插件

## Key Components

### 1. Main Plugin File (`aliyun-deepseek-chatbot.php`)
- Defines plugin information and constants
- Loads language files
- Includes the component class files
- Sets up activation and uninstallation hooks
- Initializes the plugin

### 2. Admin Class (`class-admin.php`)
- Creates the settings page
- Handles settings registration and validation
- Renders admin UI elements
- Provides debugging information when needed

### 3. Frontend Class (`class-frontend.php`)
- Handles the shortcode rendering
- Manages frontend scripts and styles
- Creates the chat interface HTML

### 4. API Handler Class (`class-api-handler.php`)
- Manages AJAX requests
- Communicates with the Aliyun DeepSeek API
- Processes API responses
- Handles errors and returns formatted responses

### 5. CSS and JavaScript Files
- `chatbot.css`: Styles the frontend chat interface
- `admin.css`: Styles the admin settings page
- `chatbot.js`: Provides the frontend chat functionality

## 关键组件说明

### 1. 主插件文件（`aliyun-deepseek-chatbot.php`）  
- 定义插件信息和常量  
- 加载语言文件  
- 引入各个功能类文件  
- 设置插件的激活与卸载钩子  
- 初始化插件主流程  

### 2. 管理端类（`class-admin.php`）  
- 创建设置页面  
- 处理设置的注册与验证  
- 渲染后台界面元素  
- 提供调试信息（如启用调试模式）  

### 3. 前端类（`class-frontend.php`）  
- 处理短代码渲染  
- 管理前端脚本和样式的加载  
- 构建聊天界面的 HTML  

### 4. API 处理类（`class-api-handler.php`）  
- 处理 AJAX 请求  
- 与阿里云 DeepSeek API 进行通信  
- 解析并处理 API 响应  
- 错误处理与返回格式化结果  

### 5. 样式与脚本文件  
- `chatbot.css`：用于美化前端聊天界面  
- `admin.css`：用于美化后台设置页面  
- `chatbot.js`：提供前端聊天交互功能

## Configuration After Installation

1. Navigate to **Settings > AI ChatBot** in your WordPress admin dashboard
2. Enter your Aliyun DashScope API Key and App ID
3. Configure optional settings as needed
4. Test the chatbot by adding the shortcode `[aliyun_chatbot]` to a page or post

## 安装后的配置

1. 在 WordPress 管理后台导航至 **设置 > AI ChatBot**  
2. 输入您的阿里云 DashScope API Key 和 App ID  
3. 根据需要配置可选设置项  
4. 在页面或文章中添加短代码 `[aliyun_chatbot]`，测试聊天机器人是否正常工作

## Customization

### CSS Customization
Edit the `chatbot.css` file to customize the appearance of the chat interface. The default file provides a responsive design that works well on most websites.

### JavaScript Customization
Edit the `chatbot.js` file to customize the behavior of the chat interface. The default file includes basic functionality for sending messages, displaying responses, and handling errors.

### Shortcode Customization
The plugin supports several attributes for the `[aliyun_chatbot]` shortcode:
- `title`: The title displayed at the top of the chatbot
- `placeholder`: Placeholder text for the input field
- `welcome_message`: Initial message from the chatbot
- `show_clear`: Show clear conversation button (yes/no)

### Additional Files
You can add additional CSS and JavaScript files by modifying the `enqueue_scripts` methods in the respective classes.

## 自定义设置

### CSS 样式自定义  
编辑 `chatbot.css` 文件，可自定义聊天界面的外观。默认样式具有良好的响应式设计，适配大多数网站。

### JavaScript 行为自定义  
编辑 `chatbot.js` 文件，可调整聊天界面的行为逻辑。默认脚本提供了发送消息、显示回复和处理错误的基础功能。

### 短代码属性自定义  
插件的 `[aliyun_chatbot]` 短代码支持多个属性，方便灵活配置：  
- `title`：聊天窗口顶部的标题  
- `placeholder`：输入框中的占位提示文本  
- `welcome_message`：聊天机器人启动时显示的欢迎语  
- `show_clear`：是否显示“清除对话”按钮（`yes` / `no`）

### 其他扩展文件  
如需添加额外的 CSS 或 JavaScript 文件，可在对应类中的 `enqueue_scripts` 方法中进行修改来加载这些资源。

## Debugging

When `WP_DEBUG` is enabled in your `wp-config.php` file, the plugin will:
1. Display additional debugging information on the settings page
2. Log API requests and responses to the WordPress debug log
3. Show a direct settings form that can sometimes resolve issues with the standard settings form

## 调试说明

当您在 `wp-config.php` 文件中启用 `WP_DEBUG` 后，插件将提供以下调试功能：

1. 在设置页面显示额外的调试信息  
2. 将 API 请求与响应记录到 WordPress 的调试日志中  
3. 显示一个直接设置表单，有时可以解决标准设置表单无法正常使用的问题