<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table            = 'academic_years';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'year_code', 'start_date', 'end_date', 'current_semester',
        'is_current', 'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'year_code' => 'required|max_length[20]|is_unique[academic_years.year_code,id,{id}]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'current_semester' => 'permit_empty|in_list[1st Semester,2nd Semester,Summer]',
        'status' => 'permit_empty|in_list[upcoming,active,completed]'
    ];

    public function getCurrentAcademicYear()
    {
        return $this->where('is_current', 1)->first();
    }

    public function setCurrentAcademicYear($yearId)
    {
        // First, unset all current flags
        $this->update(null, ['is_current' => 0]);
        
        // Then set the new current year
        return $this->update($yearId, ['is_current' => 1, 'status' => 'active']);
    }

    public function getActiveYears()
    {
        return $this->where('status', 'active')
                    ->orWhere('status', 'upcoming')
                    ->orderBy('start_date', 'DESC')
                    ->findAll();
    }
}
