<?php
/**
 * Plugin Name: Custom Form Plugin
 * Description: A custom plugin for handling quote requests.
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CFP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CFP_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include necessary classes
require_once CFP_PLUGIN_PATH . 'includes/class-email-manager.php';
require_once CFP_PLUGIN_PATH . 'includes/class-form-handler.php';
require_once CFP_PLUGIN_PATH . 'includes/class-settings-manager.php';
require_once CFP_PLUGIN_PATH . 'includes/class-email-template-manager.php';

class Custom_Form_Plugin {
    protected $settings_manager;
    protected $form_handler;
    protected $email_manager;

    public function __construct() {
        // Initialize settings and email manager
        $this->settings_manager = new CFP_Settings_Manager();
        $this->email_manager = new CFP_Email_Manager();

        // Pass both managers to the form handler
        $this->form_handler = new CFP_Form_Handler($this->settings_manager, $this->email_manager);

        // Hooking into WordPress actions
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_shortcode('custom_quote_form', [$this->form_handler, 'render_form']); // Ensure this is correct

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        // Enqueue the main JavaScript file for handling form submission
        wp_enqueue_script('custom-form-plugin-main', CFP_PLUGIN_URL . 'assets/src/js/form-handler.js', ['jquery'], null, true);

        // Localize script to pass the Ajax URL and nonce
        wp_localize_script('custom-form-plugin-main', 'cqf_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'quote_nonce' => wp_create_nonce('cfp_form_nonce') // Create a nonce for security
        ]);

        // Optionally, enqueue styles if needed
        wp_enqueue_style('custom-form-plugin-style', CFP_PLUGIN_URL . 'assets/build/main.css');
    }

    public function add_admin_menu() {
        // Admin menu logic can be added here if needed
    }
}

// Initialize the plugin
new Custom_Form_Plugin();
