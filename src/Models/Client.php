<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Client
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, 
                   COUNT(DISTINCT camp.id) as campaign_count,
                   (SELECT COUNT(*) FROM subject_lines sl 
                    JOIN campaigns ca ON sl.campaign_id = ca.id 
                    WHERE ca.client_id = c.id AND sl.status = 'pending') as pending_subjects,
                   (SELECT COUNT(*) FROM email_bodies eb 
                    JOIN campaigns ca ON eb.campaign_id = ca.id 
                    WHERE ca.client_id = c.id AND eb.status = 'pending') as pending_emails
            FROM clients c
            LEFT JOIN campaigns camp ON c.id = camp.client_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE access_token = ? AND is_active = 1");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $name, string $email): int
    {
        $token = $this->generateToken();
        
        $stmt = $this->db->prepare("
            INSERT INTO clients (name, email, access_token, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $email, $token]);
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email, bool $isActive): bool
    {
        $stmt = $this->db->prepare("
            UPDATE clients 
            SET name = ?, email = ?, is_active = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$name, $email, $isActive ? 1 : 0, $id]);
    }

    public function regenerateToken(int $id): string
    {
        $token = $this->generateToken();
        
        $stmt = $this->db->prepare("
            UPDATE clients 
            SET access_token = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$token, $id]);
        
        return $token;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getToken(int $id): ?string
    {
        $stmt = $this->db->prepare("SELECT access_token FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? $result['access_token'] : null;
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}

