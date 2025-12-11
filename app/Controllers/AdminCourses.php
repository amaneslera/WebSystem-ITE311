<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;
use App\Models\RoomModel;
use App\Models\ScheduleModel;
use App\Models\AcademicYearModel;
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
            $courseModel = new CourseModel();
            
            $data = [
                'course_code' => $this->request->getPost('course_code'),
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id'),
                'department' => $this->request->getPost('department'),
                'semester' => $this->request->getPost('semester'),
                'units' => $this->request->getPost('units'),
                'year_level' => $this->request->getPost('year_level'),
                'program_id' => $this->request->getPost('program_id'),
                'max_students' => $this->request->getPost('max_students'),
                'lecture_hours' => $this->request->getPost('lecture_hours'),
                'lab_hours' => $this->request->getPost('lab_hours'),
                'prerequisite_course_ids' => json_encode($this->request->getPost('prerequisites') ?? []),
                'status' => 'active',
                'current_enrolled' => 0
            ];

            if ($courseModel->insert($data)) {
                return redirect()->to('/admin/courses')->with('success', 'Course created successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to create course')->withInput();
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

        if ($this->request->getMethod() === 'post') {
            $data = [
                'course_code' => $this->request->getPost('course_code'),
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id'),
                'department' => $this->request->getPost('department'),
                'semester' => $this->request->getPost('semester'),
                'units' => $this->request->getPost('units'),
                'year_level' => $this->request->getPost('year_level'),
                'program_id' => $this->request->getPost('program_id'),
                'max_students' => $this->request->getPost('max_students'),
                'lecture_hours' => $this->request->getPost('lecture_hours'),
                'lab_hours' => $this->request->getPost('lab_hours'),
                'prerequisite_course_ids' => json_encode($this->request->getPost('prerequisites') ?? []),
                'status' => $this->request->getPost('status')
            ];

            if ($courseModel->update($courseId, $data)) {
                return redirect()->to('/admin/courses')->with('success', 'Course updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update course');
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

        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        // Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student is already enrolled in this course'
            ])->setStatusCode(409);
        }

        // Admin can override capacity, but still check
        if (!$courseModel->hasAvailableSeats($courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Warning: Course is at full capacity'
            ])->setStatusCode(409);
        }

        // Enroll with transaction (admin override - skip prerequisite/schedule checks)
        $result = $enrollmentModel->enrollUserWithTransaction([
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ]);

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student enrolled successfully by admin'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
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
        $userModel = new UserModel();
        
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ])->setStatusCode(404);
        }

        $enrolled = 0;
        $failed = 0;
        $errors = [];

        foreach ($studentIds as $studentId) {
            // Check if already enrolled
            if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
                $user = $userModel->find($studentId);
                $errors[] = ($user['name'] ?? 'Student') . ' is already enrolled';
                $failed++;
                continue;
            }

            // Admin can override capacity - just enroll directly
            $result = $enrollmentModel->enrollUserWithTransaction([
                'user_id' => $studentId,
                'course_id' => $courseId,
                'notification_message' => 'You have been enrolled in ' . $course['title'] . ' by admin'
            ]);

            if ($result['success']) {
                $enrolled++;
            } else {
                $failed++;
                $user = $userModel->find($studentId);
                $errors[] = ($user['name'] ?? 'Student') . ': ' . $result['message'];
            }
        }

        $message = "Enrolled $enrolled student(s).";
        if ($failed > 0) {
            $message .= " $failed failed.";
            if (!empty($errors)) {
                $message .= "\n\nErrors:\n" . implode("\n", $errors);
            }
        }

        return $this->response->setJSON([
            'success' => $enrolled > 0,
            'enrolled_count' => $enrolled,
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
