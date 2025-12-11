<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table            = 'departments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'name', 'description', 'head_id', 'status'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'code' => 'required|max_length[20]|is_unique[departments.code,id,{id}]',
        'name' => 'required|max_length[255]',
        'status' => 'permit_empty|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'code' => [
            'required' => 'Department code is required',
            'is_unique' => 'Department code already exists'
        ],
        'name' => [
            'required' => 'Department name is required'
        ]
    ];

    public function getActiveDepartments()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getDepartmentWithHead($departmentId)
    {
        return $this->select('departments.*, users.name as head_name')
                    ->join('users', 'users.id = departments.head_id', 'left')
                    ->where('departments.id', $departmentId)
                    ->first();
    }
}
