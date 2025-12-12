<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\EnrollmentInvitationModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;
use App\Models\RoomModel;
use App\Models\ScheduleModel;
use App\Models\AcademicYearModel;
use App\Helpers\NotificationHelper;
use CodeIgniter\Controller;

class AdminCourses extends Controller
{
    /**
     * Admin: Create new course
     */
    public function create()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Admin access required');
        }

        if ($this->request->getMethod() === 'post') {
            // Validation rules
            $rules = [
                'course_code' => [
                    'rules' => 'required|min_length[3]|max_length[20]|is_unique[courses.course_code]',
                    'errors' => [
                        'required' => 'Course code is required',
                        'min_length' => 'Course code must be at least 3 characters',
                        'max_length' => 'Course code cannot exceed 20 characters',
                        'is_unique' => 'This course code already exists'
                    ]
                ],
                'title' => [
                    'rules' => 'required|min_length[3]|max_length[255]',
                    'errors' => [
                        'required' => 'Course title is required',
                        'min_length' => 'Course title must be at least 3 characters',
                        'max_length' => 'Course title cannot exceed 255 characters'
                    ]
                ],
                'units' => [
                    'rules' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
                    'errors' => [
                        'integer' => 'Units must be a number',
                        'greater_than' => 'Units must be greater than 0',
                        'less_than_equal_to' => 'Units cannot exceed 10'
                    ]
                ],
                'max_students' => [
                    'rules' => 'permit_empty|integer|greater_than[0]',
                    'errors' => [
                        'integer' => 'Max students must be a number',
                        'greater_than' => 'Max students must be greater than 0'
                    ]
                ],
                'lecture_hours' => [
                    'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
                    'errors' => [
                        'integer' => 'Lecture hours must be a number',
                        'greater_than_equal_to' => 'Lecture hours cannot be negative'
                    ]
                ],
                'lab_hours' => [
                    'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
                    'errors' => [
                        'integer' => 'Lab hours must be a number',
                        'greater_than_equal_to' => 'Lab hours cannot be negative'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $courseModel = new CourseModel();
            
            $data = [
                'course_code' => $this->request->getPost('course_code'),
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null,
                'department' => $this->request->getPost('department'),
                'semester' => $this->request->getPost('semester'),
                'units' => $this->request->getPost('units') ?: 3,
                'year_level' => $this->request->getPost('year_level'),
                'program_id' => $this->request->getPost('program_id') ?: null,
                'max_students' => $this->request->getPost('max_students') ?: null,
                'lecture_hours' => $this->request->getPost('lecture_hours') ?: 0,
                'lab_hours' => $this->request->getPost('lab_hours') ?: 0,
                'room' => $this->request->getPost('room'),
                'schedule_days' => $this->request->getPost('schedule_days'),
                'schedule_time' => $this->request->getPost('schedule_time'),
                'prerequisite_course_ids' => json_encode($this->request->getPost('prerequisites') ?? []),
                'status' => 'active',
                'current_enrolled' => 0
            ];

            if ($courseModel->insert($data)) {
                return redirect()->to('/admin/courses')->with('success', 'Course created successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to create course. Please try again.')->withInput();
            }
        }

        // Load form data
        $userModel = new UserModel();
        $programModel = new ProgramModel();
        $departmentModel = new DepartmentModel();

        $data = [
            'teachers' => $userModel->where('role', 'teacher')->where('status', 'active')->findAll(),
            'programs' => $programModel->getActivePrograms(),
            'departments' => $departmentModel->getActiveDepartments(),
            'courses' => (new CourseModel())->where('status', 'active')->findAll()
        ];

        return view('admin/courses/create', $data);
    }

    /**
     * Admin: List all courses
     */
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $courseModel = new CourseModel();
        $data['courses'] = $courseModel->getCoursesWithTeacher();

        return view('admin/courses/index', $data);
    }

    /**
     * Admin: Update course
     */
    public function update($courseId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found');
        }

        log_message('info', '=== UPDATE METHOD CALLED === Course ID: ' . $courseId . ', Method: ' . $this->request->getMethod() . ', IsPost: ' . ($this->request->getMethod() === 'post' ? 'YES' : 'NO'));

        if (strtolower($this->request->getMethod()) === 'post') {
            log_message('info', '=== POST DATA RECEIVED === ' . json_encode($this->request->getPost()));
            
            // Validation rules
            $rules = [
                'course_code' => [
                    'rules' => "required|min_length[3]|max_length[20]|is_unique[courses.course_code,id,{$courseId}]",
                    'errors' => [
                        'required' => 'Course code is required',
                        'min_length' => 'Course code must be at least 3 characters',
                        'max_length' => 'Course code cannot exceed 20 characters',
                        'is_unique' => 'This course code already exists'
                    ]
                ],
                'title' => [
                    'rules' => 'required|min_length[3]|max_length[255]',
                    'errors' => [
                        'required' => 'Course title is required',
                        'min_length' => 'Course title must be at least 3 characters',
                        'max_length' => 'Course title cannot exceed 255 characters'
                    ]
                ],
                'units' => [
                    'rules' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
                    'errors' => [
                        'integer' => 'Units must be a number',
                        'greater_than' => 'Units must be greater than 0',
                        'less_than_equal_to' => 'Units cannot exceed 10'
                    ]
                ],
                'status' => [
                    'rules' => 'required|in_list[active,inactive]',
                    'errors' => [
                        'required' => 'Status is required',
                        'in_list' => 'Status must be either active or inactive'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for update
            $updateData = [];
            
            // Only include fields that have values
            $fields = [
                'course_code', 'title', 'description', 'teacher_id', 'department', 
                'semester', 'units', 'year_level', 'program_id', 'max_students', 
                'lecture_hours', 'lab_hours', 'room', 'schedule_days', 
                'schedule_time', 'prerequisite_course_ids', 'status'
            ];

            foreach ($fields as $field) {
                $value = $this->request->getPost($field);
                if ($value !== null && $value !== '') {
                    $updateData[$field] = $value;
                } elseif (in_array($field, ['teacher_id', 'program_id', 'prerequisite_course_ids', 'max_students'])) {
                    $updateData[$field] = null;
                }
            }

            // Set defaults for numeric fields if empty
            if (empty($updateData['units'])) {
                $updateData['units'] = 3;
            }
            if (empty($updateData['lecture_hours'])) {
                $updateData['lecture_hours'] = 0;
            }
            if (empty($updateData['lab_hours'])) {
                $updateData['lab_hours'] = 0;
            }

            try {
                $result = $courseModel->update($courseId, $updateData);
                
                if ($result !== false) {
                    return redirect()->to('/admin/courses')->with('success', 'Course updated successfully!');
                } else {
                    $errors = $courseModel->errors();
                    $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Failed to update course. Please check your input.';
                    log_message('error', 'Course update failed: ' . json_encode($errors));
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            } catch (\Exception $e) {
                log_message('error', 'Course update exception: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        $userModel = new UserModel();
        $programModel = new ProgramModel();
        $departmentModel = new DepartmentModel();

        $data = [
            'course' => $courseModel->find($courseId),
            'teachers' => $userModel->where('role', 'teacher')->where('status', 'active')->findAll(),
            'programs' => $programModel->getActivePrograms(),
            'departments' => $departmentModel->getActiveDepartments(),
            'courses' => $courseModel->where('status', 'active')->findAll()
        ];

        return view('admin/courses/edit', $data);
    }

    /**
     * Admin: Manually enroll student (override restrictions)
     */
    public function enrollStudent()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin access required'
            ])->setStatusCode(403);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'student_id' => 'required|integer|is_not_unique[users.id]',
            'course_id' => 'required|integer|is_not_unique[courses.id]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ])->setStatusCode(400);
        }

        $studentId = $this->request->getPost('student_id');
        $courseId = $this->request->getPost('course_id');

        try {
            $enrollmentModel = new EnrollmentModel();
            $invitationModel = new EnrollmentInvitationModel();
            $courseModel = new CourseModel();

            // Check if already enrolled
            if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student is already enrolled in this course'
                ])->setStatusCode(409);
            }

            // Check if there's already a pending invitation or request
            if ($invitationModel->hasPendingInvitationOrRequest($studentId, $courseId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'There is already a pending invitation or request for this student and course'
                ])->setStatusCode(409);
            }

            // Admin can override capacity, but still check
            if (!$courseModel->hasAvailableSeats($courseId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Warning: Course is at full capacity'
                ])->setStatusCode(409);
            }

            // Create invitation instead of direct enrollment
            $adminId = session()->get('user_id');
            $message = "You have been invited to enroll in this course by an administrator.";
            $result = $invitationModel->createInvitation($studentId, $courseId, $adminId, $message);

        if ($result['success']) {
            // Notify student and teacher about the invitation
            try {
                $notificationHelper = new NotificationHelper();
                $userModel = new UserModel();
                $student = $userModel->find($studentId);
                $course = $courseModel->find($courseId);
                
                if ($student && $course) {
                    $studentName = $student['name'];
                    $courseName = $course['course_name'] ?? $course['title'] ?? 'Unknown Course';
                    
                    // Notify the student
                    $studentMessage = "You have been invited to enroll in: {$courseName}";
                    $notificationHelper->notifyStudent($studentId, $studentMessage);
                    
                    // Notify the teacher
                    if (isset($course['teacher_id']) && $course['teacher_id']) {
                        $teacherMessage = "Admin sent invitation: {$studentName} → {$courseName}";
                        $notificationHelper->notifyTeacher($course['teacher_id'], $teacherMessage);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to send admin invitation notification: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Invitation sent successfully! Student will be notified.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ])->setStatusCode(500);
        }
        } catch (\Exception $e) {
            log_message('error', 'Admin enrollment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Admin: Assign teacher to course
     */
    public function assignTeacher()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin access required'
            ])->setStatusCode(403);
        }

        $courseId = $this->request->getPost('course_id');
        $teacherId = $this->request->getPost('teacher_id');

        $courseModel = new CourseModel();
        
        if ($courseModel->update($courseId, ['teacher_id' => $teacherId])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher assigned successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to assign teacher'
            ])->setStatusCode(500);
        }
    }

    /**
     * Admin: Get available students for enrollment (not already enrolled)
     */
    public function availableStudents($courseId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin access required'
            ])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        
        // Get students not enrolled in this course
        $students = $db->table('users')
            ->select('users.id, users.name, users.email, users.student_id, users.year_level')
            ->where('users.role', 'student')
            ->where('users.status', 'active')
            ->whereNotIn('users.id', function($builder) use ($courseId) {
                $builder->select('user_id')
                        ->from('enrollments')
                        ->where('course_id', $courseId);
            })
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Admin: Bulk enroll multiple students (override all restrictions)
     */
    public function bulkEnroll()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin access required'
            ])->setStatusCode(403);
        }

        $studentIds = $this->request->getPost('student_ids');
        $courseId = $this->request->getPost('course_id');

        if (empty($studentIds) || !is_array($studentIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No students selected'
            ])->setStatusCode(400);
        }

        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $invitationModel = new EnrollmentInvitationModel();
        $userModel = new UserModel();
        
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ])->setStatusCode(404);
        }

        $invited = 0;
        $failed = 0;
        $errors = [];
        $adminId = session()->get('user_id');

        foreach ($studentIds as $studentId) {
            // Check if already enrolled
            if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
                $user = $userModel->find($studentId);
                $errors[] = ($user['name'] ?? 'Student') . ' is already enrolled';
                $failed++;
                continue;
            }

            // Check if there's already a pending invitation or request
            if ($invitationModel->hasPendingInvitationOrRequest($studentId, $courseId)) {
                $user = $userModel->find($studentId);
                $errors[] = ($user['name'] ?? 'Student') . ' already has a pending invitation';
                $failed++;
                continue;
            }

            // Send invitation instead of direct enrollment
            $message = "You have been invited to enroll in this course by an administrator (bulk enrollment).";
            $result = $invitationModel->createInvitation($studentId, $courseId, $adminId, $message);

            if ($result['success']) {
                $invited++;
                
                // Notify the student
                try {
                    $notificationHelper = new NotificationHelper();
                    $notificationHelper->notifyStudent($studentId, "You have been invited to enroll in: {$course['title']}");
                } catch (\Exception $e) {
                    log_message('error', 'Failed to send bulk invitation notification: ' . $e->getMessage());
                }
            } else {
                $failed++;
                $user = $userModel->find($studentId);
                $errors[] = ($user['name'] ?? 'Student') . ': ' . $result['message'];
            }
        }

        $message = "Sent $invited invitation(s).";
        if ($failed > 0) {
            $message .= " $failed failed.";
            if (!empty($errors)) {
                $message .= "\n\nErrors:\n" . implode("\n", $errors);
            }
        }

        return $this->response->setJSON([
            'success' => $invited > 0,
            'invited_count' => $invited,
            'failed_count' => $failed,
            'message' => $message
        ]);
    }

    /**
     * Admin: Create schedule for course
     */
    public function createSchedule()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin access required'
            ])->setStatusCode(403);
        }

        $scheduleModel = new ScheduleModel();
        
        $data = [
            'course_id' => $this->request->getPost('course_id'),
            'room_id' => $this->request->getPost('room_id'),
            'day_of_week' => $this->request->getPost('day_of_week'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'academic_year' => $this->request->getPost('academic_year'),
            'semester' => $this->request->getPost('semester')
        ];

        // Check for conflicts
        if ($scheduleModel->hasConflict(
            $data['room_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['academic_year'],
            $data['semester']
        )) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Schedule conflict: Room is already booked at this time'
            ])->setStatusCode(409);
        }

        if ($scheduleModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Schedule created successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create schedule'
            ])->setStatusCode(500);
        }
    }

    /**
     * Completed Courses Management - View all students and their completed courses
     */
    public function completedCourses()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Admin access required');
        }

        $userModel = new UserModel();
        $courseModel = new CourseModel();
        $completedCourseModel = new \App\Models\CompletedCourseModel();

        // Get all students
        $students = $userModel->where('role', 'student')->where('status', 'active')->findAll();

        // Get all completed courses
        $allCompletedCourses = $completedCourseModel->getAllCompletedCourses();

        // Get all available courses for the dropdown
        $availableCourses = $courseModel->getAllCourses();

        $data = [
            'title' => 'Completed Courses Management',
            'students' => $students,
            'completedCourses' => $allCompletedCourses,
            'availableCourses' => $availableCourses
        ];

        return view('admin/courses/completed_courses', $data);
    }

    /**
     * Add a completed course for a student
     */
    public function addCompletedCourse()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin access required'
            ])->setStatusCode(403);
        }

        $completedCourseModel = new \App\Models\CompletedCourseModel();

        $data = [
            'user_id' => $this->request->getPost('user_id'),
            'course_id' => $this->request->getPost('course_id'),
            'completed_date' => $this->request->getPost('completed_date'),
            'grade' => $this->request->getPost('grade'),
            'institution' => $this->request->getPost('institution'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($completedCourseModel->markCourseComplete($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course marked as completed successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark course as completed. Student may already have this course marked as completed.'
            ])->setStatusCode(400);
        }
    }

    /**
     * Delete a completed course record
     */
    public function deleteCompletedCourse($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Admin access required');
        }

        $completedCourseModel = new \App\Models\CompletedCourseModel();
        $record = $completedCourseModel->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Completed course record not found.');
        }

        if ($completedCourseModel->delete($id)) {
            return redirect()->back()->with('success', 'Completed course record deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete completed course record.');
        }
    }
}
