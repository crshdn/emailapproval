<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class ApprovalHistory
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByClientId(int $clientId, ?int $limit = null): array
    {
        $sql = "
            SELECT ah.*, 
                   CASE 
                       WHEN ah.item_type = 'subject_line' THEN sl.subject_text
                       WHEN ah.item_type = 'email_body' THEN SUBSTRING(eb.body_content, 1, 200)
                   END as item_preview,
                   CASE 
                       WHEN ah.item_type = 'subject_line' THEN c1.name
                       WHEN ah.item_type = 'email_body' THEN c2.name
                   END as campaign_name
            FROM approval_history ah
            LEFT JOIN subject_lines sl ON ah.item_type = 'subject_line' AND ah.item_id = sl.id
            LEFT JOIN campaigns c1 ON sl.campaign_id = c1.id
            LEFT JOIN email_bodies eb ON ah.item_type = 'email_body' AND ah.item_id = eb.id
            LEFT JOIN campaigns c2 ON eb.campaign_id = c2.id
            WHERE ah.client_id = ?
            ORDER BY ah.created_at DESC
        ";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function findByCampaignId(int $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT ah.*, 
                   CASE 
                       WHEN ah.item_type = 'subject_line' THEN sl.subject_text
                       WHEN ah.item_type = 'email_body' THEN SUBSTRING(eb.body_content, 1, 200)
                   END as item_preview
            FROM approval_history ah
            LEFT JOIN subject_lines sl ON ah.item_type = 'subject_line' AND ah.item_id = sl.id
            LEFT JOIN email_bodies eb ON ah.item_type = 'email_body' AND ah.item_id = eb.id
            WHERE (sl.campaign_id = ? OR eb.campaign_id = ?)
            ORDER BY ah.created_at DESC
        ");
        $stmt->execute([$campaignId, $campaignId]);
        return $stmt->fetchAll();
    }

    public function findByItemId(string $itemType, int $itemId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM approval_history 
            WHERE item_type = ? AND item_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$itemType, $itemId]);
        return $stmt->fetchAll();
    }

    public function create(
        string $itemType, 
        int $itemId, 
        string $action, 
        int $clientId, 
        int $revisionNumber, 
        ?string $feedback = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO approval_history 
            (item_type, item_id, action, client_id, revision_number, feedback, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$itemType, $itemId, $action, $clientId, $revisionNumber, $feedback]);
        
        return (int) $this->db->lastInsertId();
    }

    public function getRecentActivity(int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT ah.*, 
                   cl.name as client_name,
                   CASE 
                       WHEN ah.item_type = 'subject_line' THEN sl.subject_text
                       WHEN ah.item_type = 'email_body' THEN SUBSTRING(eb.body_content, 1, 200)
                   END as item_preview,
                   CASE 
                       WHEN ah.item_type = 'subject_line' THEN c1.name
                       WHEN ah.item_type = 'email_body' THEN c2.name
                   END as campaign_name
            FROM approval_history ah
            JOIN clients cl ON ah.client_id = cl.id
            LEFT JOIN subject_lines sl ON ah.item_type = 'subject_line' AND ah.item_id = sl.id
            LEFT JOIN campaigns c1 ON sl.campaign_id = c1.id
            LEFT JOIN email_bodies eb ON ah.item_type = 'email_body' AND ah.item_id = eb.id
            LEFT JOIN campaigns c2 ON eb.campaign_id = c2.id
            ORDER BY ah.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}

