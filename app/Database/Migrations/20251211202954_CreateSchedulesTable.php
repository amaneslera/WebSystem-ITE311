<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'room_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'day_of_week' => [
                'type'       => 'ENUM',
                'constraint' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'null'       => false,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'academic_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'e.g., 2024-2025',
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['1st Semester', '2nd Semester', 'Summer'],
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('room_id', 'rooms', 'id', 'SET NULL', 'CASCADE');
        // Composite key to prevent scheduling conflicts
        $this->forge->addKey(['room_id', 'day_of_week', 'start_time', 'academic_year', 'semester'], false, true);
        $this->forge->createTable('schedules', true);
    }

    public function down()
    {
        $this->forge->dropTable('schedules', true);
    }
}
