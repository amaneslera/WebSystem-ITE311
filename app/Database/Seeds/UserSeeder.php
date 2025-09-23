<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        $this->db->table('users')->insert([
            'name'       => 'Draine Gray',
            'email'      => 'draine@gmail.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Teacher users
        $teachers = [
            [
                'name'       => 'Jim Jamero',
                'email'      => 'jim@lms.com',
                'password'   => password_hash('jim123', PASSWORD_DEFAULT),
                'role'       => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Mary Dream',
                'email'      => 'maria@lms.com',
                'password'   => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'       => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('users')->insertBatch($teachers);

        // Student users
        $students = [
            [
                'name'       => 'Aman Eslera',
                'email'      => 'aman.eslera@gmail.com',
                'password'   => password_hash('Draine010101', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Crystal Herda',
                'email'      => 'crystalherda@gmail.com',
                'password'   => password_hash('fooxft1', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Hezekiah Bernasor',
                'email'      => 'hezekiah@gmail.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($students);
    }
}