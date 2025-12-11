<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\ScheduleModel;
use CodeIgniter\Controller;

class TeacherCourses extends Controller
{
    /**
     * Teacher: View assigned courses
     */
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }

        $teacherId = session()->get('user_id');
        $courseModel = new CourseModel();
        
        $data['courses'] = $courseModel->select('courses.*, 
                                                 COUNT(enrollments.id) as enrolled_count')
                                       ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
                                       ->where('courses.teacher_id', $teacherId)
                                       ->where('courses.status', 'active')
                                       ->groupBy('courses.id')
                                       ->findAll();

        return view('teacher/courses/index', $data);
    }

    /**
     * Teacher: View students enrolled in their course
     */
    public function viewStudents($courseId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }

        $teacherId = session()->get('user_id');
        $courseModel = new CourseModel();
        
        // Verify teacher owns this course
        $course = $courseModel->find($courseId);
        if (!$course || $course['teacher_id'] != $teacherId) {
            return redirect()->to('/teacher/courses')->with('error', 'Access denied');
        }

        $enrollmentModel = new EnrollmentModel();
        $data = [
            'course' => $course,
            'students' => $enrollmentModel->select('enrollments.*, users.name, users.email, 
                                                    users.student_id, users.year_level')
                                          ->join('users', 'users.id = enrollments.user_id')
                                          ->where('enrollments.course_id', $courseId)
                                          ->where('users.status', 'active')
                                          ->findAll()
        ];

        return view('teacher/courses/students', $data);
    }

    /**
     * Teacher: Enroll student to their assigned course
     */
    public function enrollStudent()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher access required'
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
        $teacherId = session()->get('user_id');

        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        // Verify teacher owns this course
        $course = $courseModel->find($courseId);
        if (!$course || $course['teacher_id'] != $teacherId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You can only enroll students in your assigned courses'
            ])->setStatusCode(403);
        }

        // Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student is already enrolled in this course'
            ])->setStatusCode(409);
        }

        // Check capacity
        if (!$courseModel->hasAvailableSeats($courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course is at full capacity'
            ])->setStatusCode(409);
        }

        // Check prerequisites (teacher can see warning but can override)
        $hasPrereq = $courseModel->hasPrerequisites($courseId, $studentId);
        
        // Enroll with transaction
        $result = $enrollmentModel->enrollUserWithTransaction([
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ]);

        if ($result['success']) {
            $message = 'Student enrolled successfully';
            if (!$hasPrereq) {
                $message .= ' (Warning: Prerequisites not met)';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'prerequisite_warning' => !$hasPrereq
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ])->setStatusCode(500);
        }
    }

    /**
     * Teacher: Unenroll student from their course
     */
    public function unenrollStudent()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher access required'
            ])->setStatusCode(403);
        }

        $studentId = $this->request->getPost('student_id');
        $courseId = $this->request->getPost('course_id');
        $teacherId = session()->get('user_id');

        $courseModel = new CourseModel();
        
        // Verify teacher owns this course
        $course = $courseModel->find($courseId);
        if (!$course || $course['teacher_id'] != $teacherId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $enrollmentModel = new EnrollmentModel();
        
        if ($enrollmentModel->unenrollUser($studentId, $courseId)) {
            // Decrement course count
            $courseModel->decrementEnrolled($courseId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student unenrolled successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to unenroll student'
            ])->setStatusCode(500);
        }
    }

    /**
     * Teacher: Get available students for enrollment (not already enrolled)
     */
    public function availableStudents($courseId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher access required'
            ])->setStatusCode(403);
        }

        $teacherId = session()->get('user_id');
        $courseModel = new CourseModel();
        
        // Verify teacher owns this course
        $course = $courseModel->find($courseId);
        if (!$course || $course['teacher_id'] != $teacherId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
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
     * Teacher: Bulk enroll multiple students
     */
    public function bulkEnroll()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teacher access required'
            ])->setStatusCode(403);
        }

        $studentIds = $this->request->getPost('student_ids');
        $courseId = $this->request->getPost('course_id');
        $teacherId = session()->get('user_id');

        if (empty($studentIds) || !is_array($studentIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No students selected'
            ])->setStatusCode(400);
        }

        $courseModel = new CourseModel();
        
        // Verify teacher owns this course
        $course = $courseModel->find($courseId);
        if (!$course || $course['teacher_id'] != $teacherId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $enrollmentModel = new EnrollmentModel();
        $userModel = new UserModel();
        
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

            // Check capacity
            if (!$courseModel->hasAvailableSeats($courseId)) {
                $errors[] = 'Course is full. Cannot enroll more students.';
                break;
            }

            // Enroll the student
            $result = $enrollmentModel->enrollUserWithTransaction([
                'user_id' => $studentId,
                'course_id' => $courseId,
                'notification_message' => 'You have been enrolled in ' . $course['title'] . ' by your teacher'
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
     * Teacher: View schedule for their courses
     */
    public function schedule()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }

        $teacherId = session()->get('user_id');
        $scheduleModel = new ScheduleModel();
        $academicYearModel = new \App\Models\AcademicYearModel();
        
        $currentYear = $academicYearModel->getCurrentAcademicYear();
        
        $data = [
            'schedule' => $scheduleModel->getTeacherSchedule(
                $teacherId,
                $currentYear['year_code'] ?? date('Y'),
                $currentYear['current_semester'] ?? '1st Semester'
            ),
            'current_year' => $currentYear
        ];

        return view('teacher/schedule', $data);
    }
}
