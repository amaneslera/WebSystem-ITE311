<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $db;
    protected $builder;
    protected $userModel;

    public function __construct()
    {
        
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('users');
        $this->userModel = new UserModel();
    }

    public function register()
    {
        helper(['form']);
        $data = [];

        if ($this->request->is('post')) {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|alpha_space',
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
                    'program_id' => $this->request->getPost('program_id') ?: null,
                    'year_level' => $this->request->getPost('year_level') ?: null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->builder->insert($newData)) {
                    session()->setFlashdata('success', 'Registration successful. You can now log in.');
                    return redirect()->to(base_url('login'));
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
            
            // Only allow active users (status = 'active') to login
            $user = $this->builder
                ->where('email', $email)
                ->where('status', 'active')
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

                // Redirect to unified dashboard (Auth::dashboard will render role-specific content)
                $data = [
                    'success' => true,
                    'message' => 'Welcome back, ' . $user['name'] . '! You have successfully logged in.',
                    'redirect' => 'dashboard',
                    'user' => [
                        'name' => $user['name'],
                        'role' => $user['role']
                    ]
                ];

                return $this->response->setJSON($data);
            
                
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
            return redirect()->to(base_url('login'))->with('error', 'Please log in to access the dashboard');
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
                
                // Count only active users (not soft-deleted)
                $data['total_teachers'] = $this->db->table('users')->where('role', 'teacher')->where('status', 'active')->countAllResults();
                $data['total_students'] = $this->db->table('users')->where('role', 'student')->where('status', 'active')->countAllResults();
                $data['total_courses'] = $this->db->tableExists('courses') ? 
                    $this->db->table('courses')->countAllResults() : 0;
                
                
                // Show only active users (not soft-deleted)
                $data['users'] = $this->db->table('users')
                    ->select('id, name, email, role, created_at')
                    ->where('status', 'active')
                    ->orderBy('created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray();
                
                // Get all courses with teacher names (exclude soft-deleted teachers)
                if ($this->db->tableExists('courses')) {
                    $data['all_courses'] = $this->db->table('courses')
                        ->select('courses.*, users.name as teacher_name')
                        ->join('users', 'users.id = courses.teacher_id AND users.status = \'active\'', 'left')
                        ->orderBy('courses.created_at', 'DESC')
                        ->get()->getResultArray();
                    
                    // Add student count for each course
                    foreach ($data['all_courses'] as &$course) {
                        if ($this->db->tableExists('enrollments')) {
                            $course['students_count'] = $this->db->table('enrollments')
                                ->where('course_id', $course['id'])
                                ->countAllResults();
                        } else {
                            $course['students_count'] = 0;
                        }
                    }
                } else {
                    $data['all_courses'] = [];
                }
                break;

            case 'teacher':
                $data['total_courses'] = $this->db->tableExists('courses') ? 
                    $this->db->table('courses')->where('teacher_id', $userId)->countAllResults() : 0;
                
                
                $data['courses'] = $this->db->tableExists('courses') ?
                    $this->db->table('courses')
                        ->where('teacher_id', $userId)
                        ->select('id, title, description')
                        ->get()->getResultArray() : [];

                $enrollmentModel = new \App\Models\EnrollmentModel();
                $courses = $data['courses'];
                foreach ($courses as &$course) {        
                    $course['students_count'] = count($enrollmentModel->getCourseEnrollments($course['id']));
                }
                unset($course);
                $data['courses'] = $courses;

                $uniqueStudentIds = [];

                foreach ($data['courses'] as $course) {
                    $enrollments = $enrollmentModel->getCourseEnrollments($course['id']);
                    foreach ($enrollments as $enrollment) {
                        $uniqueStudentIds[$enrollment['user_id']] = true;
                    }
                }
                $data['total_students'] = count($uniqueStudentIds);

                // Get total materials count for teacher
                $data['total_materials'] = 0;
                if ($this->db->tableExists('materials')) {
                    $courseIds = array_column($data['courses'], 'id');
                    if (!empty($courseIds)) {
                        $data['total_materials'] = $this->db->table('materials')
                            ->whereIn('course_id', $courseIds)
                            ->countAllResults();
                    }
                }

                $data['pending_assignments'] = [];
                $data['notifications'] = [];
                break;

            case 'student':
                $data['total_courses'] = 0;
                $data['enrolled_courses'] = [];
                $data['recent_grades'] = [];
                $data['upcoming_assignments'] = [];
                
                // Get student info for filtering
                $student = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
                
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
                    
                    // Get courses filtered by student's program and year level
                    $builder = $this->db->table('courses')
                        ->select('courses.*, users.name as teacher_name')
                        ->join('users', 'users.id = courses.teacher_id AND users.status = \'active\'')
                        ->where('courses.status', 'active');
                    
                    if (!empty($enrolledCourseIds)) {
                        $builder->whereNotIn('courses.id', $enrolledCourseIds);
                    }
                    
                    // Filter by student's program if set
                    if (!empty($student['program_id'])) {
                        $builder->groupStart()
                                ->where('courses.program_id', $student['program_id'])
                                ->orWhere('courses.program_id', null) // Include general courses
                                ->groupEnd();
                    }
                    
                    // Filter by student's year level if set
                    if (!empty($student['year_level'])) {
                        $builder->groupStart()
                                ->where('courses.year_level', $student['year_level'])
                                ->orWhere('courses.year_level', null) // Include courses for all years
                                ->groupEnd();
                    }
                    
                    $data['available_courses'] = $builder->get()->getResultArray();
                    $data['student_info'] = $student; // Pass student info to view
                } else {
                    $data['available_courses'] = [];
                    $data['student_info'] = $student;
                }

                // Get the total courses count
                $totalCourses = $this->db->table('enrollments')->where('user_id', $userId)->countAllResults();
                $data['total_courses'] = $totalCourses;
                session()->set('total_courses', $totalCourses); // Store in session for AJAX updates

                // Get enrolled courses (exclude inactive teachers)
                if ($this->db->tableExists('enrollments')) {
                    $data['enrolled_courses'] = $this->db->table('enrollments')
                        ->select('enrollments.*, courses.title, courses.description, users.name as teacher_name')
                        ->join('courses', 'courses.id = enrollments.course_id')
                        ->join('users', 'users.id = courses.teacher_id AND users.status = \'active\'')
                        ->where('enrollments.user_id', $userId)
                        ->get()
                        ->getResultArray();
                } else {
                    $data['enrolled_courses'] = [];
                }

                // Get materials for enrolled courses with course information
                $enrolledCourseIds = array_column($data['enrolled_courses'], 'course_id');
                $data['materials'] = [];
                if (!empty($enrolledCourseIds) && $this->db->tableExists('materials')) {
                    $data['materials'] = $this->db->table('materials')
                        ->select('materials.*, courses.title as course_title, courses.course_code')
                        ->join('courses', 'courses.id = materials.course_id')
                        ->whereIn('materials.course_id', $enrolledCourseIds)
                        ->orderBy('materials.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                }
                break;
        }

        // Step 3: Pass the data to the view
        return view('auth/dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}