<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\RESTful\ResourceController;

class Course extends ResourceController
{
    /**
     * Handle course enrollment via AJAX
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function enroll()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to enroll in courses'
            ])->setStatusCode(401);
        }
        
        // Get course_id from POST request
        $courseId = request()->getVar('course_id');
        if (!$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required'
            ])->setStatusCode(400);
        }
        
        // Get user ID from session
        $userId = session()->get('user_id');
        
        // Check if user is already enrolled
        $enrollmentModel = new EnrollmentModel();
        if ($enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ])->setStatusCode(409); // Conflict status code
        }
        
        // Insert new enrollment record
        $result = $enrollmentModel->enrollUser([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Get course details to return
            $db = \Config\Database::connect();
            $course = $db->table('courses')
                ->select('courses.*, users.name as teacher_name')
                ->join('users', 'users.id = courses.teacher_id')
                ->where('courses.id', $courseId)
                ->get()->getRowArray();
            
            // Update session data - add this block
            $currentCount = session()->get('total_courses') ?? 0;
            session()->set('total_courses', $currentCount + 1);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course',
                'enrollment_id' => $result,
                'course' => $course
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in the course. Please try again.'
            ])->setStatusCode(500);
        }
    }
}