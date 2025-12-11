<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_id', 'room_id', 'day_of_week', 'start_time', 
        'end_time', 'academic_year', 'semester'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'course_id' => 'required|is_not_unique[courses.id]',
        'day_of_week' => 'required|in_list[Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday]',
        'start_time' => 'required|valid_time',
        'end_time' => 'required|valid_time',
        'academic_year' => 'required|max_length[20]',
        'semester' => 'required|in_list[1st Semester,2nd Semester,Summer]'
    ];

    /**
     * Check if there's a scheduling conflict
     */
    public function hasConflict($roomId, $dayOfWeek, $startTime, $endTime, $academicYear, $semester, $excludeScheduleId = null)
    {
        $builder = $this->where('room_id', $roomId)
                        ->where('day_of_week', $dayOfWeek)
                        ->where('academic_year', $academicYear)
                        ->where('semester', $semester)
                        ->groupStart()
                            ->where('start_time <', $endTime)
                            ->where('end_time >', $startTime)
                        ->groupEnd();

        if ($excludeScheduleId) {
            $builder->where('id !=', $excludeScheduleId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get schedule with course and room details
     */
    public function getScheduleWithDetails($scheduleId)
    {
        return $this->select('schedules.*, courses.title as course_title, courses.course_code, 
                             rooms.room_number, rooms.building, users.name as teacher_name')
                    ->join('courses', 'courses.id = schedules.course_id')
                    ->join('rooms', 'rooms.id = schedules.room_id', 'left')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->where('schedules.id', $scheduleId)
                    ->first();
    }

    /**
     * Get student schedule
     */
    public function getStudentSchedule($userId, $academicYear, $semester)
    {
        return $this->select('schedules.*, courses.title as course_title, courses.course_code, 
                             rooms.room_number, rooms.building, users.name as teacher_name')
                    ->join('courses', 'courses.id = schedules.course_id')
                    ->join('enrollments', 'enrollments.course_id = courses.id')
                    ->join('rooms', 'rooms.id = schedules.room_id', 'left')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->where('enrollments.user_id', $userId)
                    ->where('schedules.academic_year', $academicYear)
                    ->where('schedules.semester', $semester)
                    ->orderBy('FIELD(schedules.day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")', '', false)
                    ->orderBy('schedules.start_time', 'ASC')
                    ->findAll();
    }

    /**
     * Get teacher schedule
     */
    public function getTeacherSchedule($teacherId, $academicYear, $semester)
    {
        return $this->select('schedules.*, courses.title as course_title, courses.course_code, 
                             rooms.room_number, rooms.building')
                    ->join('courses', 'courses.id = schedules.course_id')
                    ->join('rooms', 'rooms.id = schedules.room_id', 'left')
                    ->where('courses.teacher_id', $teacherId)
                    ->where('schedules.academic_year', $academicYear)
                    ->where('schedules.semester', $semester)
                    ->orderBy('FIELD(schedules.day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")', '', false)
                    ->orderBy('schedules.start_time', 'ASC')
                    ->findAll();
    }

    /**
     * Check if student has schedule conflict before enrolling
     */
    public function hasStudentConflict($userId, $courseId, $academicYear, $semester)
    {
        $newSchedules = $this->where('course_id', $courseId)
                             ->where('academic_year', $academicYear)
                             ->where('semester', $semester)
                             ->findAll();

        if (empty($newSchedules)) {
            return false; // No schedule yet for this course
        }

        $studentSchedules = $this->getStudentSchedule($userId, $academicYear, $semester);

        foreach ($newSchedules as $newSched) {
            foreach ($studentSchedules as $existingSched) {
                if ($newSched['day_of_week'] === $existingSched['day_of_week']) {
                    // Check time overlap
                    if ($newSched['start_time'] < $existingSched['end_time'] && 
                        $newSched['end_time'] > $existingSched['start_time']) {
                        return true; // Conflict found
                    }
                }
            }
        }

        return false;
    }
}
