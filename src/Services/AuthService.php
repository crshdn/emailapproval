<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminUser;
use App\Config\Database;
use PDO;

class AuthService
{
    private AdminUser $adminUser;
    private PDO $db;
    private int $maxLoginAttempts = 5;
    private int $lockoutMinutes = 15;

    public function __construct()
    {
        $this->adminUser = new AdminUser();
        $this->db = Database::getConnection();
    }

    public function login(string $username, string $password, string $ipAddress): array
    {
        // Check for rate limiting
        if ($this->isLockedOut($ipAddress)) {
            return [
                'success' => false,
                'message' => 'Too many failed login attempts. Please try again later.'
            ];
        }

        $user = $this->adminUser->findByUsername($username);

        if (!$user || !$this->adminUser->verifyPassword($password, $user['password_hash'])) {
            $this->recordLoginAttempt($ipAddress, $username, false);
            return [
                'success' => false,
                'message' => 'Invalid username or password.'
            ];
        }

        // Successful login
        $this->recordLoginAttempt($ipAddress, $username, true);
        $this->adminUser->updateLastLogin($user['id']);

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        return [
            'success' => true,
            'message' => 'Login successful.'
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            return false;
        }

        // Check session lifetime
        $sessionLifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 7200);
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionLifetime) {
            $this->logout();
            return false;
        }

        return true;
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->adminUser->findById($_SESSION['admin_id']);
    }

    public function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    private function isLockedOut(string $ipAddress): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as attempt_count
            FROM login_attempts
            WHERE ip_address = ? 
              AND successful = 0 
              AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$ipAddress, $this->lockoutMinutes]);
        $result = $stmt->fetch();

        return ($result['attempt_count'] ?? 0) >= $this->maxLoginAttempts;
    }

    private function recordLoginAttempt(string $ipAddress, ?string $username, bool $successful): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip_address, username, successful, attempted_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$ipAddress, $username, $successful ? 1 : 0]);
    }

    public function clearOldLoginAttempts(): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM login_attempts 
            WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
    }
}

