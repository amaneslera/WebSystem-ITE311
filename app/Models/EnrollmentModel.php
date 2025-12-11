<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];
    
    protected $useTimestamps = false;
    
    /**
     * Insert a new enrollment record
     * 
     * @param array $data Array containing user_id and course_id
     * @return mixed Insert ID on success, false on failure
     */
    public function enrollUser($data)
    {
        // Add enrollment date if not provided
        if (!isset($data['enrollment_date'])) {
            $data['enrollment_date'] = date('Y-m-d H:i:s');
        }
        
        // Verify required fields exist
        if (!isset($data['user_id']) || !isset($data['course_id'])) {
            return false;
        }
        
        // Check if already enrolled
        if ($this->isAlreadyEnrolled($data['user_id'], $data['course_id'])) {
            return false; // Already enrolled
        }
        
        // Insert the enrollment record
        return $this->insert($data);
    }
    
    /**
     * Get all courses a user is enrolled in
     * Excludes soft-deleted teachers from results
     * 
     * @param int $user_id The user ID
     * @return array Array of course data the user is enrolled in
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title as course_title, courses.description, users.name as teacher_name')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users', 'users.id = courses.teacher_id AND users.status = \'active\'') // Exclude inactive teachers
            ->where('enrollments.user_id', $user_id)
            ->findAll();
    }
    
    /**
     * Check if a user is already enrolled in a specific course
     * 
     * @param int $user_id The user ID
     * @param int $course_id The course ID
     * @return bool True if already enrolled, false otherwise
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $result = $this->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->countAllResults();
            
        return ($result > 0);
    }

    /**
     * Alias for isAlreadyEnrolled
     * Check if a user is enrolled in a specific course
     * 
     * @param int $user_id The user ID
     * @param int $course_id The course ID
     * @return bool True if enrolled, false otherwise
     */
    public function isEnrolled($user_id, $course_id)
    {
        return $this->isAlreadyEnrolled($user_id, $course_id);
    }
    
    /**
     * Get all students enrolled in a specific course
     * Excludes soft-deleted students from results
     * 
     * @param int $course_id The course ID
     * @return array Array of student data enrolled in the course
     */
    public function getCourseEnrollments($course_id)
    {
        return $this->select('enrollments.*, users.name, users.email')
            ->join('users', 'users.id = enrollments.user_id AND users.status = \'active\'') // Exclude inactive students
            ->where('enrollments.course_id', $course_id)
            ->findAll();
    }
    
    /**
     * Unenroll a user from a course
     * 
     * @param int $user_id The user ID
     * @param int $course_id The course ID
     * @return bool True on success, false on failure
     */
    public function unenrollUser($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->delete();
    }
    
    /**
     * Enroll user with transaction support
     * 
     * @param array $data Enrollment data
     * @return array Result with success status
     */
    public function enrollUserWithTransaction($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Enroll the user
            $enrollmentId = $this->enrollUser($data);
            
            if (!$enrollmentId) {
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to create enrollment record'
                ];
            }
            
            // Increment course enrolled count
            $courseModel = new \App\Models\CourseModel();
            if (!$courseModel->incrementEnrolled($data['course_id'])) {
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to update course enrollment count'
                ];
            }
            
            // Get course details for notification
            $course = $courseModel->find($data['course_id']);
            
            // Create notification
            $notificationModel = new \App\Models\NotificationModel();
            $message = "You have been enrolled in " . $course['title'];
            $notificationModel->createNotification($data['user_id'], $message);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Transaction failed'
                ];
            }
            
            return [
                'success' => true,
                'enrollment_id' => $enrollmentId,
                'message' => 'Successfully enrolled'
            ];
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Enrollment failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during enrollment'
            ];
        }
    }
}