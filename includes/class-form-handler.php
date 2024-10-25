<?php
/**
 * Class CFP_Form_Handler
 * 
 * Handles form submissions and email sending for the Custom Form Plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CFP_Form_Handler
{
    private $email_manager;
    private $settings_manager;
    private $settings;

    public function __construct($settings_manager, $email_manager)
    {
        // Assign the settings and email manager to the class properties
        $this->settings_manager = $settings_manager;
        $this->email_manager = $email_manager;

        // Load settings once to reduce repeated calls
        $this->settings = get_option($this->settings_manager->get_options_name());

        // Hook for handling AJAX form submissions
        add_action('wp_ajax_nopriv_submit_quote_form', [$this, 'handle_form_submission']);
        add_action('wp_ajax_submit_quote_form', [$this, 'handle_form_submission']);
        // Hook for the email sending event
        add_action('cqf_send_email_event', [$this, 'send_email']);
    }

    public function handle_form_submission()
    {
        // Verify nonce for security
        if (!isset($_POST['cfp_nonce']) || !wp_verify_nonce($_POST['cfp_nonce'], 'cfp_form_nonce')) {
            wp_send_json_error(['message' => 'Nonce verification failed.'], 403);
            wp_die();
        }

        // Retrieve form data and sanitize
        $fullName = sanitize_text_field($_POST['cfp_name']);
        $email = sanitize_email($_POST['cfp_email']);
        $mobile = sanitize_text_field($_POST['cfp_phone']);
        $pin = sanitize_text_field($_POST['cfp_postcode']);

        // Validate email
        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Invalid email address.'], 400);
            wp_die();
        }

        // Prepare email subject and message
        $new_quote_subject_template = $this->settings_manager->get_new_quote_subject();
        $subject = sprintf($new_quote_subject_template, $fullName);
        $message = CFP_Email_Template_Manager::build_quote_email_body($fullName, $email, $mobile, $pin);

        // Get admin email from settings
        $admin_email = $this->settings['email_recipient'] ?? $this->settings_manager->get_admin_email(); // Fallback to default admin email
        
        // Schedule email to be sent to admin
        wp_schedule_single_event(time(), 'cqf_send_email_event', [
            'to' => $admin_email,
            'subject' => $subject,
            'message' => $message,
        ]);

        // Send thank-you email to user
        $thank_you_subject = $this->settings_manager->get_thank_you_subject();
        $thank_you_message = $this->settings_manager->get_thank_you_message();
        $thank_you_sent = $this->email_manager->send_email($email, $thank_you_subject, $thank_you_message);

        if (!$thank_you_sent) {
            wp_send_json_error(['message' => 'Failed to send thank you email.'], 500);
            wp_die();
        }

        // Return a success response
        wp_send_json_success(['message' => 'Form submitted successfully!']);
        wp_die(); // Always die in AJAX handlers
    }

    public function send_email($to, $subject, $message)
    {
        // Send the email
        $email_sent = $this->email_manager->send_email($to, $subject, $message);

        if (!$email_sent) {
            error_log('Failed to send email to ' . $to);
        }
    }

    public function render_form() {
        ob_start();
        include CFP_PLUGIN_PATH . 'templates/form-template.php'; // Adjust the path if necessary
        return ob_get_clean();
    }
}
