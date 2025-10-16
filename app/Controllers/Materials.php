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

        if ($this->request->getMethod() === 'post') {
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
                return redirect()->back()->with('error', implode(' ', $validation->getErrors()));
            }

            $file = $this->request->getFile('material_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Configure upload preferences
                $uploadPath = WRITEPATH . 'uploads/materials';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);

                $data = [
                    'course_id'  => $course_id,
                    'file_name'  => $file->getClientName(),
                    'file_path'  => $uploadPath . '/' . $newName,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $model->insertMaterial($data);

                return redirect()->to('/courses/manage/' . $course_id)->with('success', 'Material uploaded successfully.');
            } else {
                return redirect()->back()->with('error', 'Invalid file upload.');
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
            if (is_file($material['file_path'])) {
                unlink($material['file_path']);
            }
            // Delete the record from the database
            $model->delete($material_id);

            return redirect()->back()->with('success', 'Material deleted successfully.');
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
        if (is_file($material['file_path'])) {
            return $this->response->download($material['file_path'], null);
        } else {
            return redirect()->back()->with('error', 'File not found.');
        }
    }
}