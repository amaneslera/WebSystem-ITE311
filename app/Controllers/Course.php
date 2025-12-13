<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\NotificationModel;
use App\Models\EnrollmentInvitationModel;
use App\Helpers\NotificationHelper;
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
        $invitationModel = new EnrollmentInvitationModel();
        $completedCourseModel = new \App\Models\CompletedCourseModel();
        
        // Check if course is already completed
        if ($completedCourseModel->hasCompletedCourse($userId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You have already completed this course'
            ])->setStatusCode(409);
        }
        
        // Check if user is already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ])->setStatusCode(409);
        }
        
        // Check if there's already a pending invitation or request
        if ($invitationModel->hasPendingInvitationOrRequest($userId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You already have a pending enrollment request for this course'
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
        
        // Create enrollment request instead of direct enrollment
        // Support both JSON and POST data
        $message = null;
        if ($this->request->getHeaderLine('Content-Type') === 'application/json') {
            $json = $this->request->getJSON();
            $message = $json->message ?? null;
        } else {
            $message = $this->request->getPost('message');
        }
        $result = $invitationModel->createRequest($userId, $courseId, $message);
        
        if ($result['success']) {
            // Get course details
            $course = $courseModel->getCourseWithTeacher($courseId);
            
            // Notify teacher and admins about the enrollment request
            try {
                $notificationHelper = new NotificationHelper();
                $studentName = session()->get('name');
                $courseName = $course['title'] ?? 'Unknown Course';
                
                // Notify the teacher
                $teacherMessage = "{$studentName} has requested to enroll in your course: {$courseName}";
                $notificationHelper->notifyTeacherOfCourse($courseId, $teacherMessage);
                
                // Notify admins
                $adminMessage = "Student enrollment request: {$studentName} → {$courseName}";
                $notificationHelper->notifyAllAdmins($adminMessage);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send enrollment request notification: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment request submitted! Please wait for approval from the teacher or admin.',
                'course' => $course
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to submit enrollment request. Please try again.'
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

    /**
     * Search courses - supports both AJAX and regular requests
     * Laboratory Exercise 9: Search and Filtering System
     * 
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function search()
    {
        $searchTerm = $this->request->getGet('search_term') ?? $this->request->getPost('search_term');
        $courseModel = new CourseModel();
        
        if (!empty($searchTerm)) {
            // Search in course title, code, and description using LIKE queries
            $courses = $courseModel->select('courses.*, users.name as teacher_name')
                ->join('users', 'users.id = courses.teacher_id AND users.status = \'active\'', 'left')
                ->groupStart()
                    ->like('courses.title', $searchTerm)
                    ->orLike('courses.course_code', $searchTerm)
                    ->orLike('courses.description', $searchTerm)
                ->groupEnd()
                ->where('courses.status', 'active')
                ->findAll();
        } else {
            // Return all active courses if no search term
            $courses = $courseModel->select('courses.*, users.name as teacher_name')
                ->join('users', 'users.id = courses.teacher_id AND users.status = \'active\'', 'left')
                ->where('courses.status', 'active')
                ->findAll();
        }
        
        // Return JSON for AJAX requests
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'courses' => $courses,
                'count' => count($courses),
                'search_term' => $searchTerm
            ]);
        }
        
        // Return view for regular requests (courses/index.php)
        return view('courses/index', [
            'courses' => $courses,
            'search_term' => $searchTerm ?? ''
        ]);
    }
}