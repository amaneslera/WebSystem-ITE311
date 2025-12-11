<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseCapacityFields extends Migration
{
    public function up()
    {
        $fields = [
            'max_students' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Maximum students (null = unlimited)',
                'after'      => 'teacher_id'
            ],
            'current_enrolled' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Current enrolled count',
                'after'      => 'max_students'
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'archived'],
                'default'    => 'active',
                'null'       => false,
                'comment'    => 'Course status',
                'after'      => 'current_enrolled'
            ]
        ];
        
        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['max_students', 'current_enrolled', 'status']);
    }
}
