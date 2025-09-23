<?php


namespace App\Controllers;

use CodeIgniter\Controller;

class TeacherController extends BaseController
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
        if (session()->get('role') !== 'teacher') {
            return redirect()->to('/login')->with('error', 'You do not have permission to access the teacher dashboard.');
        }
        
        // Get teacher ID
        $teacherId = session()->get('user_id');
        
        // Prepare data for dashboard view
        $data = [
            'title' => 'Teacher Dashboard',
            'name' => session()->get('name'),
        ];
        
        // Get teacher's courses if available
        if ($this->db->tableExists('courses')) {
            $coursesBuilder = $this->db->table('courses');
            $data['courses'] = $coursesBuilder->where('teacher_id', $teacherId)->get()->getResultArray();
            $data['total_courses'] = count($data['courses']);
        } else {
            $data['courses'] = [];
            $data['total_courses'] = 0;
        }
        
        // Get students in teacher's courses if available
        if ($this->db->tableExists('course_student')) {
            $data['students'] = $this->db->table('users')
                ->join('course_student', 'users.id = course_student.student_id')
                ->join('courses', 'courses.id = course_student.course_id')
                ->where('courses.teacher_id', $teacherId)
                ->where('users.role', 'student')
                ->select('users.id, users.name, users.email')
                ->distinct()
                ->get()
                ->getResultArray();
            
            $data['total_students'] = count($data['students']);
        } else {
            $data['students'] = [];
            $data['total_students'] = 0;
        }
        
        // Load the teacher dashboard view
        return view('teacher/dashboard', $data);
    }
}