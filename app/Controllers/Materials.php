<?php

/**
 * Materials Controller - Handles secure file upload, download, and deletion
 * Security: File type/size validation, MIME checking, enrollment verification, secure storage
 */

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Helpers\NotificationHelper;
use CodeIgniter\Controller;

class Materials extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function upload($course_id)
    {
        // Security Check: Only teachers and admins can upload materials
        $role = session()->get('role');
        if (!session()->get('isLoggedIn') || ($role !== 'teacher' && $role !== 'admin')) {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Only teachers and administrators can upload materials.');
        }

        // Additional security: Verify teacher owns the course (if teacher role)
        if ($role === 'teacher') {
            $courseModel = new \App\Models\CourseModel();
            $course = $courseModel->find($course_id);
            if (!$course || $course['teacher_id'] != session()->get('user_id')) {
                return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. You can only upload materials to your own courses.');
            }
        }

        helper(['form', 'url']);
        $model = new MaterialModel();

        if (strtolower($this->request->getMethod()) === 'post') {
            // Load validation library
            $validation = \Config\Services::validation();

            // Set validation rules
            $validation->setRules([
                'material_file' => [
                    'label' => 'Material File',
                    'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,doc,docx,ppt,pptx,zip,rar,jpg,jpeg,png]',
                    'errors' => [
                        'uploaded' => 'Please select a file to upload.',
                        'max_size' => 'File size must not exceed 10MB.',
                        'ext_in'   => 'Invalid file type. Allowed: pdf, doc, docx, ppt, pptx, zip, rar, jpg, jpeg, png.'
                    ]
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $errors = $validation->getErrors();
                session()->setFlashdata('error', implode(' ', $errors));
                return redirect()->to(base_url('materials/upload/' . $course_id));
            }

            $file = $this->request->getFile('material_file');
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Security Mitigation #1: Validate file type using MIME type checking
                $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                 'application/zip', 'application/x-rar-compressed', 'image/jpeg', 'image/png'];
                
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    session()->setFlashdata('error', 'Invalid file type detected. MIME type: ' . $file->getMimeType());
                    return redirect()->to(base_url('materials/upload/' . $course_id));
                }

                // Configure upload preferences with secure settings
                $uploadPath = WRITEPATH . 'uploads/materials';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true); // Security: Use 0755 instead of 0777 to prevent excessive permissions
                }
                
                // Security Mitigation #2: Generate random filename to prevent file name manipulation attacks
                $newName = $file->getRandomName();
                
                // Move the file
                if ($file->move($uploadPath, $newName)) {
                    // Use relative path for database storage (consistent with existing records)
                    $relativePath = 'writable/uploads/materials/' . $newName;
                    
                    $data = [
                        'course_id'  => $course_id,
                        'file_name'  => $file->getClientName(),
                        'file_path'  => $relativePath,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Try to insert into database
                    $result = $model->insertMaterial($data);
                    
                    if ($result) {
                        // Notify all students enrolled in this course
                        try {
                            $notificationHelper = new NotificationHelper();
                            $courseModel = new \App\Models\CourseModel();
                            $course = $courseModel->find($course_id);
                            $courseName = $course ? $course['title'] : 'Course';
                            $fileName = $file->getClientName();
                            $message = "New material uploaded in {$courseName}: {$fileName}";
                            $notificationHelper->notifyStudentsInCourse($course_id, $message);
                        } catch (\Exception $e) {
                            log_message('error', 'Failed to send material upload notification: ' . $e->getMessage());
                        }
                        
                        session()->setFlashdata('success', 'Material uploaded successfully!');
                        return redirect()->to(base_url('materials/upload/' . $course_id));
                    } else {
                        // If database insert fails, delete the uploaded file
                        unlink($uploadPath . '/' . $newName);
                        $errors = $model->errors();
                        $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                        session()->setFlashdata('error', 'Failed to save material to database: ' . $errorMsg);
                        return redirect()->to(base_url('materials/upload/' . $course_id));
                    }
                } else {
                    session()->setFlashdata('error', 'Failed to move uploaded file: ' . $file->getErrorString());
                    return redirect()->to(base_url('materials/upload/' . $course_id));
                }
            } else {
                $errorMsg = 'Invalid file upload.';
                if ($file) {
                    $errorMsg .= ' Error: ' . $file->getErrorString();
                }
                session()->setFlashdata('error', $errorMsg);
                return redirect()->to(base_url('materials/upload/' . $course_id));
            }
        }

        // Display upload form (you should create a view for this)
        return view('materials/upload', ['course_id' => $course_id]);
    }

    public function delete($material_id)
    {
        // Security Check: Only teachers and admins can delete materials
        $role = session()->get('role');
        if (!session()->get('isLoggedIn') || ($role !== 'teacher' && $role !== 'admin')) {
            return redirect()->to(base_url('login'))->with('error', 'Access denied. Only teachers and administrators can delete materials.');
        }

        $model = new MaterialModel();
        $material = $model->find($material_id);

        if ($material) {
            // Additional security: Verify teacher owns the course (if teacher role)
            if ($role === 'teacher') {
                $courseModel = new \App\Models\CourseModel();
                $course = $courseModel->find($material['course_id']);
                if (!$course || $course['teacher_id'] != session()->get('user_id')) {
                    return redirect()->back()->with('error', 'Access denied. You can only delete materials from your own courses.');
                }
            }
            // Delete the file from the server
            $filePath = $material['file_path'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
            // Delete the record from the database
            if ($model->delete($material_id)) {
                return redirect()->back()->with('success', 'Material deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to delete material from database.');
            }
        } else {
            return redirect()->back()->with('error', 'Material not found.');
        }
    }

    public function download($material_id)
    {
        // Security Check #1: Authentication - Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in to download materials.');
        }

        $model = new \App\Models\MaterialModel();
        $material = $model->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Security Check #2: Authorization - Verify user has permission to access this material
        $userId = session()->get('user_id');
        $role = session()->get('role');

        // Teachers and admins can download any material
        // Students can only download materials from courses they're enrolled in
        if ($role === 'student') {
            $enrolled = $this->db->table('enrollments')
                ->where('user_id', $userId)
                ->where('course_id', $material['course_id'])
                ->countAllResults();

            if (!$enrolled) {
                // Security: Log unauthorized access attempt
                log_message('warning', 'Unauthorized download attempt by user ' . $userId . ' for material ' . $material_id);
                return redirect()->back()->with('error', 'Access denied. You must be enrolled in this course to download materials.');
            }
        } elseif ($role === 'teacher') {
            // Verify teacher owns the course
            $courseModel = new \App\Models\CourseModel();
            $course = $courseModel->find($material['course_id']);
            if ($course && $course['teacher_id'] != $userId && $role !== 'admin') {
                log_message('warning', 'Unauthorized download attempt by teacher ' . $userId . ' for material ' . $material_id);
                return redirect()->back()->with('error', 'Access denied. You can only download materials from your own courses.');
            }
        }
        // Admin has full access, no additional check needed

        // Download the file securely
        $filePath = $material['file_path'];
        
        // Handle both absolute and relative paths
        if (!is_file($filePath)) {
            // Try as relative path from project root
            $filePath = ROOTPATH . $filePath;
        }
        
        if (is_file($filePath)) {
            return $this->response->download($filePath, null);
        } else {
            return redirect()->back()->with('error', 'File not found. Path: ' . $material['file_path']);
        }
    }
}