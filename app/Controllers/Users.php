<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

/**
 * Users Controller
 * Handles user management with soft delete functionality
 */
class Users extends Controller
{
    protected $db;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        // Use the new UserModel with soft delete support
        $this->userModel = new UserModel();
    }

    /**
     * Display all active users (Admin only)
     * Shows users with their status (active/inactive)
     */
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get all users with status information (excluding soft-deleted)
        $data = [
            'title' => 'User Management',
            'users' => $this->userModel->getAllUsersWithStatus()
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
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
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
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
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
            'password' => $this->request->getPost('password'), // Will be hashed in model
            'role' => $this->request->getPost('role')
        ];

        // Add student-specific fields if role is student
        if ($this->request->getPost('role') === 'student') {
            $userData['student_id'] = $this->request->getPost('student_id') ?: null;
            $userData['program_id'] = $this->request->getPost('program_id') ?: null;
            $userData['year_level'] = $this->request->getPost('year_level') ?: null;
        }

        if ($this->userModel->createUser($userData)) {
            return redirect()->to(base_url('users'))->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create user.')->withInput();
        }
    }

    /**
     * Show edit user form
     * Only shows if user is active (not soft-deleted)
     */
    public function edit($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get active user only (status = 'active')
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }

        // Prevent editing yourself
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'You cannot edit your own account. Please use the profile settings.');
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
     * Only updates active users (status = 'active')
     */
    public function update($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get active user only
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }

        // Prevent updating yourself
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'You cannot update your own account through this page.');
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
            'role' => $this->request->getPost('role')
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = $this->request->getPost('password'); // Will be hashed in model
        }

        // Add student-specific fields if role is student
        if ($this->request->getPost('role') === 'student') {
            $userData['student_id'] = $this->request->getPost('student_id') ?: null;
            $userData['program_id'] = $this->request->getPost('program_id') ?: null;
            $userData['year_level'] = $this->request->getPost('year_level') ?: null;
        }

        if ($this->userModel->updateUser($id, $userData)) {
            return redirect()->to(base_url('users'))->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update user.')->withInput();
        }
    }

    /**
     * Delete user - IDENTICAL TO DEACTIVATE (sets status = 'inactive')
     * Does NOT permanently remove from database
     * Cannot delete admin users or yourself
     */
    public function delete($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get active user only
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }

        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return redirect()->to(base_url('users'))->with('error', 'Cannot delete admin users. You can only edit admin accounts.');
        }

        // Prevent deleting yourself
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'You cannot delete your own account.');
        }

        // Perform deactivation (same as delete - just sets status to inactive)
        if ($this->userModel->deactivateUser($id)) {
            return redirect()->to(base_url('users'))->with('success', 'User deleted successfully.');
        } else {
            return redirect()->to(base_url('users'))->with('error', 'Failed to delete user.');
        }
    }
    
    /**
     * Deactivate user (sets status = 'inactive')
     * Cannot deactivate admin users or yourself
     */
    public function deactivate($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get user
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }

        // Prevent deactivating admin users
        if ($user['role'] === 'admin') {
            return redirect()->to(base_url('users'))->with('error', 'Cannot deactivate admin users.');
        }

        // Prevent deactivating yourself
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'You cannot deactivate your own account.');
        }

        // Perform deactivation
        if ($this->userModel->deactivateUser($id)) {
            return redirect()->to(base_url('users'))->with('success', 'User deactivated successfully.');
        } else {
            return redirect()->to(base_url('users'))->with('error', 'Failed to deactivate user.');
        }
    }
    
    /**
     * Activate user (sets status = 'active')
     */
    public function activate($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get user (check if exists, regardless of status)
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('users/inactive'))->with('error', 'User not found.');
        }

        // Perform activation
        if ($this->userModel->activateUser($id)) {
            return redirect()->to(base_url('users/inactive'))->with('success', 'User activated successfully.');
        } else {
            return redirect()->to(base_url('users/inactive'))->with('error', 'Failed to activate user.');
        }
    }
    
    /**
     * Display inactive users page
     * Shows all users with status = 'inactive'
     */
    public function inactive()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Admin only.');
        }

        // Get all inactive users
        $inactiveUsers = $this->userModel->getInactiveUsers();

        return view('users/inactive', [
            'title' => 'Inactive Users',
            'users' => $inactiveUsers
        ]);
    }
}
