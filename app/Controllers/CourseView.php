<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;

class CourseView extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $materialModel;
    protected $assignmentModel;
    protected $submissionModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->materialModel = new MaterialModel();
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
    }

    /**
     * Single page course view - role-based content
     */
    public function view($course_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in');
        }

        $role = session()->get('role');
        $user_id = session()->get('user_id');

        // Get course details with teacher info
        $course = $this->courseModel
            ->select('courses.*, users.name as teacher_name, users.email as teacher_email')
            ->join('users', 'users.id = courses.teacher_id', 'left')
            ->find($course_id);

        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found');
        }

        // Check permissions
        $hasAccess = false;
        if ($role == 'admin') {
            $hasAccess = true;
        } elseif ($role == 'teacher' && $course['teacher_id'] == $user_id) {
            $hasAccess = true;
        } elseif ($role == 'student' && $this->enrollmentModel->isEnrolled($user_id, $course_id)) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return redirect()->to('/dashboard')->with('error', 'You do not have access to this course');
        }

        // Get course materials
        $materials = $this->materialModel->getMaterialsByCourse($course_id);

        // Get assignments
        $assignments = $this->assignmentModel->getAssignmentsByCourse($course_id);

        // Get enrolled students count
        $enrollments = $this->enrollmentModel->getCourseEnrollments($course_id);
        $student_count = count($enrollments);

        $data = [
            'title' => $course['title'],
            'course' => $course,
            'materials' => $materials,
            'assignments' => $assignments,
            'student_count' => $student_count,
            'role' => $role,
            'user_id' => $user_id
        ];

        // Role-specific data
        if ($role == 'student') {
            // Get student's submissions
            $data['my_submissions'] = [];
            foreach ($assignments as $assignment) {
                $submission = $this->submissionModel->getSubmission($assignment['id'], $user_id);
                if ($submission) {
                    $data['my_submissions'][$assignment['id']] = $submission;
                }
            }

            // Get student's grade (if grades table exists)
            $data['my_grade'] = null;
            $data['enrollment'] = $this->enrollmentModel
                ->where(['user_id' => $user_id, 'course_id' => $course_id])
                ->first();

        } elseif ($role == 'teacher') {
            // Get students list
            $data['students'] = $enrollments;
            
            // Get pending submissions count
            $data['pending_count'] = $this->submissionModel
                ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                ->where('assignments.course_id', $course_id)
                ->whereIn('assignment_submissions.status', ['pending', 'late', 'resubmitted'])
                ->countAllResults();

        } elseif ($role == 'admin') {
            // Admin sees everything
            $data['students'] = $enrollments;
            $data['all_submissions'] = $this->submissionModel
                ->select('assignment_submissions.*, assignments.title as assignment_title, users.name as student_name')
                ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                ->join('users', 'users.id = assignment_submissions.user_id')
                ->where('assignments.course_id', $course_id)
                ->findAll();
        }

        return view('course/view', $data);
    }
}
