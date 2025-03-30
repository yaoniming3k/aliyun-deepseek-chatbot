# Implementation Guide for Aliyun DeepSeek ChatBot

This document explains how to implement the restructured plugin from the separate files.

## File Structure

Create a directory structure as follows:

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

1. Download zip file from Github.
2. Install it as what you regular do to Wordpress plugin installation by wordpress dashboard.

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

## Configuration After Installation

1. Navigate to **Settings > AI ChatBot** in your WordPress admin dashboard
2. Enter your Aliyun DashScope API Key and App ID
3. Configure optional settings as needed
4. Test the chatbot by adding the shortcode `[aliyun_chatbot]` to a page or post

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

## Debugging

When `WP_DEBUG` is enabled in your `wp-config.php` file, the plugin will:
1. Display additional debugging information on the settings page
2. Log API requests and responses to the WordPress debug log
3. Show a direct settings form that can sometimes resolve issues with the standard settings form