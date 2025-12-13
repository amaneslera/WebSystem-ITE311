<?php

namespace App\Controllers;

use App\Models\CompletedCourseModel;
use App\Models\UserModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class CompletedCourses extends Controller
{
    public function manage($userId = null)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Admin access required');
        }

        $userModel = new UserModel();
        $completedCourseModel = new CompletedCourseModel();
        $courseModel = new CourseModel();

        if ($userId) {
            // Get specific student's completed courses
            $student = $userModel->find($userId);
            if (!$student || $student['role'] !== 'student') {
                return redirect()->to('/admin/students')->with('error', 'Student not found');
            }

            $completedCourses = $completedCourseModel->getUserCompletedCourses($userId);
            $availableCourses = $courseModel->where('status', 'active')->findAll();

            return view('admin/manage_completed_courses', [
                'student' => $student,
                'completedCourses' => $completedCourses,
                'availableCourses' => $availableCourses
            ]);
        } else {
            // Show all students with their completed course counts
            $students = $userModel->where('role', 'student')->findAll();
            
            foreach ($students as &$student) {
                $student['completed_count'] = $completedCourseModel
                    ->where('user_id', $student['id'])
                    ->countAllResults();
            }

            return view('admin/completed_courses_list', [
                'students' => $students
            ]);
        }
    }

    public function add()
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $completedCourseModel = new CompletedCourseModel();

        $data = [
            'user_id' => $this->request->getPost('user_id'),
            'course_id' => $this->request->getPost('course_id'),
            'completed_date' => $this->request->getPost('completed_date'),
            'grade' => $this->request->getPost('grade'),
            'institution' => $this->request->getPost('institution'),
            'notes' => $this->request->getPost('notes')
        ];

        // Check if already exists
        $existing = $completedCourseModel
            ->where('user_id', $data['user_id'])
            ->where('course_id', $data['course_id'])
            ->first();

        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This course is already marked as completed for this student'
            ]);
        }

        if ($completedCourseModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Completed course added successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add completed course'
            ]);
        }
    }

    public function update($id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $completedCourseModel = new CompletedCourseModel();

        $data = [
            'completed_date' => $this->request->getPost('completed_date'),
            'grade' => $this->request->getPost('grade'),
            'institution' => $this->request->getPost('institution'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($completedCourseModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Completed course updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update completed course'
            ]);
        }
    }

    public function delete($id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $completedCourseModel = new CompletedCourseModel();

        if ($completedCourseModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Completed course deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete completed course'
            ]);
        }
    }
}
