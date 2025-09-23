<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AdminController extends BaseController
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
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'You do not have permission to access the admin dashboard.');
        }
        
        // Prepare data for dashboard view
        $data = [
            'title' => 'Admin Dashboard',
            'name' => session()->get('name'),
        ];
        
        // Get list of all users
        $usersBuilder = $this->db->table('users');
        $data['users'] = $usersBuilder->get()->getResultArray();
        
        // Get system statistics
        $data['total_teachers'] = $usersBuilder->where('role', 'teacher')->countAllResults();
        $data['total_students'] = $usersBuilder->where('role', 'student')->countAllResults();
        
        // Get list of courses if available
        if ($this->db->tableExists('courses')) {
            $coursesBuilder = $this->db->table('courses');
            $data['courses'] = $coursesBuilder->get()->getResultArray();
            $data['total_courses'] = $coursesBuilder->countAllResults();
        } else {
            $data['courses'] = [];
            $data['total_courses'] = 0;
        }
        
        // Load the admin dashboard view
        return view('admin/dashboard', $data);
    }
    
    public function users()
    {
        // Authorization check
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Manage Users',
            'users' => $this->db->table('users')->get()->getResultArray()
        ];
        
        return view('admin/users', $data);
    }
    
    public function courses()
    {
        // Authorization check
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Manage Courses'
        ];
        
        if ($this->db->tableExists('courses')) {
            $data['courses'] = $this->db->table('courses')
                ->join('users', 'users.id = courses.teacher_id', 'left')
                ->select('courses.*, users.name as teacher_name')
                ->get()
                ->getResultArray();
        } else {
            $data['courses'] = [];
        }
        
        return view('admin/courses', $data);
    }
}