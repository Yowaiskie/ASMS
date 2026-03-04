<?php

namespace App\Repositories;

use App\Core\Database;

class NotificationRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new notification
     */
    public function create($data) {
        $this->db->query("INSERT INTO notifications (user_id, title, message, link, type) 
                          VALUES (:user_id, :title, :message, :link, :type)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':message', $data['message']);
        $this->db->bind(':link', $data['link'] ?? null);
        $this->db->bind(':type', $data['type'] ?? 'info');
        return $this->db->execute();
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnread($userId, $limit = 5) {
        $this->db->query("SELECT * FROM notifications 
                          WHERE user_id = :user_id AND is_read = 0 
                          ORDER BY created_at DESC LIMIT :limit");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get all notifications for a user (paginated)
     */
    public function getAllByUser($userId, $limit = 20, $offset = 0) {
        $this->db->query("SELECT * FROM notifications 
                          WHERE user_id = :user_id 
                          ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    /**
     * Count unread notifications
     */
    public function countUnread($userId) {
        $this->db->query("SELECT COUNT(*) as count FROM notifications 
                          WHERE user_id = :user_id AND is_read = 0");
        $this->db->bind(':user_id', $userId);
        return $this->db->single()->count;
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id, $userId) {
        $this->db->query("UPDATE notifications SET is_read = 1 
                          WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    /**
     * Mark all as read for a user
     */
    public function markAllAsRead($userId) {
        $this->db->query("UPDATE notifications SET is_read = 1 
                          WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    /**
     * Delete old notifications (e.g., older than 30 days)
     */
    public function deleteOld($days = 30) {
        $this->db->query("DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        return $this->db->execute();
    }
}
