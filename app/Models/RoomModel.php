<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table            = 'rooms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'room_number', 'building', 'capacity', 'type', 
        'equipment', 'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'room_number' => 'required|max_length[50]',
        'capacity' => 'required|integer|greater_than[0]',
        'type' => 'required|in_list[lecture,laboratory,seminar,auditorium,other]',
        'status' => 'permit_empty|in_list[available,maintenance,unavailable]'
    ];

    public function getAvailableRooms()
    {
        return $this->where('status', 'available')->findAll();
    }

    public function checkRoomAvailability($roomId, $dayOfWeek, $startTime, $endTime, $academicYear, $semester)
    {
        $scheduleModel = new \App\Models\ScheduleModel();
        return !$scheduleModel->hasConflict($roomId, $dayOfWeek, $startTime, $endTime, $academicYear, $semester);
    }

    public function getRoomsByType($type)
    {
        return $this->where('type', $type)
                    ->where('status', 'available')
                    ->findAll();
    }
}
