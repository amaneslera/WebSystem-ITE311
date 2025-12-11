<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table            = 'programs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_code', 'program_name', 'department_id', 'degree_type',
        'total_units', 'duration_years', 'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'program_code' => 'required|max_length[20]|is_unique[programs.program_code,id,{id}]',
        'program_name' => 'required|max_length[255]',
        'department_id' => 'required|is_not_unique[departments.id]',
        'degree_type' => 'required|in_list[Bachelor,Master,Doctorate,Certificate,Diploma]',
        'duration_years' => 'permit_empty|integer|greater_than[0]',
        'status' => 'permit_empty|in_list[active,inactive]'
    ];

    public function getActivePrograms()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getProgramWithDepartment($programId)
    {
        return $this->select('programs.*, departments.name as department_name, departments.code as department_code')
                    ->join('departments', 'departments.id = programs.department_id')
                    ->where('programs.id', $programId)
                    ->first();
    }

    public function getProgramsByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId)
                    ->where('status', 'active')
                    ->findAll();
    }

    public function getStudentCount($programId)
    {
        return $this->db->table('users')
                        ->where('program_id', $programId)
                        ->where('role', 'student')
                        ->where('status', 'active')
                        ->countAllResults();
    }
}
