<?php


namespace App\Controllers;

use CodeIgniter\Controller;

class StudentController extends BaseController
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    public function dashboard()
    {
        // Authorization check
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        // Verify correct role
        if (session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'You do not have permission to access the student dashboard.');
        }
        
        // Get student ID
        $studentId = session()->get('user_id');
        
        // Prepare data for dashboard view
        $data = [
            'title' => 'Student Dashboard',
            'name' => session()->get('name'),
        ];
        
        // Get student's enrolled courses if available
        if ($this->db->tableExists('course_student') && $this->db->tableExists('courses')) {
            $data['courses'] = $this->db->table('courses')
                ->join('course_student', 'courses.id = course_student.course_id')
                ->where('course_student.student_id', $studentId)
                ->select('courses.*')
                ->get()
                ->getResultArray();
            
            $data['total_courses'] = count($data['courses']);
            
            // Get teachers of enrolled courses
            if (!empty($data['courses'])) {
                $courseIds = array_column($data['courses'], 'id');
                
                $data['teachers'] = $this->db->table('users')
                    ->join('courses', 'users.id = courses.teacher_id')
                    ->whereIn('courses.id', $courseIds)
                    ->select('users.id, users.name, users.email, courses.id as course_id, courses.title as course_title')
                    ->get()
                    ->getResultArray();
            } else {
                $data['teachers'] = [];
            }
        } else {
            $data['courses'] = [];
            $data['total_courses'] = 0;
            $data['teachers'] = [];
        }
        
        // Load the student dashboard view
        return view('student/dashboard', $data);
    }
}