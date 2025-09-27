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

        // Admin-specific data
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

        return view('auth/dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}