<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\NotificationRepository;

class NotificationController extends Controller {
    private $notificationRepo;

    public function __construct() {
        $this->requireLogin();
        $this->notificationRepo = new NotificationRepository();
    }

    /**
     * View Activity Center (Announcements + Notifications)
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // 1. Fetch Personal Notifications
        $notifications = $this->notificationRepo->getAllByUser($userId, 50, 0);
        foreach($notifications as $n) $n->source = 'personal';

        // 2. Fetch Announcements
        $announcementRepo = new \App\Repositories\AnnouncementRepository();
        $announcements = $announcementRepo->getAll(50, 0);
        foreach($announcements as $a) {
            $a->source = 'announcement';
            $a->type = 'info'; // Default announcement type
        }

        // 3. Merge and Sort by Date (Descending)
        $merged = array_merge($notifications, $announcements);
        usort($merged, function($a, $b) {
            return strtotime($b->created_at) <=> strtotime($a->created_at);
        });

        // 4. Mark announcements as read for this user
        $announcementRepo->markAsRead($userId);

        $this->view('notifications/index', [
            'pageTitle' => 'Activity Center',
            'title' => 'Updates | ASMS',
            'updates' => $merged,
            'personalCount' => $this->notificationRepo->countUnread($userId)
        ]);
    }

    /**
     * Mark a notification as read (AJAX)
     */
    public function markAsRead() {
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        $userId = $_SESSION['user_id'];

        if ($id && $this->notificationRepo->markAsRead($id, $userId)) {
            $this->ok(['message' => 'Marked as read.']);
        } else {
            $this->error('Failed to update notification.', 500);
        }
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead() {
        $this->verifyCsrf();
        $userId = $_SESSION['user_id'];

        if ($this->notificationRepo->markAllAsRead($userId)) {
            setFlash('msg_success', 'All notifications marked as read.');
        } else {
            setFlash('msg_error', 'Failed to update notifications.');
        }
        redirect('notifications');
    }

    /**
     * Get unread count and latest unread (AJAX polling)
     */
    public function getLatest() {
        $userId = $_SESSION['user_id'];
        $unreadCount = $this->notificationRepo->countUnread($userId);
        $latest = $this->notificationRepo->getUnread($userId, 5);

        $this->ok([
            'count' => $unreadCount,
            'notifications' => $latest
        ]);
    }
}
