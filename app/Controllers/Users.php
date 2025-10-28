<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Users extends Controller
{
    protected $db;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = $this->db->table('users');
    }

    /**
     * Display all users (Admin only)
     */
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $data = [
            'title' => 'User Management',
            'users' => $this->userModel
                ->select('id, name, email, role, created_at, updated_at')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getResultArray()
        ];

        return view('users/index', $data);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $data = [
            'title' => 'Add New User',
            'validation' => null
        ];

        return view('users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|alpha_space',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[255]',
            'password_confirm' => 'matches[password]',
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        if (!$this->validate($rules)) {
            return view('users/create', [
                'title' => 'Add New User',
                'validation' => $this->validator
            ]);
        }

        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->insert($userData)) {
            return redirect()->to('/users')->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create user.')->withInput();
        }
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $user = $this->userModel->where('id', $id)->get()->getRowArray();

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'validation' => null
        ];

        return view('users/edit', $data);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $user = $this->userModel->where('id', $id)->get()->getRowArray();

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        // Validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|alpha_space',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        // Optional password update
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]|max_length[255]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return view('users/edit', [
                'title' => 'Edit User',
                'user' => $user,
                'validation' => $this->validator
            ]);
        }

        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($this->userModel->where('id', $id)->update($userData)) {
            return redirect()->to('/users')->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update user.')->withInput();
        }
    }

    /**
     * Delete user (Cannot delete admin users)
     */
    public function delete($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $user = $this->userModel->where('id', $id)->get()->getRowArray();

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return redirect()->to('/users')->with('error', 'Cannot delete admin users. You can only edit admin accounts.');
        }

        // Prevent deleting yourself
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot delete your own account.');
        }

        if ($this->userModel->where('id', $id)->delete()) {
            return redirect()->to('/users')->with('success', 'User deleted successfully.');
        } else {
            return redirect()->to('/users')->with('error', 'Failed to delete user.');
        }
    }
}
