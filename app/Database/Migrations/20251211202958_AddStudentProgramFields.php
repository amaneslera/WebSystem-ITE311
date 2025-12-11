<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentProgramFields extends Migration
{
    public function up()
    {
        // Add program and year level to users table for students
        $fields = [
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Student program enrollment',
                'after'      => 'role'
            ],
            'year_level' => [
                'type'       => 'ENUM',
                'constraint' => ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'],
                'null'       => true,
                'comment'    => 'Current year level for students',
                'after'      => 'program_id'
            ],
            'student_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'unique'     => true,
                'comment'    => 'Student ID number',
                'after'      => 'year_level'
            ],
        ];
        
        $this->forge->addColumn('users', $fields);
        
        // Add foreign key
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_program');
        
        // Remove columns
        $this->forge->dropColumn('users', ['program_id', 'year_level', 'student_id']);
    }
}
