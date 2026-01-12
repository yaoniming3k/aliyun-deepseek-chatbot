/**
 * Admin JavaScript for Aliyun DeepSeek ChatBot
 *
 * Handles dynamic showing/hiding of configuration options based on API mode
 *
 * @since 1.2.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Get API mode select element
        var $apiModeSelect = $('#aliyun_chatbot_api_mode');

        if ($apiModeSelect.length === 0) {
            return;
        }

        // Fields that are only relevant for Model API mode
        var modelOnlyFields = [
            'aliyun_chatbot_model',
            'aliyun_chatbot_temperature',
            'aliyun_chatbot_max_tokens',
            'aliyun_chatbot_system_message',
            'aliyun_chatbot_api_endpoint'
        ];

        // Fields that are only relevant for Agent API mode
        var agentOnlyFields = [
            'aliyun_chatbot_app_id',
            'aliyun_chatbot_workspace_id'
        ];

        /**
         * Show or hide fields based on API mode
         */
        function toggleFieldsByMode() {
            var apiMode = $apiModeSelect.val();

            console.log('API Mode changed to:', apiMode);

            if (apiMode === 'model') {
                // Model API mode: Show model-specific fields, hide agent fields
                modelOnlyFields.forEach(function(fieldId) {
                    var $field = $('#' + fieldId);
                    if ($field.length > 0) {
                        $field.closest('tr').show();
                    }
                });

                agentOnlyFields.forEach(function(fieldId) {
                    var $field = $('#' + fieldId);
                    if ($field.length > 0) {
                        $field.closest('tr').hide();
                    }
                });

                // Update API endpoint description
                var $endpointField = $('#aliyun_chatbot_api_endpoint');
                if ($endpointField.length > 0) {
                    $endpointField.closest('tr').find('.description').html(
                        'Model API endpoint. Default: <code>https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions</code>'
                    );
                }

            } else if (apiMode === 'agent') {
                // Agent API mode: Show agent-specific fields, hide model fields
                agentOnlyFields.forEach(function(fieldId) {
                    var $field = $('#' + fieldId);
                    if ($field.length > 0) {
                        $field.closest('tr').show();
                    }
                });

                modelOnlyFields.forEach(function(fieldId) {
                    var $field = $('#' + fieldId);
                    if ($field.length > 0) {
                        // Hide endpoint field completely in agent mode (auto-generated)
                        if (fieldId === 'aliyun_chatbot_api_endpoint') {
                            $field.closest('tr').hide();
                        } else {
                            $field.closest('tr').hide();
                        }
                    }
                });
            }

            // Add visual indicator
            updateModeIndicator(apiMode);
        }

        /**
         * Add a visual indicator for the current mode
         */
        function updateModeIndicator(mode) {
            // Remove existing indicator
            $('.aliyun-mode-indicator').remove();

            var indicatorHtml = '';
            var indicatorClass = 'notice notice-info inline';

            if (mode === 'model') {
                indicatorHtml = '<div class="aliyun-mode-indicator ' + indicatorClass + '" style="margin: 15px 0;"><p>' +
                    '<strong>Model API Mode</strong>: You are using direct model API. ' +
                    'Configure model, temperature, and system message below. ' +
                    'Perfect for simple Q&A scenarios.' +
                    '</p></div>';
            } else if (mode === 'agent') {
                indicatorHtml = '<div class="aliyun-mode-indicator ' + indicatorClass + '" style="margin: 15px 0;"><p>' +
                    '<strong>Application API Mode</strong>: You are using application (Agent/Workflow) API. ' +
                    'Configure your application in the <a href="https://bailian.console.aliyun.com" target="_blank">Aliyun Bailian Console</a>. ' +
                    'Supports RAG, tools, and multi-turn conversations managed by cloud.' +
                    '</p></div>';
            }

            // Insert indicator after API mode field
            $apiModeSelect.closest('tr').after(indicatorHtml);
        }

        // Initial toggle on page load
        toggleFieldsByMode();

        // Toggle fields when API mode changes
        $apiModeSelect.on('change', function() {
            toggleFieldsByMode();
        });

        /**
         * Add helpful hints for App ID field
         */
        var $appIdField = $('#aliyun_chatbot_app_id');
        if ($appIdField.length > 0) {
            var $appIdRow = $appIdField.closest('tr');
            var existingDesc = $appIdRow.find('.description').html();

            $appIdRow.find('.description').html(
                existingDesc + '<br>' +
                '<a href="https://bailian.console.aliyun.com/#/app-center" target="_blank" rel="noopener noreferrer">' +
                'Get App ID from Aliyun Bailian Console' +
                '</a>'
            );
        }

        /**
         * Add validation before form submit
         */
        $('form[action="options.php"]').on('submit', function(e) {
            var apiMode = $apiModeSelect.val();
            var apiKey = $('#aliyun_chatbot_api_key').val().trim();

            // Check API key
            if (apiKey === '') {
                e.preventDefault();
                alert('Please enter your API Key.');
                $('#aliyun_chatbot_api_key').focus();
                return false;
            }

            // Check App ID for agent mode
            if (apiMode === 'agent') {
                var appId = $('#aliyun_chatbot_app_id').val().trim();
                if (appId === '') {
                    e.preventDefault();
                    alert('Application API mode requires an App ID. Please enter your App ID or switch to Model API mode.');
                    $('#aliyun_chatbot_app_id').focus();
                    return false;
                }
            }

            return true;
        });

        /**
         * Sync grouped model select with model input field.
         */
        var $modelSelect = $('#aliyun_chatbot_model_select');
        var $modelInput = $('#aliyun_chatbot_model');

        if ($modelSelect.length && $modelInput.length) {
            var currentModel = $modelInput.val();
            if (currentModel && $modelSelect.find('option[value="' + currentModel + '"]').length) {
                $modelSelect.val(currentModel);
            }

            $modelSelect.on('change', function() {
                var value = $(this).val();
                if (value) {
                    $modelInput.val(value);
                }
            });
        }
    });

})(jQuery);
