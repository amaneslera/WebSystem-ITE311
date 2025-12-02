<?php


namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'course_id'  => 1, // CS101 - Introduction to Programming
                'file_name'  => 'sample1.pdf',
                'file_path'  => 'uploads/materials/sample1.pdf',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_id'  => 2, // WEB202 - Web Technologies
                'file_name'  => 'sample2.docx',
                'file_path'  => 'uploads/materials/sample2.docx',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data
        $this->db->table('materials')->insertBatch($data);
    }
}