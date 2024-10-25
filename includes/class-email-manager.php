<?php
/**
 * Class CFP_Email_Manager
 * 
 * This class handles the email sending functionality for the Custom Form Plugin.
 * 
 * SOLID principles are applied:
 * - Single Responsibility: This class is responsible solely for sending emails.
 * - Open/Closed: The class can be extended for new email functionalities without modifying the existing code.
 * - Liskov Substitution: Child classes (if any) can replace the parent class without affecting the functionality.
 * - Interface Segregation: This class does not implement unnecessary interfaces.
 * - Dependency Inversion: High-level modules do not depend on low-level modules; both depend on abstractions.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CFP_Email_Manager
{
    private $options;

    /**
     * Constructor to initialize the options.
     */
    public function __construct()
    {
        // Fetch plugin options for email settings
        $this->options = get_option('cfp_plugin_options');

        // Ensure PHPMailer is available from WordPress core
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
        }
    }

    /**
     * Send an email using the SMTP settings.
     *
     * @param string $to Recipient email address.
     * @param string $subject Subject of the email.
     * @param string $message Message body of the email.
     * @return bool True if email is sent successfully, false otherwise.
     */
    public function send_email($to, $subject, $message)
    {
        $mail = new PHPMailer(true); // Enable exceptions in PHPMailer

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = $this->options['smtp_host'];
            $mail->SMTPAuth   = false;
            $mail->Username   = $this->options['smtp_user'];
            $mail->Password   = $this->options['smtp_pass'];
           // $mail->SMTPSecure = $this->options['smtp_encryption']; // 'tls' or 'ssl'
            $mail->Port       = $this->options['smtp_port'];

            // Email content
            $mail->setFrom($this->options['email_recipient']);
            $mail->addAddress($to); // Add recipient
            $mail->Subject = $subject;
            $mail->Body    = $message;

            // Attempt to send the email
            return $mail->send();
        } catch (Exception $e) {
            // Log the error if email sending fails
            error_log('PHPMailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Get the current SMTP options.
     * 
     * @return array SMTP options.
     */
    public function get_options()
    {
        return $this->options;
    }
}