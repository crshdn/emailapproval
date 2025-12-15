<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Client;
use App\Models\Campaign;
use App\Models\SubjectLine;
use App\Models\EmailBody;
use App\Models\ApprovalHistory;
use App\Services\MailgunService;

class ApiController
{
    private function validateRequest(): ?array
    {
        header('Content-Type: application/json');
        
        // Check CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid request token']);
            return null;
        }
        
        // Validate client token
        $clientToken = $_SESSION['client_token'] ?? '';
        $clientId = $_SESSION['client_id'] ?? 0;
        
        if (empty($clientToken) || $clientId === 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return null;
        }
        
        $clientModel = new Client();
        $client = $clientModel->findByToken($clientToken);
        
        if (!$client || $client['id'] !== $clientId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid session']);
            return null;
        }
        
        return $client;
    }

    public function approve(): void
    {
        $client = $this->validateRequest();
        if (!$client) {
            return;
        }
        
        $itemType = $_POST['item_type'] ?? '';
        $itemId = (int) ($_POST['item_id'] ?? 0);
        
        if (!in_array($itemType, ['subject_line', 'email_body']) || $itemId === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }
        
        // Verify the item belongs to this client
        if ($itemType === 'subject_line') {
            $model = new SubjectLine();
            $item = $model->findById($itemId);
        } else {
            $model = new EmailBody();
            $item = $model->findById($itemId);
        }
        
        if (!$item || $item['client_id'] !== $client['id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        // Update status
        $model->updateStatus($itemId, 'approved');
        
        // Record in history
        $historyModel = new ApprovalHistory();
        $historyModel->create(
            $itemType,
            $itemId,
            'approved',
            $client['id'],
            $item['revision_number']
        );
        
        // Send admin notification
        $mailer = new MailgunService();
        $itemPreview = $itemType === 'subject_line' ? $item['subject_text'] : substr(strip_tags($item['body_content']), 0, 200);
        $mailer->sendAdminAlert(
            $client['name'],
            $item['campaign_name'],
            $itemType,
            'Approved',
            $itemPreview
        );
        
        echo json_encode([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $itemType)) . ' approved successfully'
        ]);
    }

    public function deny(): void
    {
        $client = $this->validateRequest();
        if (!$client) {
            return;
        }
        
        $itemType = $_POST['item_type'] ?? '';
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $feedback = trim($_POST['feedback'] ?? '');
        
        if (!in_array($itemType, ['subject_line', 'email_body']) || $itemId === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }
        
        // Verify the item belongs to this client
        if ($itemType === 'subject_line') {
            $model = new SubjectLine();
            $item = $model->findById($itemId);
        } else {
            $model = new EmailBody();
            $item = $model->findById($itemId);
        }
        
        if (!$item || $item['client_id'] !== $client['id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        // Update status
        $model->updateStatus($itemId, 'denied');
        
        // Record in history
        $historyModel = new ApprovalHistory();
        $historyModel->create(
            $itemType,
            $itemId,
            'denied',
            $client['id'],
            $item['revision_number'],
            $feedback ?: null
        );
        
        // Send admin notification
        $mailer = new MailgunService();
        $itemPreview = $itemType === 'subject_line' ? $item['subject_text'] : substr(strip_tags($item['body_content']), 0, 200);
        $mailer->sendAdminAlert(
            $client['name'],
            $item['campaign_name'],
            $itemType,
            'Denied',
            $itemPreview,
            $feedback ?: null
        );
        
        echo json_encode([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $itemType)) . ' denied'
        ]);
    }
}

