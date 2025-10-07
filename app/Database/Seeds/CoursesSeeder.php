<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        // Sample course data
        $data = [
            [
                'course_code' => 'CS101',
                'title' => 'Introduction to Programming',
                'description' => 'Learn the fundamentals of programming using Python. This course covers variables, control structures, functions, and basic data structures.',
                'teacher_id' => 2,
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 month')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'WEB202',
                'title' => 'Web Technologies',
                'description' => 'Creating Website',
                'teacher_id' => 2,
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 weeks')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'DB301',
                'title' => 'Advance Database',
                'description' => 'Learn how to design efficient databases and write powerful SQL queries. Covers normalization, relationships, and advanced query techniques.',
                'teacher_id' => 2,
                'department' => 'Information Technology',
                'semester' => '2nd Semester',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 weeks')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'SYS401',
                'title' => 'System Analysis Design',
                'description' => 'Analysis for making a system',
                'teacher_id' => 3,
                'department' => 'Information Systems',
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'SYS402',
                'title' => 'System Integration',
                'description' => 'Integration of different systems',
                'teacher_id' => 3,
                'department' => 'Information Systems',
                'semester' => '2nd Semester',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // First, clear any existing courses to avoid conflicts
        $this->db->table('courses')->emptyTable();

        // Then insert the new data
        $this->db->table('courses')->insertBatch($data);
        
        echo "Added 5 sample courses.\n";
    }
}