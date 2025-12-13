<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel - Handles all user-related database operations with soft delete functionality
 * 
 * Soft Delete Feature:
 * - Users are never permanently deleted from the database
 * - Instead, the 'status' column is set to 'inactive'
 * - All queries automatically filter out inactive users (WHERE status = 'active')
 */
class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // We're implementing custom soft delete logic
    
    protected $allowedFields = [
        'name', 
        'email', 
        'password', 
        'role', 
        'status',
        'student_id',
        'program_id',
        'year_level',
        'program',
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = false; // Manual timestamp management for better control
    
    /**
     * Get all active users
     * Filters: WHERE status = 'active'
     * 
     * @param array $additionalWhere Additional where conditions (optional)
     * @return array Array of active users
     */
    public function getActiveUsers($additionalWhere = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, name, email, role, status, created_at, updated_at');
        $builder->where('status', 'active');
        
        // Apply additional where conditions if provided
        if (!empty($additionalWhere)) {
            $builder->where($additionalWhere);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get a single active user by ID
     * Returns NULL if user is inactive or doesn't exist
     * 
     * @param int $id User ID
     * @return array|null User data or NULL
     */
    public function getUserById($id)
    {
        return $this->db->table($this->table)
            ->select('id, name, email, role, status, created_at, updated_at')
            ->where('id', $id)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
    }
    
    /**
     * Get all inactive users
     * Filters: WHERE status = 'inactive'
     * 
     * @return array Array of inactive users
     */
    public function getInactiveUsers()
    {
        return $this->db->table($this->table)
            ->select('id, name, email, role, status, created_at, updated_at')
            ->where('status', 'inactive')
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get user with all related data (courses, enrollments, etc.)
     * Only returns if user is active
     * 
     * @param int $id User ID
     * @return array|null User data with relations or NULL
     */
    public function getUserWithRelations($id)
    {
        // Get basic user info (only if not deleted)
        $user = $this->getUserById($id);
        
        if (!$user) {
            return null;
        }
        
        // Add related data based on role
        switch ($user['role']) {
            case 'teacher':
                // Get courses taught by this teacher
                if ($this->db->tableExists('courses')) {
                    $user['courses'] = $this->db->table('courses')
                        ->where('teacher_id', $id)
                        ->get()
                        ->getResultArray();
                    
                    // Count students in each course
                    if ($this->db->tableExists('enrollments')) {
                        foreach ($user['courses'] as &$course) {
                            $course['students_count'] = $this->db->table('enrollments')
                                ->where('course_id', $course['id'])
                                ->countAllResults();
                        }
                    }
                }
                break;
                
            case 'student':
                // Get enrollments for this student
                if ($this->db->tableExists('enrollments')) {
                    $user['enrollments'] = $this->db->table('enrollments')
                        ->select('enrollments.*, courses.title, courses.description')
                        ->join('courses', 'courses.id = enrollments.course_id')
                        ->where('enrollments.user_id', $id)
                        ->get()
                        ->getResultArray();
                }
                break;
        }
        
        return $user;
    }
    

    
    /**
     * Check if an email exists among active users
     * Used for validation during registration/update
     * 
     * @param string $email Email to check
     * @param int|null $excludeId User ID to exclude (for updates)
     * @return bool True if email exists, False otherwise
     */
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('email', $email);
        $builder->where('status', 'active');
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Get active users by role
     * 
     * @param string $role Role to filter (admin, teacher, student)
     * @return array Array of users with specified role
     */
    public function getActiveUsersByRole($role)
    {
        return $this->getActiveUsers(['role' => $role]);
    }
    
    /**
     * Count active users
     * 
     * @param string|null $role Optional role filter
     * @return int Count of active users
     */
    public function countActiveUsers($role = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('status', 'active');
        
        if ($role !== null) {
            $builder->where('role', $role);
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Create a new user
     * 
     * @param array $data User data (name, email, password, role)
     * @return int|bool Insert ID on success, False on failure
     */
    public function createUser($data)
    {
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'active'; // Set default status
        
        // Hash password if not already hashed
        if (isset($data['password']) && strlen($data['password']) < 60) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Update user data
     * 
     * @param int $id User ID
     * @param array $data Data to update
     * @return bool True on success, False on failure
     */
    public function updateUser($id, $data)
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Hash password if provided and not already hashed
        if (isset($data['password']) && !empty($data['password']) && strlen($data['password']) < 60) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } elseif (isset($data['password']) && empty($data['password'])) {
            // Remove password from update if empty
            unset($data['password']);
        }
        
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }
    
    /**
     * Deactivate a user (sets status = 'inactive')
     * 
     * @param int $id User ID to deactivate
     * @return bool True on success, False on failure
     */
    public function deactivateUser($id)
    {
        // Check if user exists (regardless of status)
        $user = $this->db->table($this->table)
            ->where('id', $id)
            ->get()
            ->getRowArray();
        
        if (!$user) {
            return false;
        }
        
        $data = [
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }
    
    /**
     * Activate a user (sets status = 'active')
     * 
     * @param int $id User ID to activate
     * @return bool True on success, False on failure
     */
    public function activateUser($id)
    {
        // Check if user exists (regardless of status)
        $user = $this->db->table($this->table)
            ->where('id', $id)
            ->get()
            ->getRowArray();
        
        if (!$user) {
            return false;
        }
        
        $data = [
            'status' => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }
    
    /**
     * Get all users with status information
     * Returns only active users
     * 
     * @return array Array of active users
     */
    public function getAllUsersWithStatus()
    {
        return $this->db->table($this->table)
            ->select('id, name, email, role, status, created_at, updated_at')
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
