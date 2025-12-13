<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'program_code' => 'BSCS',
                'program_name' => 'Bachelor of Science in Computer Science',
                'department_id' => 1,
                'degree_type' => 'Bachelor',
                'total_units' => 120,
                'duration_years' => 4,
                'status' => 'active'
            ],
            [
                'program_code' => 'BSIT',
                'program_name' => 'Bachelor of Science in Information Technology',
                'department_id' => 1,
                'degree_type' => 'Bachelor',
                'total_units' => 120,
                'duration_years' => 4,
                'status' => 'active'
            ],
            [
                'program_code' => 'BSIS',
                'program_name' => 'Bachelor of Science in Information Systems',
                'department_id' => 1,
                'degree_type' => 'Bachelor',
                'total_units' => 120,
                'duration_years' => 4,
                'status' => 'active'
            ],
            [
                'program_code' => 'BSCpE',
                'program_name' => 'Bachelor of Science in Computer Engineering',
                'department_id' => 1,
                'degree_type' => 'Bachelor',
                'total_units' => 124,
                'duration_years' => 4,
                'status' => 'active'
            ],
            [
                'program_code' => 'BSSE',
                'program_name' => 'Bachelor of Science in Software Engineering',
                'department_id' => 1,
                'degree_type' => 'Bachelor',
                'total_units' => 120,
                'duration_years' => 4,
                'status' => 'active'
            ]
        ];

        // Insert data
        $this->db->table('programs')->insertBatch($data);
    }
}
