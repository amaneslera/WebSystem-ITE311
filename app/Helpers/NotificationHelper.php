<?php

namespace App\Helpers;

use App\Models\NotificationModel;
use App\Models\EnrollmentModel;
use App\Models\UserModel;

/**
 * NotificationHelper - Centralized notification system
 * Sends targeted notifications to specific users based on context
 */
class NotificationHelper
{
    protected $notificationModel;
    protected $enrollmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->userModel = new UserModel();
    }

    /**
     * Notify all students enrolled in a specific course
     * @param int $courseId Course ID
     * @param string $message Notification message
     * @return int Number of notifications created
     */
    public function notifyStudentsInCourse($courseId, $message)
    {
        // Get all students enrolled in this course
        $students = $this->enrollmentModel
            ->select('enrollments.user_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $courseId)
            ->where('users.role', 'student')
            ->where('users.status', 'active')
            ->findAll();

        $count = 0;
        foreach ($students as $student) {
            if ($this->notificationModel->createNotification($student['user_id'], $message)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Notify a specific teacher
     * @param int $teacherId Teacher user ID
     * @param string $message Notification message
     * @return bool Success status
     */
    public function notifyTeacher($teacherId, $message)
    {
        // Verify user is a teacher
        $teacher = $this->userModel->find($teacherId);
        if (!$teacher || $teacher['role'] !== 'teacher' || $teacher['status'] !== 'active') {
            return false;
        }

        return $this->notificationModel->createNotification($teacherId, $message);
    }

    /**
     * Notify all administrators
     * @param string $message Notification message
     * @return int Number of notifications created
     */
    public function notifyAllAdmins($message)
    {
        $admins = $this->userModel
            ->where('role', 'admin')
            ->where('status', 'active')
            ->findAll();

        $count = 0;
        foreach ($admins as $admin) {
            if ($this->notificationModel->createNotification($admin['id'], $message)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Notify a specific student
     * @param int $studentId Student user ID
     * @param string $message Notification message
     * @return bool Success status
     */
    public function notifyStudent($studentId, $message)
    {
        // Verify user is a student
        $student = $this->userModel->find($studentId);
        if (!$student || $student['role'] !== 'student' || $student['status'] !== 'active') {
            return false;
        }

        return $this->notificationModel->createNotification($studentId, $message);
    }

    /**
     * Notify a specific user (any role)
     * @param int $userId User ID
     * @param string $message Notification message
     * @return bool Success status
     */
    public function notifyUser($userId, $message)
    {
        $user = $this->userModel->find($userId);
        if (!$user || $user['status'] !== 'active') {
            return false;
        }

        return $this->notificationModel->createNotification($userId, $message);
    }

    /**
     * Notify teacher of a specific course
     * @param int $courseId Course ID
     * @param string $message Notification message
     * @return bool Success status
     */
    public function notifyTeacherOfCourse($courseId, $message)
    {
        $db = \Config\Database::connect();
        $course = $db->table('courses')
            ->select('teacher_id')
            ->where('id', $courseId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return false;
        }

        return $this->notifyTeacher($course['teacher_id'], $message);
    }
}
