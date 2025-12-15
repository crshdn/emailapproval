<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\MailgunService;
use App\Models\Client;
use App\Models\Campaign;
use App\Models\SubjectLine;
use App\Models\EmailBody;
use App\Models\ApprovalHistory;

class AdminController
{
    private AuthService $auth;
    private MailgunService $mailer;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->mailer = new MailgunService();
    }

    public function home(): void
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: /admin');
        } else {
            header('Location: /admin/login');
        }
        exit;
    }

    public function loginForm(): void
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: /admin');
            exit;
        }
        
        $csrfToken = $this->auth->generateCsrfToken();
        include __DIR__ . '/../views/admin/login.php';
    }

    public function login(): void
    {
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $error = 'Invalid request. Please try again.';
            $csrfToken = $this->auth->generateCsrfToken();
            include __DIR__ . '/../views/admin/login.php';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $result = $this->auth->login($username, $password, $ipAddress);

        if ($result['success']) {
            header('Location: /admin');
            exit;
        }

        $error = $result['message'];
        $csrfToken = $this->auth->generateCsrfToken();
        include __DIR__ . '/../views/admin/login.php';
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: /admin/login');
        exit;
    }

    public function dashboard(): void
    {
        $this->auth->requireAuth();
        
        $clientModel = new Client();
        $historyModel = new ApprovalHistory();
        
        $clients = $clientModel->findAll();
        $recentActivity = $historyModel->getRecentActivity(20);
        $csrfToken = $this->auth->generateCsrfToken();
        
        // Calculate totals
        $totalPending = 0;
        $totalApproved = 0;
        foreach ($clients as $client) {
            $totalPending += $client['pending_subjects'] + $client['pending_emails'];
        }
        
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    public function clients(): void
    {
        $this->auth->requireAuth();
        
        $clientModel = new Client();
        $clients = $clientModel->findAll();
        $csrfToken = $this->auth->generateCsrfToken();
        $appUrl = $_ENV['APP_URL'] ?? '';
        
        include __DIR__ . '/../views/admin/clients.php';
    }

    public function clientDetail(string $id): void
    {
        $this->auth->requireAuth();
        
        $clientModel = new Client();
        $campaignModel = new Campaign();
        
        $client = $clientModel->findById((int) $id);
        
        if (!$client) {
            header('Location: /admin/clients');
            exit;
        }
        
        $campaigns = $campaignModel->findByClientId((int) $id);
        $csrfToken = $this->auth->generateCsrfToken();
        $appUrl = $_ENV['APP_URL'] ?? '';
        
        include __DIR__ . '/../views/admin/client-detail.php';
    }

    public function createClient(): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($name) || empty($email)) {
            $_SESSION['error'] = 'Name and email are required.';
            header('Location: /admin/clients');
            exit;
        }

        $clientModel = new Client();
        $clientId = $clientModel->create($name, $email);

        $_SESSION['success'] = 'Client created successfully.';
        header("Location: /admin/clients/{$clientId}");
        exit;
    }

    public function updateClient(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header("Location: /admin/clients/{$id}");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $isActive = isset($_POST['is_active']);

        $clientModel = new Client();
        $clientModel->update((int) $id, $name, $email, $isActive);

        $_SESSION['success'] = 'Client updated successfully.';
        header("Location: /admin/clients/{$id}");
        exit;
    }

    public function regenerateToken(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header("Location: /admin/clients/{$id}");
            exit;
        }

        $clientModel = new Client();
        $clientModel->regenerateToken((int) $id);

        $_SESSION['success'] = 'Access link regenerated. The old link will no longer work.';
        header("Location: /admin/clients/{$id}");
        exit;
    }

    public function sendLink(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header("Location: /admin/clients/{$id}");
            exit;
        }

        $clientModel = new Client();
        $client = $clientModel->findById((int) $id);

        if (!$client) {
            $_SESSION['error'] = 'Client not found.';
            header('Location: /admin/clients');
            exit;
        }

        $appUrl = $_ENV['APP_URL'] ?? '';
        $portalUrl = "{$appUrl}/portal/{$client['access_token']}";

        if ($this->mailer->sendClientLink($client['email'], $client['name'], $portalUrl)) {
            $_SESSION['success'] = 'Portal link sent to client.';
        } else {
            $_SESSION['error'] = 'Failed to send email. Please check Mailgun configuration.';
        }

        header("Location: /admin/clients/{$id}");
        exit;
    }

    public function campaigns(string $clientId): void
    {
        $this->auth->requireAuth();
        
        $clientModel = new Client();
        $campaignModel = new Campaign();
        
        $client = $clientModel->findById((int) $clientId);
        
        if (!$client) {
            header('Location: /admin/clients');
            exit;
        }
        
        $campaigns = $campaignModel->findByClientId((int) $clientId);
        $csrfToken = $this->auth->generateCsrfToken();
        
        include __DIR__ . '/../views/admin/campaigns.php';
    }

    public function campaignDetail(string $id): void
    {
        $this->auth->requireAuth();
        
        $campaignModel = new Campaign();
        $subjectModel = new SubjectLine();
        $emailModel = new EmailBody();
        
        $campaign = $campaignModel->findById((int) $id);
        
        if (!$campaign) {
            header('Location: /admin/clients');
            exit;
        }
        
        $subjects = $subjectModel->findByCampaignId((int) $id);
        $emails = $emailModel->findByCampaignId((int) $id);
        $csrfToken = $this->auth->generateCsrfToken();
        
        include __DIR__ . '/../views/admin/campaign-detail.php';
    }

    public function createCampaign(): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $clientId = (int) ($_POST['client_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;

        if (empty($name) || $clientId === 0) {
            $_SESSION['error'] = 'Campaign name is required.';
            header("Location: /admin/clients/{$clientId}");
            exit;
        }

        $campaignModel = new Campaign();
        $campaignId = $campaignModel->create($clientId, $name, $description);

        $_SESSION['success'] = 'Campaign created successfully.';
        header("Location: /admin/campaigns/{$campaignId}");
        exit;
    }

    public function updateCampaign(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header("Location: /admin/campaigns/{$id}");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;
        $isActive = isset($_POST['is_active']);

        $campaignModel = new Campaign();
        $campaign = $campaignModel->findById((int) $id);
        $campaignModel->update((int) $id, $name, $description, $isActive);

        $_SESSION['success'] = 'Campaign updated successfully.';
        header("Location: /admin/campaigns/{$id}");
        exit;
    }

    public function subjects(string $campaignId): void
    {
        $this->auth->requireAuth();
        
        $campaignModel = new Campaign();
        $subjectModel = new SubjectLine();
        
        $campaign = $campaignModel->findById((int) $campaignId);
        
        if (!$campaign) {
            header('Location: /admin/clients');
            exit;
        }
        
        $subjects = $subjectModel->findByCampaignId((int) $campaignId);
        $csrfToken = $this->auth->generateCsrfToken();
        
        include __DIR__ . '/../views/admin/subjects.php';
    }

    public function emails(string $campaignId): void
    {
        $this->auth->requireAuth();
        
        $campaignModel = new Campaign();
        $emailModel = new EmailBody();
        
        $campaign = $campaignModel->findById((int) $campaignId);
        
        if (!$campaign) {
            header('Location: /admin/clients');
            exit;
        }
        
        $emails = $emailModel->findByCampaignId((int) $campaignId);
        $csrfToken = $this->auth->generateCsrfToken();
        
        include __DIR__ . '/../views/admin/emails.php';
    }

    public function createSubject(): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $campaignId = (int) ($_POST['campaign_id'] ?? 0);
        $subjectText = trim($_POST['subject_text'] ?? '');

        if (empty($subjectText) || $campaignId === 0) {
            $_SESSION['error'] = 'Subject line text is required.';
            header("Location: /admin/campaigns/{$campaignId}");
            exit;
        }

        $subjectModel = new SubjectLine();
        $subjectModel->create($campaignId, $subjectText);

        $_SESSION['success'] = 'Subject line added successfully.';
        header("Location: /admin/campaigns/{$campaignId}");
        exit;
    }

    public function updateSubject(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $subjectText = trim($_POST['subject_text'] ?? '');
        
        $subjectModel = new SubjectLine();
        $subject = $subjectModel->findById((int) $id);
        
        if (!$subject) {
            $_SESSION['error'] = 'Subject line not found.';
            header('Location: /admin/clients');
            exit;
        }

        $subjectModel->update((int) $id, $subjectText);

        $_SESSION['success'] = 'Subject line updated successfully.';
        header("Location: /admin/campaigns/{$subject['campaign_id']}");
        exit;
    }

    public function deleteSubject(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $subjectModel = new SubjectLine();
        $subject = $subjectModel->findById((int) $id);
        
        if (!$subject) {
            $_SESSION['error'] = 'Subject line not found.';
            header('Location: /admin/clients');
            exit;
        }

        $campaignId = $subject['campaign_id'];
        $subjectModel->delete((int) $id);

        $_SESSION['success'] = 'Subject line deleted.';
        header("Location: /admin/campaigns/{$campaignId}");
        exit;
    }

    public function resubmitSubject(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $subjectText = trim($_POST['subject_text'] ?? '');
        
        $subjectModel = new SubjectLine();
        $subject = $subjectModel->findById((int) $id);
        
        if (!$subject) {
            $_SESSION['error'] = 'Subject line not found.';
            header('Location: /admin/clients');
            exit;
        }

        $subjectModel->resubmit((int) $id, $subjectText);

        $_SESSION['success'] = 'Subject line resubmitted for approval.';
        header("Location: /admin/campaigns/{$subject['campaign_id']}");
        exit;
    }

    public function createEmail(): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $campaignId = (int) ($_POST['campaign_id'] ?? 0);
        $bodyContent = $_POST['body_content'] ?? '';
        $internalName = trim($_POST['internal_name'] ?? '') ?: null;

        if (empty($bodyContent) || $campaignId === 0) {
            $_SESSION['error'] = 'Email body content is required.';
            header("Location: /admin/campaigns/{$campaignId}");
            exit;
        }

        // Sanitize HTML content
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,u,h1,h2,h3,h4,h5,h6,ul,ol,li,a[href],img[src|alt|width|height],table,tr,td,th,thead,tbody,span[style],div[style]');
        $config->set('CSS.AllowedProperties', 'color,background-color,font-size,font-family,text-align,padding,margin');
        $purifier = new \HTMLPurifier($config);
        $bodyContent = $purifier->purify($bodyContent);

        $emailModel = new EmailBody();
        $emailModel->create($campaignId, $bodyContent, $internalName);

        $_SESSION['success'] = 'Email body added successfully.';
        header("Location: /admin/campaigns/{$campaignId}");
        exit;
    }

    public function updateEmail(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $bodyContent = $_POST['body_content'] ?? '';
        $internalName = trim($_POST['internal_name'] ?? '') ?: null;
        
        $emailModel = new EmailBody();
        $email = $emailModel->findById((int) $id);
        
        if (!$email) {
            $_SESSION['error'] = 'Email body not found.';
            header('Location: /admin/clients');
            exit;
        }

        // Sanitize HTML content
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,u,h1,h2,h3,h4,h5,h6,ul,ol,li,a[href],img[src|alt|width|height],table,tr,td,th,thead,tbody,span[style],div[style]');
        $config->set('CSS.AllowedProperties', 'color,background-color,font-size,font-family,text-align,padding,margin');
        $purifier = new \HTMLPurifier($config);
        $bodyContent = $purifier->purify($bodyContent);

        $emailModel->update((int) $id, $bodyContent, $internalName);

        $_SESSION['success'] = 'Email body updated successfully.';
        header("Location: /admin/campaigns/{$email['campaign_id']}");
        exit;
    }

    public function deleteEmail(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $emailModel = new EmailBody();
        $email = $emailModel->findById((int) $id);
        
        if (!$email) {
            $_SESSION['error'] = 'Email body not found.';
            header('Location: /admin/clients');
            exit;
        }

        $campaignId = $email['campaign_id'];
        $emailModel->delete((int) $id);

        $_SESSION['success'] = 'Email body deleted.';
        header("Location: /admin/campaigns/{$campaignId}");
        exit;
    }

    public function resubmitEmail(string $id): void
    {
        $this->auth->requireAuth();
        
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /admin/clients');
            exit;
        }

        $bodyContent = $_POST['body_content'] ?? '';
        
        $emailModel = new EmailBody();
        $email = $emailModel->findById((int) $id);
        
        if (!$email) {
            $_SESSION['error'] = 'Email body not found.';
            header('Location: /admin/clients');
            exit;
        }

        // Sanitize HTML content
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,u,h1,h2,h3,h4,h5,h6,ul,ol,li,a[href],img[src|alt|width|height],table,tr,td,th,thead,tbody,span[style],div[style]');
        $config->set('CSS.AllowedProperties', 'color,background-color,font-size,font-family,text-align,padding,margin');
        $purifier = new \HTMLPurifier($config);
        $bodyContent = $purifier->purify($bodyContent);

        $emailModel->resubmit((int) $id, $bodyContent);

        $_SESSION['success'] = 'Email body resubmitted for approval.';
        header("Location: /admin/campaigns/{$email['campaign_id']}");
        exit;
    }
}

