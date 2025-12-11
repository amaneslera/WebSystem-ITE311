<?php

namespace App\Controllers;

use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Assignments extends BaseController
{
    protected $assignmentModel;
    protected $submissionModel;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Teacher: List assignments for a course
     */
    public function index($course_id)
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $course = $this->courseModel->find($course_id);
        
        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found');
        }
        
        // Teachers can only manage their own courses, admins can manage any
        if ($role == 'teacher' && $course['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manage Assignments - ' . $course['title'],
            'course' => $course,
            'assignments' => $this->assignmentModel->getAssignmentsByCourse($course_id)
        ];

        return view('assignments/index', $data);
    }

    /**
     * Teacher: Create assignment form
     */
    public function create($course_id)
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $course = $this->courseModel->find($course_id);
        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found');
        }
        
        // Teachers can only manage their own courses, admins can manage any
        if ($role == 'teacher' && $course['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Create Assignment',
            'course' => $course
        ];

        return view('assignments/create', $data);
    }

    /**
     * Teacher: Store new assignment
     */
    public function store()
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'course_id' => 'required|numeric',
            'title' => 'required|min_length[3]|max_length[255]',
            'type' => 'required|in_list[assignment,exam,quiz,project]',
            'due_date' => 'permit_empty|valid_date',
            'max_points' => 'required|numeric|greater_than[0]',
            'allowed_file_types' => 'required',
            'max_file_size' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $course_id = $this->request->getPost('course_id');
        $course = $this->courseModel->find($course_id);
        
        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found');
        }
        
        $role = session()->get('role');
        // Teachers can only manage their own courses, admins can manage any
        if ($role == 'teacher' && $course['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'course_id' => $course_id,
            'teacher_id' => $course['teacher_id'], // Use course's teacher_id, not current user
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'due_date' => $this->request->getPost('due_date'),
            'max_points' => $this->request->getPost('max_points'),
            'allowed_file_types' => $this->request->getPost('allowed_file_types'),
            'max_file_size' => $this->request->getPost('max_file_size'),
            'allow_late_submission' => $this->request->getPost('allow_late_submission') ? 1 : 0,
            'status' => 'active'
        ];

        // Handle file attachment upload
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = WRITEPATH . 'uploads/assignment_files/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Validate file size (10MB max)
            if ($file->getSize() / 1024 > 10240) {
                return redirect()->back()->withInput()->with('error', 'Attachment file too large. Max 10MB.');
            }

            // Generate unique filename
            $newName = 'assignment_' . time() . '_' . $file->getRandomName();
            
            if ($file->move($uploadPath, $newName)) {
                $data['attachment_file'] = $file->getClientName();
                $data['attachment_path'] = $uploadPath . $newName;
            }
        }

        if ($this->assignmentModel->createAssignment($data)) {
            return redirect()->to('/assignments/index/' . $course_id)->with('success', 'Assignment created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create assignment');
    }

    /**
     * Teacher: Edit assignment form
     */
    public function edit($assignment_id)
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->getAssignmentDetails($assignment_id);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }
        
        // Teachers can only edit their own assignments, admins can edit any
        if ($role == 'teacher' && $assignment['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Edit Assignment',
            'assignment' => $assignment
        ];

        return view('assignments/edit', $data);
    }

    /**
     * Teacher: Update assignment
     */
    public function update($assignment_id)
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->find($assignment_id);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }
        
        // Teachers can only update their own assignments, admins can update any
        if ($role == 'teacher' && $assignment['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'type' => 'required|in_list[assignment,exam,quiz,project]',
            'due_date' => 'permit_empty|valid_date',
            'max_points' => 'required|numeric|greater_than[0]',
            'allowed_file_types' => 'required',
            'max_file_size' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[active,closed]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'due_date' => $this->request->getPost('due_date'),
            'max_points' => $this->request->getPost('max_points'),
            'allowed_file_types' => $this->request->getPost('allowed_file_types'),
            'max_file_size' => $this->request->getPost('max_file_size'),
            'allow_late_submission' => $this->request->getPost('allow_late_submission') ? 1 : 0,
            'status' => $this->request->getPost('status')
        ];

        if ($this->assignmentModel->updateAssignment($assignment_id, $data)) {
            return redirect()->to('/assignments/index/' . $assignment['course_id'])->with('success', 'Assignment updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update assignment');
    }

    /**
     * Teacher: Delete assignment
     */
    public function delete($assignment_id)
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $assignment = $this->assignmentModel->find($assignment_id);
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }
        
        // Teachers can only delete their own assignments, admins can delete any
        if ($role == 'teacher' && $assignment['teacher_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Delete all submissions first
        $submissions = $this->submissionModel->where('assignment_id', $assignment_id)->findAll();
        foreach ($submissions as $submission) {
            if (file_exists($submission['file_path'])) {
                unlink($submission['file_path']);
            }
        }
        $this->submissionModel->where('assignment_id', $assignment_id)->delete();

        if ($this->assignmentModel->deleteAssignment($assignment_id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Assignment deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete assignment']);
    }

    /**
     * Teacher: View submissions for an assignment
     */
    public function submissions($assignment_id)
    {
        $role = session()->get('role');
        if ($role != 'teacher' && $role != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->getAssignmentDetails($assignment_id);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }
        
        // Teachers can only view their own assignment submissions, admins can view any
        if ($role == 'teacher' && $assignment['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Assignment Submissions',
            'assignment' => $assignment,
            'submissions' => $this->submissionModel->getSubmissionsByAssignment($assignment_id),
            'stats' => $this->assignmentModel->getAssignmentStats($assignment_id)
        ];

        return view('assignments/submissions', $data);
    }

    /**
     * Teacher: Grade submission form
     */
    public function grade($submission_id)
    {
        if (session()->get('role') != 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $submission = $this->submissionModel->getSubmissionDetails($submission_id);
        if (!$submission) {
            return redirect()->to('/dashboard')->with('error', 'Submission not found');
        }

        // Verify teacher owns the assignment and get full assignment details
        $assignment = $this->assignmentModel->getAssignmentDetails($submission['assignment_id']);
        if ($assignment['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Grade Submission',
            'submission' => $submission,
            'assignment' => $assignment
        ];

        return view('assignments/grade', $data);
    }

    /**
     * Teacher: Store grade
     */
    public function storeGrade($submission_id)
    {
        if (session()->get('role') != 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $submission = $this->submissionModel->find($submission_id);
        if (!$submission) {
            return redirect()->to('/dashboard')->with('error', 'Submission not found');
        }

        $assignment = $this->assignmentModel->find($submission['assignment_id']);
        if ($assignment['teacher_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'grade' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[' . $assignment['max_points'] . ']',
            'feedback' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $grade = $this->request->getPost('grade');
        $feedback = $this->request->getPost('feedback');

        if ($this->submissionModel->gradeSubmission($submission_id, $grade, $feedback, session()->get('user_id'))) {
            return redirect()->to('/assignments/submissions/' . $assignment['id'])->with('success', 'Submission graded successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to grade submission');
    }

    /**
     * Student: View assignment details
     */
    public function view($assignment_id)
    {
        if (session()->get('role') != 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->getAssignmentDetails($assignment_id);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }

        // Check if student is enrolled in the course
        if (!$this->enrollmentModel->isEnrolled(session()->get('user_id'), $assignment['course_id'])) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course');
        }

        $submission = $this->submissionModel->getSubmission($assignment_id, session()->get('user_id'));
        $canSubmit = $this->submissionModel->canSubmit($assignment_id, session()->get('user_id'));

        $data = [
            'title' => 'Assignment Details',
            'assignment' => $assignment,
            'submission' => $submission,
            'can_submit' => $canSubmit
        ];

        return view('assignments/view', $data);
    }

    /**
     * Student: Submit assignment
     */
    public function submit($assignment_id)
    {
        if (session()->get('role') != 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->find($assignment_id);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }

        // Check enrollment
        if (!$this->enrollmentModel->isEnrolled(session()->get('user_id'), $assignment['course_id'])) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course');
        }

        // Check if can submit
        $canSubmit = $this->submissionModel->canSubmit($assignment_id, session()->get('user_id'));
        if (!$canSubmit['can_submit']) {
            return redirect()->back()->with('error', $canSubmit['reason']);
        }

        $file = $this->request->getFile('file');
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Please select a valid file');
        }

        // Validate file type
        $allowedTypes = explode(',', $assignment['allowed_file_types']);
        $fileExtension = $file->getExtension();
        if (!in_array($fileExtension, $allowedTypes)) {
            return redirect()->back()->with('error', 'Invalid file type. Allowed: ' . $assignment['allowed_file_types']);
        }

        // Validate file size (KB)
        if ($file->getSize() / 1024 > $assignment['max_file_size']) {
            return redirect()->back()->with('error', 'File size exceeds maximum allowed: ' . $assignment['max_file_size'] . ' KB');
        }

        // Create upload directory
        $uploadPath = WRITEPATH . 'uploads/assignments/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate unique filename
        $newName = $assignment_id . '_' . session()->get('user_id') . '_' . time() . '.' . $fileExtension;
        
        // Delete old submission file if exists
        $oldSubmission = $this->submissionModel->getSubmission($assignment_id, session()->get('user_id'));
        if ($oldSubmission && file_exists($oldSubmission['file_path'])) {
            unlink($oldSubmission['file_path']);
        }

        // Move file
        if ($file->move($uploadPath, $newName)) {
            $data = [
                'assignment_id' => $assignment_id,
                'user_id' => session()->get('user_id'),
                'file_name' => $file->getClientName(),
                'file_path' => $uploadPath . $newName,
                'status' => $canSubmit['status']
            ];

            if ($this->submissionModel->submitAssignment($data)) {
                return redirect()->to('/assignments/view/' . $assignment_id)->with('success', 'Assignment submitted successfully');
            }
        }

        return redirect()->back()->with('error', 'Failed to upload file');
    }

    /**
     * Student: View my submissions
     */
    public function mySubmissions()
    {
        if (session()->get('role') != 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'My Submissions',
            'submissions' => $this->submissionModel->getSubmissionsByStudent(session()->get('user_id'))
        ];

        return view('assignments/my_submissions', $data);
    }

    /**
     * Download submission file
     */
    public function download($submission_id)
    {
        $submission = $this->submissionModel->getSubmissionDetails($submission_id);
        if (!$submission) {
            return redirect()->back()->with('error', 'Submission not found');
        }

        $user_id = session()->get('user_id');
        $role = session()->get('role');

        // Check permissions
        $canDownload = false;
        if ($role == 'student' && $submission['user_id'] == $user_id) {
            $canDownload = true;
        } elseif ($role == 'teacher') {
            $assignment = $this->assignmentModel->find($submission['assignment_id']);
            if ($assignment['teacher_id'] == $user_id) {
                $canDownload = true;
            }
        } elseif ($role == 'admin') {
            $canDownload = true;
        }

        if (!$canDownload) {
            return redirect()->back()->with('error', 'Access denied');
        }

        if (!file_exists($submission['file_path'])) {
            return redirect()->back()->with('error', 'File not found');
        }

        return $this->response->download($submission['file_path'], null)->setFileName($submission['file_name']);
    }

    /**
     * Download assignment attachment file
     */
    public function downloadAttachment($assignment_id)
    {
        $assignment = $this->assignmentModel->find($assignment_id);
        if (!$assignment || empty($assignment['attachment_path'])) {
            return redirect()->back()->with('error', 'File not found');
        }

        $user_id = session()->get('user_id');
        $role = session()->get('role');

        // Check permissions
        $canDownload = false;
        if ($role == 'teacher' && $assignment['teacher_id'] == $user_id) {
            $canDownload = true;
        } elseif ($role == 'student' && $this->enrollmentModel->isEnrolled($user_id, $assignment['course_id'])) {
            $canDownload = true;
        } elseif ($role == 'admin') {
            $canDownload = true;
        }

        if (!$canDownload) {
            return redirect()->back()->with('error', 'Access denied');
        }

        if (!file_exists($assignment['attachment_path'])) {
            return redirect()->back()->with('error', 'File not found on server');
        }

        return $this->response->download($assignment['attachment_path'], null)->setFileName($assignment['attachment_file']);
    }
}
