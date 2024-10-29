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

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
    require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
    require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
}

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
        if (empty($message)) {
            error_log('Message body is empty. Email not sent.');
            return false; // or handle the error as needed
        }
    
        $mail = new PHPMailer(true); // Use PHPMailer from WordPress
    
        try {
            $mail->isSMTP();
            $mail->Host = $this->options['smtp_host'] ?? 'localhost';
            $mail->SMTPAuth = true;
            $mail->Username = $this->options['smtp_user'] ?? ''; // SMTP username
            $mail->Password = $this->options['smtp_pass'] ?? ''; // SMTP password
            $mail->SMTPSecure = $this->options['smtp_encryption'] ?? ''; // 'tls' or 'ssl'
            $mail->Port = $this->options['smtp_port'] ?? 25;
    
            // Recipients
            $mail->setFrom(
                $this->options['email_from_address'] ?? 'no-reply@meraboiler.com',
                $this->options['email_from_name'] ?? 'Meraboiler'
            );
            $mail->addAddress($to); //now ask him to submit again
    
            // Set email format to HTML
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message; // This needs to be a non-empty string
    
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
