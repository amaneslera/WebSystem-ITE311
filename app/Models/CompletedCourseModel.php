<?php

namespace App\Models;

use CodeIgniter\Model;

class CompletedCourseModel extends Model
{
    protected $table = 'completed_courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'completed_date', 'grade', 'institution', 'notes'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Check if user has completed a course
     */
    public function hasCompletedCourse($userId, $courseId)
    {
        return $this->where(['user_id' => $userId, 'course_id' => $courseId])->first() !== null;
    }

    /**
     * Mark a course as completed for a user
     */
    public function markCourseComplete($data)
    {
        // Check if already exists
        $existing = $this->where([
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id']
        ])->first();

        if ($existing) {
            return false; // Already marked as completed
        }

        return $this->insert($data);
    }

    /**
     * Get all completed courses for a user with course details
     */
    public function getUserCompletedCourses($userId)
    {
        return $this->select('completed_courses.*, courses.title as course_name, courses.course_code as course_code')
            ->join('courses', 'courses.id = completed_courses.course_id')
            ->where('completed_courses.user_id', $userId)
            ->orderBy('completed_courses.completed_date', 'DESC')
            ->findAll();
    }

    /**
     * Get all completed courses for display in admin
     */
    public function getAllCompletedCourses()
    {
        return $this->select('completed_courses.*, users.name as student_name, courses.title as course_name, courses.course_code as course_code')
            ->join('users', 'users.id = completed_courses.user_id')
            ->join('courses', 'courses.id = completed_courses.course_id')
            ->orderBy('completed_courses.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Delete a completed course record
     */
    public function removeCompletedCourse($userId, $courseId)
    {
        return $this->where([
            'user_id' => $userId,
            'course_id' => $courseId
        ])->delete();
    }
}
