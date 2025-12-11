<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id', 'teacher_id', 'title', 'description', 'attachment_file', 'attachment_path', 'type',
        'due_date', 'max_points', 'allowed_file_types', 'max_file_size',
        'allow_late_submission', 'max_attempts', 'extended_deadline', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all assignments for a specific course
     */
    public function getAssignmentsByCourse($course_id)
    {
        return $this->select('assignments.*, users.name as teacher_name')
            ->join('users', 'users.id = assignments.teacher_id')
            ->where('assignments.course_id', $course_id)
            ->orderBy('assignments.due_date', 'ASC')
            ->findAll();
    }

    /**
     * Get assignment with teacher and course details
     */
    public function getAssignmentDetails($assignment_id)
    {
        return $this->select('assignments.*, users.name as teacher_name, courses.title as course_title, courses.course_code')
            ->join('users', 'users.id = assignments.teacher_id')
            ->join('courses', 'courses.id = assignments.course_id')
            ->where('assignments.id', $assignment_id)
            ->first();
    }

    /**
     * Get assignments created by a specific teacher
     */
    public function getAssignmentsByTeacher($teacher_id)
    {
        return $this->select('assignments.*, courses.title as course_title, courses.course_code')
            ->join('courses', 'courses.id = assignments.course_id')
            ->where('assignments.teacher_id', $teacher_id)
            ->orderBy('assignments.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get pending assignments for a teacher (need grading)
     */
    public function getPendingAssignments($teacher_id)
    {
        return $this->select('assignments.*, COUNT(assignment_submissions.id) as pending_count, courses.title as course_title')
            ->join('courses', 'courses.id = assignments.course_id')
            ->join('assignment_submissions', 'assignment_submissions.assignment_id = assignments.id AND assignment_submissions.status = "pending"', 'left')
            ->where('assignments.teacher_id', $teacher_id)
            ->where('assignments.status', 'active')
            ->groupBy('assignments.id')
            ->having('pending_count >', 0)
            ->orderBy('assignments.due_date', 'ASC')
            ->findAll();
    }

    /**
     * Get upcoming assignments for a student (enrolled courses)
     */
    public function getUpcomingAssignmentsForStudent($user_id)
    {
        return $this->select('assignments.*, courses.title as course_title, courses.course_code, assignment_submissions.status as submission_status')
            ->join('courses', 'courses.id = assignments.course_id')
            ->join('enrollments', 'enrollments.course_id = courses.id AND enrollments.user_id = ' . $user_id)
            ->join('assignment_submissions', 'assignment_submissions.assignment_id = assignments.id AND assignment_submissions.user_id = ' . $user_id, 'left')
            ->where('assignments.status', 'active')
            ->where('assignments.due_date >=', date('Y-m-d H:i:s'))
            ->orderBy('assignments.due_date', 'ASC')
            ->findAll();
    }

    /**
     * Create new assignment
     */
    public function createAssignment($data)
    {
        return $this->insert($data);
    }

    /**
     * Update assignment
     */
    public function updateAssignment($assignment_id, $data)
    {
        return $this->update($assignment_id, $data);
    }

    /**
     * Delete assignment
     */
    public function deleteAssignment($assignment_id)
    {
        return $this->delete($assignment_id);
    }

    /**
     * Check if teacher owns assignment
     */
    public function isTeacherAssignment($assignment_id, $teacher_id)
    {
        $assignment = $this->find($assignment_id);
        return $assignment && $assignment['teacher_id'] == $teacher_id;
    }

    /**
     * Get assignment statistics
     */
    public function getAssignmentStats($assignment_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('assignment_submissions');
        
        $stats = [
            'total_enrolled' => 0,
            'submitted' => 0,
            'pending' => 0,
            'graded' => 0,
            'late' => 0
        ];

        // Get total enrolled students
        $assignment = $this->find($assignment_id);
        if ($assignment) {
            $enrollmentBuilder = $db->table('enrollments');
            $stats['total_enrolled'] = $enrollmentBuilder->where('course_id', $assignment['course_id'])->countAllResults();
        }

        // Get submission stats
        $stats['submitted'] = $builder->where('assignment_id', $assignment_id)->countAllResults();
        $stats['pending'] = $builder->where(['assignment_id' => $assignment_id, 'status' => 'pending'])->countAllResults();
        $stats['graded'] = $builder->where(['assignment_id' => $assignment_id, 'status' => 'graded'])->countAllResults();
        $stats['late'] = $builder->where(['assignment_id' => $assignment_id, 'status' => 'late'])->countAllResults();

        return $stats;
    }
}
