<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class EmailBody
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByCampaignId(int $campaignId, ?string $status = null): array
    {
        $sql = "SELECT * FROM email_bodies WHERE campaign_id = ?";
        $params = [$campaignId];
        
        if ($status !== null) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT eb.*, c.name as campaign_name, c.client_id, cl.name as client_name
            FROM email_bodies eb
            JOIN campaigns c ON eb.campaign_id = c.id
            JOIN clients cl ON c.client_id = cl.id
            WHERE eb.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findPendingByCampaignId(int $campaignId): array
    {
        return $this->findByCampaignId($campaignId, 'pending');
    }

    public function findFirstPendingByCampaignId(int $campaignId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM email_bodies 
            WHERE campaign_id = ? AND status = 'pending'
            ORDER BY created_at ASC
            LIMIT 1
        ");
        $stmt->execute([$campaignId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(int $campaignId, string $bodyContent, ?string $internalName = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO email_bodies (campaign_id, body_content, internal_name, status, revision_number, created_at) 
            VALUES (?, ?, ?, 'pending', 1, NOW())
        ");
        $stmt->execute([$campaignId, $bodyContent, $internalName]);
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $bodyContent, ?string $internalName = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE email_bodies 
            SET body_content = ?, internal_name = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$bodyContent, $internalName, $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE email_bodies 
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }

    public function resubmit(int $id, string $bodyContent): bool
    {
        $stmt = $this->db->prepare("
            UPDATE email_bodies 
            SET body_content = ?, status = 'pending', 
                revision_number = revision_number + 1, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$bodyContent, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM email_bodies WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCampaignId(int $emailId): ?int
    {
        $stmt = $this->db->prepare("SELECT campaign_id FROM email_bodies WHERE id = ?");
        $stmt->execute([$emailId]);
        $result = $stmt->fetch();
        return $result ? (int) $result['campaign_id'] : null;
    }
}

