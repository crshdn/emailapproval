<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Client;
use App\Models\Campaign;
use App\Models\SubjectLine;
use App\Models\EmailBody;
use App\Models\ApprovalHistory;

class ClientController
{
    public function portal(string $token): void
    {
        $clientModel = new Client();
        $client = $clientModel->findByToken($token);
        
        if (!$client) {
            http_response_code(404);
            include __DIR__ . '/../views/404.php';
            return;
        }

        $campaignModel = new Campaign();
        $campaigns = $campaignModel->findByClientId($client['id']);
        
        // Store client info in session for CSRF and API calls
        $_SESSION['client_token'] = $token;
        $_SESSION['client_id'] = $client['id'];
        
        // Generate CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrfToken = $_SESSION['csrf_token'];
        
        include __DIR__ . '/../views/client/portal.php';
    }

    public function campaign(string $token, string $campaignId): void
    {
        $clientModel = new Client();
        $client = $clientModel->findByToken($token);
        
        if (!$client) {
            http_response_code(404);
            include __DIR__ . '/../views/404.php';
            return;
        }

        $campaignModel = new Campaign();
        $campaign = $campaignModel->findByIdAndClient((int) $campaignId, $client['id']);
        
        if (!$campaign) {
            header("Location: /portal/{$token}");
            exit;
        }

        $subjectModel = new SubjectLine();
        $emailModel = new EmailBody();
        
        // Get pending items for review
        $pendingSubjects = $subjectModel->findPendingByCampaignId((int) $campaignId);
        $pendingEmails = $emailModel->findPendingByCampaignId((int) $campaignId);
        
        // Get first pending item of each type for one-at-a-time review
        $currentSubject = $subjectModel->findFirstPendingByCampaignId((int) $campaignId);
        $currentEmail = $emailModel->findFirstPendingByCampaignId((int) $campaignId);
        
        // All items for reference
        $allSubjects = $subjectModel->findByCampaignId((int) $campaignId);
        $allEmails = $emailModel->findByCampaignId((int) $campaignId);
        
        // Store client info in session
        $_SESSION['client_token'] = $token;
        $_SESSION['client_id'] = $client['id'];
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrfToken = $_SESSION['csrf_token'];
        
        include __DIR__ . '/../views/client/campaign.php';
    }

    public function history(string $token): void
    {
        $clientModel = new Client();
        $client = $clientModel->findByToken($token);
        
        if (!$client) {
            http_response_code(404);
            include __DIR__ . '/../views/404.php';
            return;
        }

        $historyModel = new ApprovalHistory();
        $history = $historyModel->findByClientId($client['id']);
        
        // Store client info in session
        $_SESSION['client_token'] = $token;
        $_SESSION['client_id'] = $client['id'];
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrfToken = $_SESSION['csrf_token'];
        
        include __DIR__ . '/../views/client/history.php';
    }
}

