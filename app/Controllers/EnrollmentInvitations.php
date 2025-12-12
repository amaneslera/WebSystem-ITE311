<?php

namespace App\Controllers;

use App\Models\EnrollmentInvitationModel;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\UserModel;
use App\Helpers\NotificationHelper;

class EnrollmentInvitations extends BaseController
{
    protected $invitationModel;
    protected $enrollmentModel;
    protected $courseModel;
    protected $userModel;

    public function __construct()
    {
        $this->invitationModel = new EnrollmentInvitationModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        $this->userModel = new UserModel();
    }

    /**
     * Unified method to respond to invitation (accept or decline)
     * Used by AJAX calls from dashboard
     */
    public function respondInvitation()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $invitationId = $this->request->getPost('invitation_id');
        $action = $this->request->getPost('action'); // 'accept' or 'decline'

        if (!$invitationId || !in_array($action, ['accept', 'decline'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $invitation = $this->invitationModel->find($invitationId);
        if (!$invitation || $invitation['user_id'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid invitation'
            ])->setStatusCode(404);
        }

        if ($action === 'accept') {
            // Accept the invitation
            $acceptResult = $this->invitationModel->accept($invitationId, '');
            
            if (!$acceptResult['success']) {
                return $this->response->setJSON($acceptResult)->setStatusCode(400);
            }

            // Enroll the student
            $enrollResult = $this->enrollmentModel->enrollUserWithTransaction([
                'user_id' => $invitation['user_id'],
                'course_id' => $invitation['course_id'],
                'enrollment_date' => date('Y-m-d H:i:s')
            ]);

            if (!$enrollResult['success']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $enrollResult['message']
                ])->setStatusCode(400);
            }

            // Notify the inviter
            try {
                $notificationHelper = new NotificationHelper();
                $student = $this->userModel->find($invitation['user_id']);
                $course = $this->courseModel->find($invitation['course_id']);
                
                $message = "{$student['name']} accepted your invitation to {$course['title']}";
                $notificationHelper->notifyUser($invitation['invited_by'], $message);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send invitation accept notification: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Invitation accepted! You have been enrolled in the course.'
            ]);

        } else {
            // Decline the invitation
            $declineResult = $this->invitationModel->decline($invitationId, '');
            
            if (!$declineResult['success']) {
                return $this->response->setJSON($declineResult)->setStatusCode(400);
            }

            // Notify the inviter
            try {
                $notificationHelper = new NotificationHelper();
                $student = $this->userModel->find($invitation['user_id']);
                $course = $this->courseModel->find($invitation['course_id']);
                
                $message = "{$student['name']} declined your invitation to {$course['title']}";
                $notificationHelper->notifyUser($invitation['invited_by'], $message);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send invitation decline notification: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Invitation declined.'
            ]);
        }
    }

    /**
     * Student: View pending invitations
     */
    public function myInvitations()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'My Course Invitations',
            'invitations' => $this->invitationModel->getPendingInvitationsForStudent(session()->get('user_id'))
        ];

        return view('enrollments/my_invitations', $data);
    }

    /**
     * Teacher/Admin: View pending enrollment requests
     */
    public function pendingRequests()
    {
        $role = session()->get('role');
        if ($role !== 'teacher' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if ($role === 'admin') {
            $requests = $this->invitationModel->getAllPendingRequests();
        } else {
            $requests = $this->invitationModel->getPendingRequestsForTeacher(session()->get('user_id'));
        }

        $data = [
            'title' => 'Pending Enrollment Requests',
            'requests' => $requests
        ];

        return view('enrollments/pending_requests', $data);
    }

    /**
     * Student: Accept invitation
     */
    public function acceptInvitation($invitationId)
    {
        try {
            if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }

            $invitation = $this->invitationModel->find($invitationId);
            if (!$invitation || $invitation['user_id'] != session()->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid invitation'
                ])->setStatusCode(404);
            }

            // Accept invitation
            $responseMessage = $this->request->getPost('response_message') ?? '';
            $acceptResult = $this->invitationModel->accept($invitationId, $responseMessage);
            
            if (!$acceptResult['success']) {
                return $this->response->setJSON($acceptResult)->setStatusCode(400);
            }

            // Enroll the student
            $enrollResult = $this->enrollmentModel->enrollUserWithTransaction([
                'user_id' => $invitation['user_id'],
                'course_id' => $invitation['course_id'],
                'enrollment_date' => date('Y-m-d H:i:s')
            ]);

            if (!$enrollResult['success']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Accepted but enrollment failed: ' . $enrollResult['message']
                ])->setStatusCode(500);
            }

            // Notify the inviter and teacher
            try {
                $notificationHelper = new NotificationHelper();
                $student = $this->userModel->find($invitation['user_id']);
                $course = $this->courseModel->find($invitation['course_id']);
                
                // Notify the inviter (admin)
                if ($invitation['invited_by']) {
                    $adminMessage = "{$student['name']} accepted the invitation and enrolled in {$course['title']}";
                    $notificationHelper->notifyUser($invitation['invited_by'], $adminMessage);
                }
                
                // Notify the teacher if different from inviter
                if (isset($course['teacher_id']) && $course['teacher_id']) {
                    if ($course['teacher_id'] != $invitation['invited_by']) {
                        $teacherMessage = "{$student['name']} is now enrolled in your course: {$course['title']}";
                        $notificationHelper->notifyTeacher($course['teacher_id'], $teacherMessage);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to send invitation acceptance notification: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'You have been enrolled in the course!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Accept invitation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Student: Decline invitation
     */
    public function declineInvitation($invitationId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $invitation = $this->invitationModel->find($invitationId);
        if (!$invitation || $invitation['user_id'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid invitation'
            ])->setStatusCode(404);
        }

        $result = $this->invitationModel->decline($invitationId, $this->request->getPost('message'));
        
        if ($result['success']) {
            // Notify the inviter
            try {
                $notificationHelper = new NotificationHelper();
                $student = $this->userModel->find($invitation['user_id']);
                $course = $this->courseModel->find($invitation['course_id']);
                
                $message = "{$student['name']} declined your invitation to {$course['title']}";
                $notificationHelper->notifyUser($invitation['invited_by'], $message);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send invitation decline notification: ' . $e->getMessage());
            }
        }

        return $this->response->setJSON($result);
    }

    /**
     * Teacher/Admin: Accept enrollment request
     */
    public function acceptRequest($requestId)
    {
        $role = session()->get('role');
        if ($role !== 'teacher' && $role !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $request = $this->invitationModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(404);
        }

        // Verify teacher owns the course
        if ($role === 'teacher') {
            $course = $this->courseModel->find($request['course_id']);
            if ($course['teacher_id'] != session()->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You can only approve requests for your courses'
                ])->setStatusCode(403);
            }
        }

        // Accept request
        $acceptResult = $this->invitationModel->accept($requestId, $this->request->getPost('message'));
        
        if (!$acceptResult['success']) {
            return $this->response->setJSON($acceptResult)->setStatusCode(400);
        }

        // Enroll the student
        $enrollResult = $this->enrollmentModel->enrollUserWithTransaction([
            'user_id' => $request['user_id'],
            'course_id' => $request['course_id'],
            'enrollment_date' => date('Y-m-d H:i:s')
        ]);

        if (!$enrollResult['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Accepted but enrollment failed: ' . $enrollResult['message']
            ])->setStatusCode(500);
        }

        // Notify the student
        try {
            $notificationHelper = new NotificationHelper();
            $course = $this->courseModel->find($request['course_id']);
            
            $message = "Your enrollment request for {$course['title']} has been approved!";
            $notificationHelper->notifyStudent($request['user_id'], $message);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send request approval notification: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Student has been enrolled!'
        ]);
    }

    /**
     * Teacher/Admin: Decline enrollment request
     */
    public function declineRequest($requestId)
    {
        $role = session()->get('role');
        if ($role !== 'teacher' && $role !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $request = $this->invitationModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(404);
        }

        // Verify teacher owns the course
        if ($role === 'teacher') {
            $course = $this->courseModel->find($request['course_id']);
            if ($course['teacher_id'] != session()->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You can only decline requests for your courses'
                ])->setStatusCode(403);
            }
        }

        $result = $this->invitationModel->decline($requestId, $this->request->getPost('message'));
        
        if ($result['success']) {
            // Notify the student
            try {
                $notificationHelper = new NotificationHelper();
                $course = $this->courseModel->find($request['course_id']);
                
                $message = "Your enrollment request for {$course['title']} has been declined";
                $notificationHelper->notifyStudent($request['user_id'], $message);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send request decline notification: ' . $e->getMessage());
            }
        }

        return $this->response->setJSON($result);
    }
}
