<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompletedCourseModel;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class StudentTranscript extends Controller
{
    public function index()
    {
        // Check if user is logged in and is a student
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to view your transcript');
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        // Get user information
        $userModel = new UserModel();
        $student = $userModel->find($userId);

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Student record not found');
        }

        // Get completed courses (transfer credits)
        $completedCourseModel = new CompletedCourseModel();
        $completedCourses = $completedCourseModel->getUserCompletedCourses($userId);

        // Get currently enrolled courses
        $enrollmentModel = new EnrollmentModel();
        $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);

        // Get available courses that student can take
        $courseModel = new CourseModel();
        $availableCourses = $courseModel->where('status', 'active')->findAll();

        // Filter available courses (not completed and not enrolled)
        $completedCourseIds = array_column($completedCourses, 'course_id');
        $enrolledCourseIds = array_column($enrolledCourses, 'course_id');
        $takenCourseIds = array_merge($completedCourseIds, $enrolledCourseIds);

        $remainingCourses = array_filter($availableCourses, function($course) use ($takenCourseIds) {
            return !in_array($course['id'], $takenCourseIds);
        });

        // Calculate statistics
        $totalCompleted = count($completedCourses);
        $totalEnrolled = count($enrolledCourses);
        $totalRemaining = count($remainingCourses);
        
        // Calculate credits - assume 3 units if not specified
        $totalCreditsCompleted = 0;
        foreach ($completedCourses as $course) {
            $totalCreditsCompleted += $course['units'] ?? 3;
        }
        
        $totalCreditsEnrolled = 0;
        foreach ($enrolledCourses as $course) {
            $totalCreditsEnrolled += $course['units'] ?? 3;
        }

        $data = [
            'title' => 'Academic Transcript',
            'student' => $student,
            'completedCourses' => $completedCourses,
            'enrolledCourses' => $enrolledCourses,
            'remainingCourses' => $remainingCourses,
            'totalCompleted' => $totalCompleted,
            'totalEnrolled' => $totalEnrolled,
            'totalRemaining' => $totalRemaining,
            'totalCreditsCompleted' => $totalCreditsCompleted,
            'totalCreditsEnrolled' => $totalCreditsEnrolled,
        ];

        return view('student/transcript', $data);
    }
}
