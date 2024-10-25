<?php
/**
 * Class CFP_Email_Template_Manager
 * 
 * Handles the email template for the Custom Form Plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CFP_Email_Template_Manager {

    public static function build_quote_email_body($fullName, $email, $mobile, $pin) {
        return "
        <table width='80%' align='center' cellpadding='0' cellspacing='0' style='font-family:Verdana, Geneva, sans-serif; font-size:13px; border:dotted 1px #186ea9; border-bottom:solid 3px #0037ff;'>
            <tr>
                <td style='background:#0037ff; padding:10px 0; font-weight:bold; color:#fff; font-size:16px;' colspan='2' align='center' valign='middle'>Homepage Contact Form Quote WP</td>
            </tr>
            <tr>
                <td width='50%' style='background:#ffffff; padding:10px; border-right:solid 1px #ddd; font-weight:bold; color:#555;'>Full Name</td>
                <td width='50%' style='background:#ffffff; padding:10px; border-right:solid 1px #ddd; color:#555;'>$fullName</td>
            </tr>
            <tr>
                <td width='50%' style='background:#f2f2f2; padding:10px; border-right:solid 1px #ddd; font-weight:bold; color:#555;'>Email Address</td>
                <td width='50%' style='background:#f2f2f2; padding:10px; border-right:solid 1px #ddd; color:#555;'>$email</td>
            </tr>
            <tr>
                <td width='50%' style='background:#ffffff; padding:10px; border-right:solid 1px #ddd; font-weight:bold; color:#555;'>Mobile Number</td>
                <td width='50%' style='background:#ffffff; padding:10px; border-right:solid 1px #ddd; color:#555;'>$mobile</td>
            </tr>
            <tr>
                <td width='50%' style='background:#f2f2f2; padding:10px; border-right:solid 1px #ddd; font-weight:bold; color:#555;'>Postal Code</td>
                <td width='50%' style='background:#f2f2f2; padding:10px; border-right:solid 1px #ddd; font-weight:bold; color:#555;'>$pin</td>
            </tr>
        </table>";
    }
}