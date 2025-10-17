<?php


namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id',
        'file_name',
        'file_path',
        'created_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function insertMaterial($data)
    {
        try {
            return $this->insert($data, false);
        } catch (\Exception $e) {
            log_message('error', 'Material insert error: ' . $e->getMessage());
            return false;
        }
    }

    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)->findAll();
    }
}