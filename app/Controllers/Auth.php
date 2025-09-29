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
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $userId = session()->get('user_id');
        $data = [
            'role' => $role,
        ];

     
        if ($role === 'admin') {
            $data['total_teachers'] = $this->db->table('users')->where('role', 'teacher')->countAllResults();
            $data['total_students'] = $this->db->table('users')->where('role', 'student')->countAllResults();
            
            if ($this->db->tableExists('courses')) {
                $data['total_courses'] = $this->db->table('courses')->countAllResults();
            } else {
                $data['total_courses'] = 0;
            }
            
            $data['users'] = $this->db->table('users')
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->get()->getResultArray();
        }
       
        else if ($role === 'teacher') {
            if ($this->db->tableExists('courses')) {
                $data['courses'] = $this->db->table('courses')
                    ->where('teacher_id', $userId)
                    ->get()->getResultArray();
                $data['total_courses'] = count($data['courses']);
                
              
                if ($this->db->tableExists('course_enrollments')) {
                    foreach ($data['courses'] as &$course) {
                        $course['students_count'] = $this->db->table('course_enrollments')
                            ->where('course_id', $course['id'])
                            ->countAllResults();
                    }
                }
            } else {
                $data['courses'] = [];
                $data['total_courses'] = 0;
            }
            
        
            $data['total_students'] = 0;
            if ($this->db->tableExists('course_enrollments') && !empty($data['courses'])) {
                $courseIds = array_column($data['courses'], 'id');
                $data['total_students'] = $this->db->table('course_enrollments')
                    ->whereIn('course_id', $courseIds)
                    ->countAllResults();
            }
            
            // Get pending assignments
            if ($this->db->tableExists('assignments') && $this->db->tableExists('submissions')) {
                $data['pending_assignments'] = $this->db->table('assignments')
                    ->select('assignments.id, assignments.title, assignments.due_date, courses.title as course_title, COUNT(submissions.id) as submission_count')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->join('submissions', 'submissions.assignment_id = assignments.id', 'left')
                    ->where('courses.teacher_id', $userId)
                    ->where('submissions.graded', 0)
                    ->groupBy('assignments.id')
                    ->having('submission_count >', 0)
                    ->get()->getResultArray();
            } else {
                $data['pending_assignments'] = [];
            }
            
           
            $data['notifications'] = [];
            
            if (!empty($data['pending_assignments'])) {
                foreach (array_slice($data['pending_assignments'], 0, 2) as $assignment) {
                    $data['notifications'][] = [
                        'message' => 'You have ' . $assignment['submission_count'] . ' submissions to grade for "' . $assignment['title'] . '"',
                        'time_ago' => '2 hours ago',
                        'type' => 'assignment'
                    ];
                }
            }
        }

        return view('auth/dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}