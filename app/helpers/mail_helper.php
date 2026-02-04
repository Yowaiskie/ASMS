<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APPROOT . '/../vendor/autoload.php';

function sendEmailNotification($to, $subject, $title, $message, $actionText = 'Go to Dashboard', $actionUrl = 'http://localhost/ASMS/public/login') {
    $mail = new PHPMailer(true);

    if (empty($to)) {
        error_log("PHPMailer Error: No recipient email provided.");
        return false;
    }

    try {
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Force UTF-8
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Attachments (Embed Logo)
        $logoPath = APPROOT . '/../public/images/logo.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'logo_img');
            $logoHtml = "<img src='cid:logo_img' alt='ASMS Logo' style='width: 80px; height: 80px; margin-bottom: 20px;'>";
        } else {
            $logoHtml = "<h1 style='color:white; margin-bottom:20px;'>ASMS</h1>";
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;

        $mail->Body = "
        <div style='font-family: sans-serif; background-color: #f8fafc; padding: 40px; color: #334155;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);'>
                <div style='background-color: #1e63d4; padding: 40px; text-align: center;'>
                    {$logoHtml}
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>ASMS Notification</h1>
                </div>
                <div style='padding: 40px;'>
                    <h2 style='color: #1e293b; margin-top: 0;'>{$title}</h2>
                    <p style='line-height: 1.6; color: #64748b;'>{$message}</p>
                    <div style='margin-top: 32px; text-align: center;'>
                        <a href='{$actionUrl}' style='background-color: #1e63d4; color: #ffffff; padding: 12px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; display: inline-block;'>
                            {$actionText}
                        </a>
                    </div>
                </div>
                <div style='padding: 20px; background-color: #f1f5f9; text-align: center; font-size: 12px; color: #94a3b8;'>
                    <p>This is an automated message from the Altar Servers Management System.</p>
                    <p>&copy; " . date('Y') . " ASMS. All rights reserved.</p>
                </div>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}