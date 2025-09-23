<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $db;
    protected $builder;

    public function __construct()
    {
        // Connect to database and users table
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
                    'role'       => 'student', // Changed from 'user' to 'student'
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
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[6]|max_length[255]'
            ];

            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                
                $user = $this->builder
                    ->where('email', $email)
                    ->get()
                    ->getRowArray();

                if ($user && password_verify($password, $user['password'])) {
                    // Store user data in session
                    session()->set([
                        'user_id'    => $user['id'],
                        'name'       => $user['name'],
                        'email'      => $user['email'],
                        'role'       => $user['role'],
                        'isLoggedIn' => true
                    ]);

                    // Set dashboard redirect URL based on role
                    $redirectUrl = '/dashboard';
                    switch ($user['role']) {
                        case 'admin':
                            $redirectUrl = '/admin/dashboard';
                            break;
                        case 'teacher':
                            $redirectUrl = '/teacher/dashboard';
                            break;
                        case 'student':
                            $redirectUrl = '/student/dashboard';
                            break;
                    }

                    // Return success status and redirect URL for AJAX handling
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
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $this->validator->getErrors()
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
        return view('auth/dashboard', ['role' => $role]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}