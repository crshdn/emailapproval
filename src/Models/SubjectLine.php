<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class SubjectLine
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByCampaignId(int $campaignId, ?string $status = null): array
    {
        $sql = "SELECT * FROM subject_lines WHERE campaign_id = ?";
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
            SELECT sl.*, c.name as campaign_name, c.client_id, cl.name as client_name
            FROM subject_lines sl
            JOIN campaigns c ON sl.campaign_id = c.id
            JOIN clients cl ON c.client_id = cl.id
            WHERE sl.id = ?
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
            SELECT * FROM subject_lines 
            WHERE campaign_id = ? AND status = 'pending'
            ORDER BY created_at ASC
            LIMIT 1
        ");
        $stmt->execute([$campaignId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(int $campaignId, string $subjectText): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO subject_lines (campaign_id, subject_text, status, revision_number, created_at) 
            VALUES (?, ?, 'pending', 1, NOW())
        ");
        $stmt->execute([$campaignId, $subjectText]);
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $subjectText): bool
    {
        $stmt = $this->db->prepare("
            UPDATE subject_lines 
            SET subject_text = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$subjectText, $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE subject_lines 
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }

    public function resubmit(int $id, string $subjectText): bool
    {
        $stmt = $this->db->prepare("
            UPDATE subject_lines 
            SET subject_text = ?, status = 'pending', 
                revision_number = revision_number + 1, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$subjectText, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM subject_lines WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCampaignId(int $subjectId): ?int
    {
        $stmt = $this->db->prepare("SELECT campaign_id FROM subject_lines WHERE id = ?");
        $stmt->execute([$subjectId]);
        $result = $stmt->fetch();
        return $result ? (int) $result['campaign_id'] : null;
    }
}

