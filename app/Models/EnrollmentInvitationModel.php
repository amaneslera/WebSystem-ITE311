<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentInvitationModel extends Model
{
    protected $table = 'enrollment_invitations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'course_id', 'type', 'status', 'invited_by', 
        'message', 'response_message', 'responded_at', 'created_at'
    ];
    protected $useTimestamps = false;

    /**
     * Create an invitation (admin/teacher invites student)
     */
    public function createInvitation($userId, $courseId, $invitedBy, $message = null)
    {
        // Check if invitation already exists
        $existing = $this->where('user_id', $userId)
                         ->where('course_id', $courseId)
                         ->where('status', 'pending')
                         ->first();
        
        if ($existing) {
            return ['success' => false, 'message' => 'An invitation already exists for this student'];
        }

        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'type' => 'invitation',
            'status' => 'pending',
            'invited_by' => $invitedBy,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->insert($data)) {
            return ['success' => true, 'invitation_id' => $this->insertID()];
        }

        return ['success' => false, 'message' => 'Failed to create invitation'];
    }

    /**
     * Create an enrollment request (student requests to enroll)
     */
    public function createRequest($userId, $courseId, $message = null)
    {
        // Check if request already exists
        $existing = $this->where('user_id', $userId)
                         ->where('course_id', $courseId)
                         ->where('status', 'pending')
                         ->first();
        
        if ($existing) {
            return ['success' => false, 'message' => 'You already have a pending request for this course'];
        }

        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'type' => 'request',
            'status' => 'pending',
            'invited_by' => null,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->insert($data)) {
            return ['success' => true, 'request_id' => $this->insertID()];
        }

        return ['success' => false, 'message' => 'Failed to create enrollment request'];
    }

    /**
     * Accept an invitation/request
     */
    public function accept($invitationId, $responseMessage = null)
    {
        $invitation = $this->find($invitationId);
        if (!$invitation || $invitation['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Invalid or expired invitation'];
        }

        $updated = $this->update($invitationId, [
            'status' => 'accepted',
            'response_message' => $responseMessage,
            'responded_at' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            return ['success' => true, 'invitation' => $invitation];
        }

        return ['success' => false, 'message' => 'Failed to accept invitation'];
    }

    /**
     * Decline an invitation/request
     */
    public function decline($invitationId, $responseMessage = null)
    {
        $invitation = $this->find($invitationId);
        if (!$invitation || $invitation['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Invalid or expired invitation'];
        }

        $updated = $this->update($invitationId, [
            'status' => 'declined',
            'response_message' => $responseMessage,
            'responded_at' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            return ['success' => true, 'invitation' => $invitation];
        }

        return ['success' => false, 'message' => 'Failed to decline invitation'];
    }

    /**
     * Get pending invitations for a student
     */
    public function getPendingInvitationsForStudent($userId)
    {
        return $this->select('enrollment_invitations.*, 
                             courses.title as course_title, 
                             courses.course_code,
                             courses.description as course_description,
                             inviter.name as invited_by_name,
                             inviter.role as invited_by_role')
                    ->join('courses', 'courses.id = enrollment_invitations.course_id')
                    ->join('users as inviter', 'inviter.id = enrollment_invitations.invited_by', 'left')
                    ->where('enrollment_invitations.user_id', $userId)
                    ->where('enrollment_invitations.type', 'invitation')
                    ->where('enrollment_invitations.status', 'pending')
                    ->orderBy('enrollment_invitations.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending enrollment requests for a course (for teacher/admin)
     */
    public function getPendingRequestsForCourse($courseId)
    {
        return $this->select('enrollment_invitations.*, 
                             users.name as student_name, 
                             users.email as student_email,
                             users.student_id')
                    ->join('users', 'users.id = enrollment_invitations.user_id')
                    ->where('enrollment_invitations.course_id', $courseId)
                    ->where('enrollment_invitations.type', 'request')
                    ->where('enrollment_invitations.status', 'pending')
                    ->orderBy('enrollment_invitations.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all pending requests for teacher's courses
     */
    public function getPendingRequestsForTeacher($teacherId)
    {
        return $this->select('enrollment_invitations.*, 
                             users.name as student_name, 
                             users.email as student_email,
                             users.student_id,
                             courses.title as course_title,
                             courses.course_code')
                    ->join('users', 'users.id = enrollment_invitations.user_id')
                    ->join('courses', 'courses.id = enrollment_invitations.course_id')
                    ->where('courses.teacher_id', $teacherId)
                    ->where('enrollment_invitations.type', 'request')
                    ->where('enrollment_invitations.status', 'pending')
                    ->orderBy('enrollment_invitations.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all pending requests (for admin)
     */
    public function getAllPendingRequests()
    {
        return $this->select('enrollment_invitations.*, 
                             users.name as student_name, 
                             users.email as student_email,
                             users.student_id,
                             courses.title as course_title,
                             courses.course_code,
                             teacher.name as teacher_name')
                    ->join('users', 'users.id = enrollment_invitations.user_id')
                    ->join('courses', 'courses.id = enrollment_invitations.course_id')
                    ->join('users as teacher', 'teacher.id = courses.teacher_id', 'left')
                    ->where('enrollment_invitations.type', 'request')
                    ->where('enrollment_invitations.status', 'pending')
                    ->orderBy('enrollment_invitations.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Check if student has pending invitation or request for course
     */
    public function hasPendingInvitationOrRequest($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('status', 'pending')
                    ->countAllResults() > 0;
    }

    /**
     * Cancel invitation (by sender)
     */
    public function cancel($invitationId)
    {
        return $this->update($invitationId, [
            'status' => 'cancelled',
            'responded_at' => date('Y-m-d H:i:s')
        ]);
    }
}
