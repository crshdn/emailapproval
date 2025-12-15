<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class AdminUser
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $username, string $password, string $email): int
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $this->db->prepare("
            INSERT INTO admin_users (username, password_hash, email, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$username, $passwordHash, $email]);
        
        return (int) $this->db->lastInsertId();
    }

    public function verifyPassword(string $password, string $passwordHash): bool
    {
        return password_verify($password, $passwordHash);
    }

    public function updateLastLogin(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $this->db->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$passwordHash, $id]);
    }
}

