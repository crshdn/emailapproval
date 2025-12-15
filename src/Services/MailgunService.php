<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use Mailgun\Mailgun;
use PDO;
use Exception;

class MailgunService
{
    private ?Mailgun $mailgun = null;
    private string $domain;
    private string $fromEmail;
    private string $fromName;
    private string $adminEmail;
    private PDO $db;

    public function __construct()
    {
        $apiKey = $_ENV['MAILGUN_API_KEY'] ?? '';
        $this->domain = $_ENV['MAILGUN_DOMAIN'] ?? '';
        $this->fromEmail = $_ENV['MAILGUN_FROM_EMAIL'] ?? 'noreply@example.com';
        $this->fromName = $_ENV['MAILGUN_FROM_NAME'] ?? 'Email Approval System';
        $this->adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
        $region = $_ENV['MAILGUN_REGION'] ?? 'us';
        $this->db = Database::getConnection();

        if (!empty($apiKey)) {
            // Use EU endpoint if region is set to 'eu'
            $endpoint = ($region === 'eu') ? 'https://api.eu.mailgun.net' : 'https://api.mailgun.net';
            $this->mailgun = Mailgun::create($apiKey, $endpoint);
        }
    }

    public function sendClientLink(string $clientEmail, string $clientName, string $portalUrl): bool
    {
        $subject = "Your Email Approval Portal Link";
        
        ob_start();
        include __DIR__ . '/../views/emails/client-link.php';
        $html = ob_get_clean();

        return $this->send($clientEmail, $subject, $html, 'client_link');
    }

    public function sendAdminAlert(
        string $clientName,
        string $campaignName,
        string $itemType,
        string $action,
        string $itemPreview,
        ?string $feedback = null
    ): bool {
        $subject = "[{$action}] {$clientName} - {$campaignName} - " . ucfirst(str_replace('_', ' ', $itemType));
        
        ob_start();
        include __DIR__ . '/../views/emails/admin-alert.php';
        $html = ob_get_clean();

        return $this->send($this->adminEmail, $subject, $html, 'admin_alert');
    }

    public function sendNewContentNotification(string $clientEmail, string $clientName, string $portalUrl, int $pendingCount): bool
    {
        $subject = "New Content Awaiting Your Approval";
        
        ob_start();
        include __DIR__ . '/../views/emails/new-content.php';
        $html = ob_get_clean();

        return $this->send($clientEmail, $subject, $html, 'client_link');
    }

    private function send(string $to, string $subject, string $html, string $emailType): bool
    {
        if ($this->mailgun === null) {
            $this->logEmail($to, $emailType, $subject, 'failed', 'Mailgun not configured');
            return false;
        }

        try {
            $this->mailgun->messages()->send($this->domain, [
                'from' => "{$this->fromName} <{$this->fromEmail}>",
                'to' => $to,
                'subject' => $subject,
                'html' => $html,
            ]);

            $this->logEmail($to, $emailType, $subject, 'sent');
            return true;
        } catch (Exception $e) {
            $this->logEmail($to, $emailType, $subject, 'failed', $e->getMessage());
            error_log("Mailgun error: " . $e->getMessage());
            return false;
        }
    }

    private function logEmail(string $recipient, string $emailType, string $subject, string $status, ?string $errorMessage = null): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO email_log (recipient_email, email_type, subject, status, error_message, sent_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$recipient, $emailType, $subject, $status, $errorMessage]);
    }

    public function isConfigured(): bool
    {
        return $this->mailgun !== null && !empty($this->domain) && !empty($this->adminEmail);
    }
}

