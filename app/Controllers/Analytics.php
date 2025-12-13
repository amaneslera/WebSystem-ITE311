<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Analytics extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Admin access required');
        }

        $userModel = new UserModel();
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        // Get year level distribution for students
        $studentYearLevels = $this->getStudentYearLevelDistribution();
        
        // Get year level distribution for courses
        $courseYearLevels = $this->getCourseYearLevelDistribution();
        
        // Get enrollment statistics by year level
        $enrollmentStats = $this->getEnrollmentStatsByYearLevel();
        
        // Get overall statistics
        $totalStudents = $userModel->where('role', 'student')->where('status', 'active')->countAllResults();
        $totalCourses = $courseModel->where('status', 'active')->countAllResults();
        $totalEnrollments = $enrollmentModel->countAllResults();
        
        // Calculate average enrollments per year level
        $avgEnrollmentsByYear = $this->getAverageEnrollmentsByYear();

        $data = [
            'title' => 'Year Level Analytics',
            'studentYearLevels' => $studentYearLevels,
            'courseYearLevels' => $courseYearLevels,
            'enrollmentStats' => $enrollmentStats,
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
            'avgEnrollmentsByYear' => $avgEnrollmentsByYear
        ];

        return view('analytics/year_level', $data);
    }

    /**
     * Get student distribution by year level
     */
    private function getStudentYearLevelDistribution()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                COALESCE(year_level, 'Unspecified') as year_level,
                COUNT(*) as count
            FROM users 
            WHERE role = 'student' AND status = 'active'
            GROUP BY year_level
            ORDER BY 
                CASE 
                    WHEN year_level = '1st Year' THEN 1
                    WHEN year_level = '2nd Year' THEN 2
                    WHEN year_level = '3rd Year' THEN 3
                    WHEN year_level = '4th Year' THEN 4
                    WHEN year_level = '5th Year' THEN 5
                    ELSE 6
                END
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get course distribution by year level
     */
    private function getCourseYearLevelDistribution()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                COALESCE(year_level, 'Unspecified') as year_level,
                COUNT(*) as count
            FROM courses 
            WHERE status = 'active'
            GROUP BY year_level
            ORDER BY 
                CASE 
                    WHEN year_level = '1st Year' THEN 1
                    WHEN year_level = '2nd Year' THEN 2
                    WHEN year_level = '3rd Year' THEN 3
                    WHEN year_level = '4th Year' THEN 4
                    WHEN year_level = '5th Year' THEN 5
                    ELSE 6
                END
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get enrollment statistics by year level
     */
    private function getEnrollmentStatsByYearLevel()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                COALESCE(u.year_level, 'Unspecified') as year_level,
                COUNT(DISTINCT e.user_id) as enrolled_students,
                COUNT(e.id) as total_enrollments,
                ROUND(COUNT(e.id) / COUNT(DISTINCT e.user_id), 2) as avg_courses_per_student
            FROM users u
            LEFT JOIN enrollments e ON u.id = e.user_id
            WHERE u.role = 'student' AND u.status = 'active'
            GROUP BY u.year_level
            ORDER BY 
                CASE 
                    WHEN u.year_level = '1st Year' THEN 1
                    WHEN u.year_level = '2nd Year' THEN 2
                    WHEN u.year_level = '3rd Year' THEN 3
                    WHEN u.year_level = '4th Year' THEN 4
                    WHEN u.year_level = '5th Year' THEN 5
                    ELSE 6
                END
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get average enrollments by year level courses
     */
    private function getAverageEnrollmentsByYear()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                COALESCE(c.year_level, 'Unspecified') as year_level,
                COUNT(DISTINCT c.id) as course_count,
                COUNT(e.id) as total_enrollments,
                ROUND(COUNT(e.id) / COUNT(DISTINCT c.id), 2) as avg_students_per_course
            FROM courses c
            LEFT JOIN enrollments e ON c.id = e.course_id
            WHERE c.status = 'active'
            GROUP BY c.year_level
            ORDER BY 
                CASE 
                    WHEN c.year_level = '1st Year' THEN 1
                    WHEN c.year_level = '2nd Year' THEN 2
                    WHEN c.year_level = '3rd Year' THEN 3
                    WHEN c.year_level = '4th Year' THEN 4
                    WHEN c.year_level = '5th Year' THEN 5
                    ELSE 6
                END
        ");
        
        return $query->getResultArray();
    }
}
