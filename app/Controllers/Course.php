<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\NotificationModel;
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
        
        // Validate input with comprehensive rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_id' => [
                'label' => 'Course ID',
                'rules' => 'required|integer|is_not_unique[courses.id]',
                'errors' => [
                    'required' => 'Course ID is required',
                    'integer' => 'Invalid course ID format',
                    'is_not_unique' => 'Course does not exist'
                ]
            ]
        ]);
        
        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ])->setStatusCode(400);
        }
        
        $courseId = $this->request->getVar('course_id');
        $userId = session()->get('user_id');
        
        // Initialize models
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();
        
        // Check if user is already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ])->setStatusCode(409);
        }
        
        // Check if course has available seats
        if (!$courseModel->hasAvailableSeats($courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This course is full. No seats available.'
            ])->setStatusCode(409);
        }
        
        // Check if course matches student's program and year level (real-world restriction)
        $userModel = new \App\Models\UserModel();
        $student = $userModel->find($userId);
        $course = $courseModel->find($courseId);
        
        if ($student && $course) {
            // If student has a program, check if course matches (optional for flexibility)
            if (!empty($student['program_id']) && !empty($course['program_id'])) {
                if ($student['program_id'] != $course['program_id']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'This course is not available for your program.'
                    ])->setStatusCode(409);
                }
            }
            
            // If student has year level, check if course matches (optional)
            if (!empty($student['year_level']) && !empty($course['year_level'])) {
                if ($student['year_level'] != $course['year_level']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'This course is for ' . $course['year_level'] . ' students only.'
                    ])->setStatusCode(409);
                }
            }
        }
        
        // Check prerequisites
        if (!$courseModel->hasPrerequisites($courseId, $userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You have not completed the required prerequisite courses.'
            ])->setStatusCode(409);
        }
        
        // Check for schedule conflicts
        $academicYearModel = new \App\Models\AcademicYearModel();
        $currentYear = $academicYearModel->getCurrentAcademicYear();
        
        if ($currentYear) {
            $scheduleModel = new \App\Models\ScheduleModel();
            if ($scheduleModel->hasStudentConflict($userId, $courseId, $currentYear['year_code'], $currentYear['current_semester'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Schedule conflict detected. This course overlaps with your existing schedule.'
                ])->setStatusCode(409);
            }
        }
        
        // Use transaction for atomic enrollment
        $result = $enrollmentModel->enrollUserWithTransaction([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ]);
        
        if ($result['success']) {
            // Get course details
            $course = $courseModel->getCourseWithTeacher($courseId);
            
            // Update session data
            $currentCount = session()->get('total_courses') ?? 0;
            session()->set('total_courses', $currentCount + 1);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course',
                'enrollment_id' => $result['enrollment_id'],
                'course' => $course
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to enroll in the course. Please try again.'
            ])->setStatusCode(500);
        }
    }

    public function view($course_id)
    {
        
        $materialModel = new \App\Models\MaterialModel();

        
        $materials = $materialModel->getMaterialsByCourse($course_id);

        // Optionally, check if the student is enrolled in this course

        return view('course_view', [
            
            'materials' => $materials
        ]);
    }
}