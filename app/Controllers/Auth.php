<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('users');
    }

    public function register()
    {
        helper(['form']);
        $data = [];

        if ($this->request->is('post')) {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'matches[password]'
            ];

            if ($this->validate($rules)) {
                $newData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role'       => 'student', 
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->builder->insert($newData)) {
                    session()->setFlashdata('success', 'Registration successful. You can now log in.');
                    return redirect()->to('/login');
                } else {
                    session()->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    public function login()
    {
        helper(['form']);
        $data = [];

        
        if ($this->request->is('post')) {
           
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            $user = $this->builder
                ->where('email', $email)
                ->get()
                ->getRowArray();

            if ($user && password_verify($password, $user['password'])) {
           
                session()->set([
                    'user_id'    => $user['id'],
                    'name'       => $user['name'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true
                ]);
                
                
                $redirectUrl = '/dashboard';
               

                
                $data = [
                    'success' => true,
                    'message' => 'Welcome back, ' . $user['name'] . '! You have successfully logged in.',
                    'redirect' => $redirectUrl,
                    'user' => [
                        'name' => $user['name'],
                        'role' => $user['role']
                    ]
                ];
                
                return $this->response->setJSON($data);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid email or password.'
                ]);
            }
        }

        return view('auth/login', $data);
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in to access the dashboard');
        }


        $role = session()->get('role');
        $userId = session()->get('user_id');
        $name = session()->get('name');

       
        $data = [
            'title' => ucfirst($role) . ' Dashboard',
            'role' => $role,
            'user_id' => $userId,
            'name' => $name
        ];

       
        switch ($role) {
            case 'admin':
                
                $data['total_teachers'] = $this->db->table('users')->where('role', 'teacher')->countAllResults();
                $data['total_students'] = $this->db->table('users')->where('role', 'student')->countAllResults();
                $data['total_courses'] = $this->db->tableExists('courses') ? 
                    $this->db->table('courses')->countAllResults() : 0;
                
                
                $data['users'] = $this->db->table('users')
                    ->select('id, name, email, role, created_at')
                    ->orderBy('created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray();
                break;

            case 'teacher':
                
                $data['total_courses'] = $this->db->tableExists('courses') ? 
                    $this->db->table('courses')->where('teacher_id', $userId)->countAllResults() : 0;
                
                
                $data['courses'] = $this->db->tableExists('courses') ?
                    $this->db->table('courses')
                        ->where('teacher_id', $userId)
                        ->select('id, title, description')
                        ->get()->getResultArray() : [];
                    
                $data['total_students'] = 0;
                $data['pending_assignments'] = [];
                $data['notifications'] = [];
                break;

            case 'student':
                $data['total_courses'] = 0;
                $data['enrolled_courses'] = [];
                $data['recent_grades'] = [];
                $data['upcoming_assignments'] = [];
                
                // Get available courses (courses the student is not enrolled in)
                if ($this->db->tableExists('courses')) {
                    // Get IDs of courses the student is already enrolled in
                    $enrolledCourseIds = [];
                    if ($this->db->tableExists('enrollments')) {
                        $enrolledCourseIds = $this->db->table('enrollments')
                            ->select('course_id')
                            ->where('user_id', $userId)
                            ->get()
                            ->getResultArray();
                        $enrolledCourseIds = array_column($enrolledCourseIds, 'course_id');
                    }
                    
                    // Get courses the student is not enrolled in
                    $builder = $this->db->table('courses')
                        ->select('courses.*, users.name as teacher_name')
                        ->join('users', 'users.id = courses.teacher_id');
                    
                    if (!empty($enrolledCourseIds)) {
                        $builder->whereNotIn('courses.id', $enrolledCourseIds);
                    }
                    
                    $data['available_courses'] = $builder->get()->getResultArray();
                } else {
                    $data['available_courses'] = [];
                }

                // Get the total courses count
                $totalCourses = $this->db->table('enrollments')->where('user_id', $userId)->countAllResults();
                $data['total_courses'] = $totalCourses;
                session()->set('total_courses', $totalCourses); // Store in session for AJAX updates
                break;
        }

        // Step 3: Pass the data to the view
        return view('auth/dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}