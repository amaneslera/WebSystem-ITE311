<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Sample Admin
        $this->db->table('users')->insert([
            'username'    => 'admin',
            'email'       => 'admin@lms.com',
            'password'    => password_hash('admin123', PASSWORD_DEFAULT),
            'role'        => 'admin',
            'first_name'  => 'System',
            'last_name'   => 'Administrator',
            'employee_id' => 'ADM2024001',
            'department'  => 'Administration',
            'phone'       => '09123456789',
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Sample Instructor
        $this->db->table('users')->insert([
            'username'    => 'jim_instructor',
            'email'       => 'jim@lms.com',
            'password'    => password_hash('jim123', PASSWORD_DEFAULT),
            'role'        => 'instructor',
            'first_name'  => 'Jim',
            'last_name'   => 'Jamero',
            'employee_id' => 'INST2024001',
            'department'  => 'Information Technology',
            'phone'       => '09234567890',
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Sample Students
        $students = [
            [
                'username'    => '2311600016',
                'email'       => 'aman.eslera@student.lms.com',
                'password'    => password_hash('Draine010101', PASSWORD_DEFAULT),
                'role'        => 'student',
                'first_name'  => 'Aman',
                'last_name'   => 'Eslera',
                'student_id'  => '2311600016',
                'program'     => 'BSIT',
                'department'  => 'Information Technology',
                'year_level'  => '3rd Year',
                'phone'       => '09345678901',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'username'    => '2311600113',
                'email'       => 'crystalherda@student.lms.com',
                'password'    => password_hash('fooxft1', PASSWORD_DEFAULT),
                'role'        => 'student',
                'first_name'  => 'Crystal',
                'last_name'   => 'Herda',
                'student_id'  => '2311600113',
                'program'     => 'BSCS',
                'department'  => 'Computer Science',
                'year_level'  => '3rd Year',
                'phone'       => '09456789012',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'username'    => 'student3',
                'email'       => 'hezekiah@student.lms.com',
                'password'    => password_hash('student123', PASSWORD_DEFAULT),
                'role'        => 'student',
                'first_name'  => 'Hezekiah',
                'last_name'   => 'Bernasor',
                'student_id'  => '2311600200',
                'program'     => 'BSIT',
                'department'  => 'Information Technology',
                'year_level'  => '2nd Year',
                'phone'       => '09567890123',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($students);
    }
}
