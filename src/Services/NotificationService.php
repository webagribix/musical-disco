<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Alert;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class NotificationService
{
    public function sendEmail(string $to, string $subject, string $body): void
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST']     ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@poultryos.ke', $_ENV['MAIL_FROM_NAME'] ?? APP_NAME);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->isHTML(false);
            $mail->send();
        } catch (MailException $e) {
            error_log("Mail error: " . $e->getMessage());
        }
    }

    public function sendSMS(string $phone, string $message): void
    {
        $apiKey   = $_ENV['AT_API_KEY']  ?? '';
        $username = $_ENV['AT_USERNAME'] ?? 'sandbox';
        if (!$apiKey) return;

        $url  = 'https://api.africastalking.com/version1/messaging';
        $data = http_build_query([
            'username' => $username,
            'to'       => $phone,
            'message'  => $message,
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "apiKey: $apiKey\r\nContent-Type: application/x-www-form-urlencoded\r\n",
                'content' => $data,
            ],
        ]);

        @file_get_contents($url, false, $ctx);
    }

    public function createInAppAlert(int $batchId, string $type, string $message): void
    {
        // Avoid duplicate alerts within 1 hour
        $existing = \App\Core\DB::getInstance()->selectWhere(
            'alerts',
            'batch_id = ? AND type = ? AND is_resolved = 0 AND created_at >= ?',
            [$batchId, $type, date('Y-m-d H:i:s', strtotime('-1 hour'))]
        );

        if (!empty($existing)) return;

        Alert::create([
            'batch_id'    => $batchId,
            'type'        => $type,
            'message'     => $message,
            'is_resolved' => 0,
        ]);
    }
}
