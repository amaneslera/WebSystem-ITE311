<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table = 'assignment_submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'assignment_id', 'user_id', 'file_name', 'file_path',
        'submitted_at', 'grade', 'feedback', 'graded_at', 'graded_by', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Submit an assignment
     */
    public function submitAssignment($data)
    {
        // Check if already submitted
        $existing = $this->where([
            'assignment_id' => $data['assignment_id'],
            'user_id' => $data['user_id']
        ])->first();

        if ($existing) {
            // Update existing submission (resubmission)
            $data['status'] = 'resubmitted';
            return $this->update($existing['id'], $data);
        }

        // New submission
        $data['submitted_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    /**
     * Get submission by assignment and student
     */
    public function getSubmission($assignment_id, $user_id)
    {
        return $this->where([
            'assignment_id' => $assignment_id,
            'user_id' => $user_id
        ])->first();
    }

    /**
     * Get all submissions for an assignment
     */
    public function getSubmissionsByAssignment($assignment_id)
    {
        return $this->select('assignment_submissions.*, users.name as student_name, users.email as student_email')
            ->join('users', 'users.id = assignment_submissions.user_id')
            ->where('assignment_submissions.assignment_id', $assignment_id)
            ->orderBy('assignment_submissions.submitted_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all submissions by a student
     */
    public function getSubmissionsByStudent($user_id)
    {
        return $this->select('assignment_submissions.*, assignments.title as assignment_title, assignments.type, courses.title as course_title, courses.course_code')
            ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
            ->join('courses', 'courses.id = assignments.course_id')
            ->where('assignment_submissions.user_id', $user_id)
            ->orderBy('assignment_submissions.submitted_at', 'DESC')
            ->findAll();
    }

    /**
     * Grade a submission
     */
    public function gradeSubmission($submission_id, $grade, $feedback, $graded_by)
    {
        return $this->update($submission_id, [
            'grade' => $grade,
            'feedback' => $feedback,
            'graded_at' => date('Y-m-d H:i:s'),
            'graded_by' => $graded_by,
            'status' => 'graded'
        ]);
    }

    /**
     * Get submission with full details
     */
    public function getSubmissionDetails($submission_id)
    {
        return $this->select('assignment_submissions.*, 
                            assignments.title as assignment_title, 
                            assignments.max_points,
                            assignments.due_date,
                            users.name as student_name, 
                            users.email as student_email,
                            courses.title as course_title,
                            grader.name as grader_name')
            ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
            ->join('users', 'users.id = assignment_submissions.user_id')
            ->join('courses', 'courses.id = assignments.course_id')
            ->join('users as grader', 'grader.id = assignment_submissions.graded_by', 'left')
            ->where('assignment_submissions.id', $submission_id)
            ->first();
    }

    /**
     * Delete submission file and record
     */
    public function deleteSubmission($submission_id)
    {
        $submission = $this->find($submission_id);
        if ($submission && file_exists($submission['file_path'])) {
            unlink($submission['file_path']);
        }
        return $this->delete($submission_id);
    }

    /**
     * Check if student can submit (late submission check)
     */
    public function canSubmit($assignment_id, $user_id)
    {
        $db = \Config\Database::connect();
        $assignment = $db->table('assignments')->where('id', $assignment_id)->get()->getRowArray();
        
        if (!$assignment) {
            return ['can_submit' => false, 'reason' => 'Assignment not found'];
        }

        if ($assignment['status'] == 'closed') {
            return ['can_submit' => false, 'reason' => 'Assignment is closed'];
        }

        $now = date('Y-m-d H:i:s');
        $is_late = $assignment['due_date'] && $now > $assignment['due_date'];

        // Check if past extended deadline
        if ($assignment['extended_deadline'] && $now > $assignment['extended_deadline']) {
            return ['can_submit' => false, 'reason' => 'Past extended deadline'];
        }

        // Check if past due date and late submissions not allowed
        if ($is_late && !$assignment['allow_late_submission']) {
            return ['can_submit' => false, 'reason' => 'Past due date and late submissions not allowed'];
        }

        // Check submission attempts limit
        $attemptCount = $this->where([
            'assignment_id' => $assignment['id'],
            'user_id' => $user_id
        ])->countAllResults();

        if ($attemptCount >= $assignment['max_attempts']) {
            return ['can_submit' => false, 'reason' => 'Maximum submission attempts reached (' . $assignment['max_attempts'] . ')'];
        }

        return [
            'can_submit' => true, 
            'is_late' => $is_late,
            'status' => $is_late ? 'late' : 'pending',
            'attempts_remaining' => $assignment['max_attempts'] - $attemptCount
        ];
    }

    /**
     * Get pending submissions for grading
     */
    public function getPendingSubmissions($teacher_id = null)
    {
        $query = $this->select('assignment_submissions.*, 
                                assignments.title as assignment_title,
                                assignments.type,
                                users.name as student_name,
                                courses.title as course_title')
            ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
            ->join('users', 'users.id = assignment_submissions.user_id')
            ->join('courses', 'courses.id = assignments.course_id')
            ->whereIn('assignment_submissions.status', ['pending', 'late', 'resubmitted']);

        if ($teacher_id) {
            $query->where('assignments.teacher_id', $teacher_id);
        }

        return $query->orderBy('assignment_submissions.submitted_at', 'ASC')->findAll();
    }
}
