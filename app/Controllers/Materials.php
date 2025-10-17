<?php


namespace App\Controllers;

use App\Models\MaterialModel;
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
                return redirect()->to('/materials/upload/' . $course_id);
            }

            $file = $this->request->getFile('material_file');
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Configure upload preferences
                $uploadPath = WRITEPATH . 'uploads/materials';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
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
                        session()->setFlashdata('success', 'Material uploaded successfully!');
                        return redirect()->to('/materials/upload/' . $course_id);
                    } else {
                        // If database insert fails, delete the uploaded file
                        unlink($uploadPath . '/' . $newName);
                        $errors = $model->errors();
                        $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                        session()->setFlashdata('error', 'Failed to save material to database: ' . $errorMsg);
                        return redirect()->to('/materials/upload/' . $course_id);
                    }
                } else {
                    session()->setFlashdata('error', 'Failed to move uploaded file: ' . $file->getErrorString());
                    return redirect()->to('/materials/upload/' . $course_id);
                }
            } else {
                $errorMsg = 'Invalid file upload.';
                if ($file) {
                    $errorMsg .= ' Error: ' . $file->getErrorString();
                }
                session()->setFlashdata('error', $errorMsg);
                return redirect()->to('/materials/upload/' . $course_id);
            }
        }

        // Display upload form (you should create a view for this)
        return view('materials/upload', ['course_id' => $course_id]);
    }

    public function delete($material_id)
    {
        $model = new MaterialModel();
        $material = $model->find($material_id);

        if ($material) {
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
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in to download materials.');
        }

        $model = new \App\Models\MaterialModel();
        $material = $model->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Check if user is enrolled in the course
        $userId = session()->get('user_id');
        $enrolled = $this->db->table('enrollments')
            ->where('user_id', $userId)
            ->where('course_id', $material['course_id'])
            ->countAllResults();

        if (!$enrolled) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

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