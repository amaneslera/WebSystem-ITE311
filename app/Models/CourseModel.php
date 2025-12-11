<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'course_code',
        'title',
        'description',
        'teacher_id',
        'max_students',
        'current_enrolled',
        'status',
        'department',
        'semester',
        'units',
        'year_level',
        'program_id',
        'prerequisite_course_ids',
        'lecture_hours',
        'lab_hours',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'code'        => 'permit_empty|max_length[20]',
        'course_code' => 'permit_empty|max_length[20]',
        'title'       => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'teacher_id'  => 'required|is_not_unique[users.id]',
        'max_students' => 'permit_empty|integer|greater_than[0]',
        'status'      => 'permit_empty|in_list[active,inactive,archived]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Course title is required'
        ],
        'teacher_id' => [
            'required'        => 'Teacher is required',
            'is_not_unique'   => 'Invalid teacher ID'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get all active courses with teacher information
     */
    public function getCoursesWithTeacher()
    {
        return $this->select('courses.*, users.name as teacher_name, users.email as teacher_email')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->where('courses.status', 'active')
                    ->where('users.status', 'active')
                    ->findAll();
    }

    /**
     * Get course with teacher information
     */
    public function getCourseWithTeacher($courseId)
    {
        return $this->select('courses.*, users.name as teacher_name, users.email as teacher_email')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->where('courses.id', $courseId)
                    ->where('courses.status', 'active')
                    ->where('users.status', 'active')
                    ->first();
    }

    /**
     * Check if course has available seats
     */
    public function hasAvailableSeats($courseId)
    {
        $course = $this->find($courseId);
        
        if (!$course) {
            return false;
        }

        // If max_students is null or 0, unlimited enrollment
        if (empty($course['max_students'])) {
            return true;
        }

        $currentEnrolled = $course['current_enrolled'] ?? 0;
        return $currentEnrolled < $course['max_students'];
    }

    /**
     * Increment enrolled student count
     */
    public function incrementEnrolled($courseId)
    {
        $course = $this->find($courseId);
        
        if (!$course) {
            return false;
        }

        $currentEnrolled = $course['current_enrolled'] ?? 0;
        return $this->update($courseId, [
            'current_enrolled' => $currentEnrolled + 1
        ]);
    }

    /**
     * Decrement enrolled student count
     */
    public function decrementEnrolled($courseId)
    {
        $course = $this->find($courseId);
        
        if (!$course) {
            return false;
        }

        $currentEnrolled = $course['current_enrolled'] ?? 0;
        if ($currentEnrolled <= 0) {
            return false;
        }

        return $this->update($courseId, [
            'current_enrolled' => $currentEnrolled - 1
        ]);
    }

    /**
     * Get active courses count
     */
    public function getActiveCourseCount()
    {
        return $this->where('status', 'active')->countAllResults();
    }

    /**
     * Check if student meets course prerequisites
     * Now checks both completed courses (for transferees) and enrollments
     */
    public function hasPrerequisites($courseId, $userId)
    {
        $course = $this->find($courseId);
        
        if (!$course || empty($course['prerequisite_course_ids'])) {
            return true; // No prerequisites required
        }

        $prerequisiteIds = json_decode($course['prerequisite_course_ids'], true);
        if (empty($prerequisiteIds)) {
            return true;
        }

        // Check if student has completed or is enrolled in all prerequisite courses
        $enrollmentModel = new EnrollmentModel();
        $completedCourseModel = new \App\Models\CompletedCourseModel();
        
        foreach ($prerequisiteIds as $prereqId) {
            // First check if student has completed the course (for transferees)
            $hasCompleted = $completedCourseModel->hasCompletedCourse($userId, $prereqId);
            
            // If not completed, check if enrolled
            $isEnrolled = $enrollmentModel->isAlreadyEnrolled($userId, $prereqId);
            
            // If neither completed nor enrolled, prerequisite not met
            if (!$hasCompleted && !$isEnrolled) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get courses by program and year level
     */
    public function getCoursesByProgramAndYear($programId, $yearLevel, $semester = null)
    {
        $builder = $this->where('program_id', $programId)
                        ->where('year_level', $yearLevel)
                        ->where('status', 'active');

        if ($semester) {
            $builder->where('semester', $semester);
        }

        return $builder->findAll();
    }

    /**
     * Get course with full details including schedule
     */
    public function getCourseFullDetails($courseId)
    {
        $course = $this->select('courses.*, users.name as teacher_name, 
                                programs.program_name, programs.program_code')
                       ->join('users', 'users.id = courses.teacher_id', 'left')
                       ->join('programs', 'programs.id = courses.program_id', 'left')
                       ->where('courses.id', $courseId)
                       ->first();

        if ($course) {
            // Get schedules
            $scheduleModel = new \App\Models\ScheduleModel();
            $course['schedules'] = $scheduleModel->select('schedules.*, rooms.room_number, rooms.building')
                                                 ->join('rooms', 'rooms.id = schedules.room_id', 'left')
                                                 ->where('schedules.course_id', $courseId)
                                                 ->findAll();
        }

        return $course;
    }

    /**
     * Get all courses (active and inactive)
     */
    public function getAllCourses()
    {
        return $this->orderBy('title', 'ASC')->findAll();
    }
}
