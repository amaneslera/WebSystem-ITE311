<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Sample Admin
        $this->db->table('users')->insert([
            'username'   => 'admin',
            'email'      => 'admin@lms.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'first_name' => 'System',
            'last_name'  => 'Administrator',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Sample Instructors
        $instructors = [
            [
                'username'   => 'john_instructor',
                'email'      => 'john@lms.com',
                'password'   => password_hash('instructor123', PASSWORD_DEFAULT),
                'role'       => 'instructor',
                'first_name' => 'John',
                'last_name'  => 'Smith',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'jane_instructor',
                'email'      => 'jane@lms.com',
                'password'   => password_hash('instructor123', PASSWORD_DEFAULT),
                'role'       => 'instructor',
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($instructors);

        // Sample Students
        $students = [
            [
                'username'   => 'student1',
                'email'      => 'student1@lms.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'first_name' => 'Alice',
                'last_name'  => 'Johnson',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'student2',
                'email'      => 'student2@lms.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'first_name' => 'Bob',
                'last_name'  => 'Wilson',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'student3',
                'email'      => 'student3@lms.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'first_name' => 'Carol',
                'last_name'  => 'Brown',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($students);
    }
}
