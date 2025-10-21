<?php


namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get notifications for current user (JSON response for AJAX)
     * Returns unread count and list of notifications
     */
    public function get()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        
        // Get unread count and notifications
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        $notifications = $this->notificationModel->getNotificationsForUser($userId, 10);

        // Format timestamps for better readability
        foreach ($notifications as &$notification) {
            $timestamp = strtotime($notification['created_at']);
            $diff = time() - $timestamp;
            
            if ($diff < 60) {
                $notification['time_ago'] = 'Just now';
            } elseif ($diff < 3600) {
                $notification['time_ago'] = floor($diff / 60) . ' min ago';
            } elseif ($diff < 86400) {
                $notification['time_ago'] = floor($diff / 3600) . ' hours ago';
            } else {
                $notification['time_ago'] = date('M d, Y', $timestamp);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a specific notification as read (JSON response for AJAX)
     * @param int $id Notification ID
     */
    public function mark_as_read($id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        
        // Get the notification to verify ownership
        $notification = $this->notificationModel->find($id);
        
        if (!$notification) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found'
            ])->setStatusCode(404);
        }

        // Verify the notification belongs to the current user
        if ($notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(403);
        }

        // Mark as read
        $result = $this->notificationModel->markAsRead($id);

        if ($result) {
            // Get updated unread count
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $unreadCount
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ])->setStatusCode(500);
        }
    }
}