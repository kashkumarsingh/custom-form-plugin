<?php
/**
 * Class CFP_Settings_Manager
 * 
 * Manages admin settings for the Custom Form Plugin.
 * 
 * SOLID principles:
 * - Single Responsibility: Responsible only for managing settings.
 * - Open/Closed: Open for extension without modifying core methods.
 * - Encapsulation: Private properties and controlled access.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CFP_Settings_Manager {
    private $settings_group = 'cfp_plugin_settings';
    private $options_name   = 'cfp_plugin_options';

    public function __construct() {
        add_action('admin_menu', [$this, 'cfpAddSettingsPage']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Adds a settings page under the Options menu.
     */
    public function cfpAddSettingsPage() {
        add_options_page(
            'Custom Form Plugin Settings',
            'Form Plugin Settings',
            'manage_options',
            'cfp-settings', // Ensure this slug is unique to avoid duplication
            [$this, 'cfpRenderSettingsPage']
        );
    }

    /**
     * Registers plugin settings and fields.
     */
    public function register_settings() {
        register_setting($this->settings_group, $this->options_name, [$this, 'cfpSanitizeSettings']);

        // SMTP Section
        add_settings_section(
            'cfp_smtp_section',
            'SMTP Configuration',
            [$this, 'cfpSmtpSectionCallback'],
            'cfp-settings'
        );

        // SMTP fields
        $this->cfpAddSettingsField('smtp_host', 'SMTP Host', 'text', 'cfp_smtp_section');
        $this->cfpAddSettingsField('smtp_port', 'SMTP Port', 'number', 'cfp_smtp_section');
        $this->cfpAddSettingsField('smtp_user', 'SMTP Username', 'text', 'cfp_smtp_section');
        $this->cfpAddSettingsField('smtp_pass', 'SMTP Password', 'password', 'cfp_smtp_section');
        $this->cfpAddSettingsField('smtp_encryption', 'SMTP Encryption', 'text', 'cfp_smtp_section');
        $this->cfpAddSettingsField('email_from_name', 'Email From Name', 'text', 'cfp_smtp_section');
        $this->cfpAddSettingsField('email_from_address', 'Email From Address', 'email', 'cfp_smtp_section');
        $this->cfpAddSettingsField('email_recipient', 'Recipient Email Address', 'email', 'cfp_smtp_section');

        // Additional Email Settings Section
        add_settings_section(
            'cfp_email_section',
            'Email Settings',
            [$this, 'cfpEmailSectionCallback'],
            'cfp-settings'
        );

        // Email fields
        $this->cfpAddSettingsField('new_quote_subject', 'New Quote Request Email Subject', 'text', 'cfp_email_section');
        $this->cfpAddSettingsField('thank_you_subject', 'Thank You Email Subject', 'text', 'cfp_email_section');
        $this->cfpAddSettingsField('thank_you_message', 'Thank You Email Message', 'textarea', 'cfp_email_section');
    }

    /**
     * Renders the settings page.
     */
    public function cfpRenderSettingsPage() {
        if (!current_user_can('manage_options')) return;

        // Separate HTML rendering logic
        echo $this->getSettingsPageHtml();
    }

    /**
     * Returns the HTML content for the settings page.
     */
    private function getSettingsPageHtml() {
        ob_start();
        ?>
        <div class="wrap">
            <h1>Custom Form Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields($this->settings_group);
                    do_settings_sections('cfp-settings');
                    submit_button();
                ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Helper function to add a settings field.
     */
    private function cfpAddSettingsField($id, $title, $type, $section) {
        add_settings_field(
            $id,
            $title,
            [$this, 'cfpRenderField'],
            'cfp-settings',
            $section,
            ['label_for' => $id, 'type' => $type, 'name' => $id]
        );
    }

    /**
     * Callback for SMTP section description.
     */
    public function cfpSmtpSectionCallback() {
        echo '<p>Configure SMTP settings for email functionality.</p>';
    }

    /**
     * Callback for Email section description.
     */
    public function cfpEmailSectionCallback() {
        echo '<p>Configure email settings for notifications and responses.</p>';
    }

    /**
     * Renders the input fields based on type.
     */
    public function cfpRenderField($args) {
        $options = get_option($this->options_name);
        $value = isset($options[$args['name']]) ? esc_attr($options[$args['name']]) : '';
        if ($args['type'] === 'textarea') {
            echo '<textarea name="' . esc_attr($this->options_name) . '[' . esc_attr($args['name']) . ']" rows="5" cols="50">' . $value . '</textarea>';
        } else {
            echo '<input type="' . esc_attr($args['type']) . '" id="' . esc_attr($args['name']) . '" name="' . esc_attr($this->options_name) . '[' . esc_attr($args['name']) . ']" value="' . $value . '" class="regular-text">';
        }
    }

    /**
     * Sanitizes the plugin settings before saving.
     */
    public function cfpSanitizeSettings($input) {
        $sanitized_input = [];

        // Sanitize SMTP settings
        $sanitized_input['smtp_host'] = sanitize_text_field($input['smtp_host']);
        $sanitized_input['smtp_port'] = absint($input['smtp_port']);
        $sanitized_input['smtp_user'] = sanitize_text_field($input['smtp_user']);
        $sanitized_input['smtp_pass'] = sanitize_text_field($input['smtp_pass']);
        $sanitized_input['smtp_encryption'] = sanitize_text_field($input['smtp_encryption']);
        $sanitized_input['email_from_name'] = sanitize_text_field($input['email_from_name']);
        $sanitized_input['email_from_address'] = sanitize_email($input['email_from_address']);
        $sanitized_input['email_recipient'] = sanitize_email($input['email_recipient']);
        
        // Sanitize additional email settings
        $sanitized_input['new_quote_subject'] = sanitize_text_field($input['new_quote_subject']);
        $sanitized_input['thank_you_subject'] = sanitize_text_field($input['thank_you_subject']);
        $sanitized_input['thank_you_message'] = sanitize_textarea_field($input['thank_you_message']);
        
        return $sanitized_input;
    }

    /**
     * Retrieves the admin email.
     */
    public function get_admin_email() {
        return get_option('admin_email');
    }

    /**
     * Retrieves the options name.
     */
    public function get_options_name() {
        return $this->options_name;
    }

    /**
     * Retrieves the new quote subject.
     */
    public function get_new_quote_subject() {
        $options = get_option($this->options_name);
        return isset($options['new_quote_subject']) ? $options['new_quote_subject'] : 'New Quote Request from %s';
    }

    /**
     * Retrieves the thank you email subject.
     */
    public function get_thank_you_subject() {
        $options = get_option($this->options_name);
        return isset($options['thank_you_subject']) ? $options['thank_you_subject'] : 'Thank You for Your Quote Request';
    }

    /**
     * Retrieves the thank you email message.
     */
    public function get_thank_you_message() {
        $options = get_option($this->options_name);
        return isset($options['thank_you_message']) ? $options['thank_you_message'] : 'We appreciate your request. We will get back to you shortly.';
    }
}
