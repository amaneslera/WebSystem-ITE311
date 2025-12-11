<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseDetailsFields extends Migration
{
    public function up()
    {
        // Add missing fields to courses table
        $fields = [
            'units' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => false,
                'default'    => 3,
                'comment'    => 'Credit units',
                'after'      => 'description'
            ],
            'year_level' => [
                'type'       => 'ENUM',
                'constraint' => ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'],
                'null'       => true,
                'after'      => 'units'
            ],
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Link to program if course is program-specific',
                'after'      => 'year_level'
            ],
            'prerequisite_course_ids' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON array of prerequisite course IDs',
                'after'   => 'program_id'
            ],
            'lecture_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,1',
                'null'       => true,
                'comment'    => 'Lecture hours per week',
                'after'      => 'prerequisite_course_ids'
            ],
            'lab_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,1',
                'null'       => true,
                'comment'    => 'Laboratory hours per week',
                'after'      => 'lecture_hours'
            ],
        ];
        
        $this->forge->addColumn('courses', $fields);
        
        // Add foreign key for program_id
        $this->forge->processIndexes('courses');
        $this->db->query('ALTER TABLE courses ADD CONSTRAINT fk_courses_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE courses DROP FOREIGN KEY fk_courses_program');
        
        // Remove columns
        $this->forge->dropColumn('courses', [
            'units',
            'year_level', 
            'program_id',
            'prerequisite_course_ids',
            'lecture_hours',
            'lab_hours'
        ]);
    }
}
