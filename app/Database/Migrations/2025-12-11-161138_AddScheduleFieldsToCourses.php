<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScheduleFieldsToCourses extends Migration
{
    public function up()
    {
        $fields = [
            'room' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Room number or location',
                'after' => 'units'
            ],
            'schedule_days' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Days of the week (e.g., MWF, TTH)',
                'after' => 'room'
            ],
            'schedule_time' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Time schedule (e.g., 8:00-9:30 AM)',
                'after' => 'schedule_days'
            ],
        ];
        
        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['room', 'schedule_days', 'schedule_time']);
    }
}
