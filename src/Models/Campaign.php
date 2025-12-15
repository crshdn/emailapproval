<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Campaign
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByClientId(int $clientId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   (SELECT COUNT(*) FROM subject_lines WHERE campaign_id = c.id AND status = 'pending') as pending_subjects,
                   (SELECT COUNT(*) FROM email_bodies WHERE campaign_id = c.id AND status = 'pending') as pending_emails,
                   (SELECT COUNT(*) FROM subject_lines WHERE campaign_id = c.id AND status = 'approved') as approved_subjects,
                   (SELECT COUNT(*) FROM email_bodies WHERE campaign_id = c.id AND status = 'approved') as approved_emails,
                   (SELECT COUNT(*) FROM subject_lines WHERE campaign_id = c.id AND status = 'denied') as denied_subjects,
                   (SELECT COUNT(*) FROM email_bodies WHERE campaign_id = c.id AND status = 'denied') as denied_emails
            FROM campaigns c
            WHERE c.client_id = ? AND c.is_active = 1
            ORDER BY c.name ASC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cl.name as client_name, cl.id as client_id
            FROM campaigns c
            JOIN clients cl ON c.client_id = cl.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByIdAndClient(int $id, int $clientId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   (SELECT COUNT(*) FROM subject_lines WHERE campaign_id = c.id AND status = 'pending') as pending_subjects,
                   (SELECT COUNT(*) FROM email_bodies WHERE campaign_id = c.id AND status = 'pending') as pending_emails
            FROM campaigns c
            WHERE c.id = ? AND c.client_id = ? AND c.is_active = 1
        ");
        $stmt->execute([$id, $clientId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(int $clientId, string $name, ?string $description = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO campaigns (client_id, name, description, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$clientId, $name, $description]);
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, ?string $description, bool $isActive): bool
    {
        $stmt = $this->db->prepare("
            UPDATE campaigns 
            SET name = ?, description = ?, is_active = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $isActive ? 1 : 0, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM campaigns WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getClientId(int $campaignId): ?int
    {
        $stmt = $this->db->prepare("SELECT client_id FROM campaigns WHERE id = ?");
        $stmt->execute([$campaignId]);
        $result = $stmt->fetch();
        return $result ? (int) $result['client_id'] : null;
    }
}

